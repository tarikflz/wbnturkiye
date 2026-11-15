<?php
// Tüm /api/* istekleri buraya yönlendirilir (.htaccess). Rotalar aşağıda; ayrıntılar README.md içinde.
require dirname(__DIR__) . '/_app.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = '/' . trim(substr((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen('/api')), '/');

/** Admin oturumu ve (değişiklik yapan isteklerde) CSRF kontrolü. */
function require_admin(bool $write = false): void
{
    if (!current_admin()) {
        throw new HttpError(401, 'Oturum açmanız gerekiyor.');
    }
    if ($write && !csrf_valid($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
        throw new HttpError(403, 'Güvenlik doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.');
    }
}

function admin_list(callable $lister): array
{
    require_admin();
    return $lister();
}

try {
    // Yükleme limiti aşıldığında PHP gövdeyi boş bırakır
    if ($method === 'POST' && empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0
        && !str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        throw new HttpError(413, 'Gönderilen dosya çok büyük.');
    }

    $result = match (true) {
        // Public formlar
        $method === 'POST' && $path === '/applications' => handle_application(request_body()),
        $method === 'POST' && $path === '/member-updates' => handle_member_update(request_body()),
        $method === 'POST' && $path === '/corporate-applications' => handle_corporate_application(request_body()),
        $method === 'POST' && $path === '/receipts' => handle_receipt(request_body(), $_FILES['receipt'] ?? null),
        $method === 'POST' && $path === '/danismanlik/signup' => handle_nodyo_signup(request_body()),
        $method === 'POST' && $path === '/danismanlik/continue' => handle_nodyo_continue(request_body()),
        $method === 'POST' && $path === '/contact' => handle_contact(request_body()),

        // Admin: listeler
        $method === 'GET' && $path === '/applications' => admin_list('list_applications'),
        $method === 'GET' && $path === '/member-updates' => admin_list('list_member_updates'),
        $method === 'GET' && $path === '/corporate-applications' => admin_list('list_corporate_applications'),
        $method === 'GET' && $path === '/receipts' => admin_list('list_receipts'),
        $method === 'GET' && $path === '/mail-templates' => admin_list('list_mail_templates'),
        $method === 'GET' && $path === '/nodyo-members' => admin_list('list_nodyo_members'),

        default => null,
    };

    if ($result === null) {
        // Parametreli admin rotaları
        if ($method === 'GET' && preg_match('#^/receipts/([A-Z0-9-]+)/file$#', $path, $m)) {
            require_admin();
            stream_receipt_file($m[1], isset($_GET['download']));
        }
        if ($method === 'PUT' && preg_match('#^/mail-templates/([a-z_]+)$#', $path, $m)) {
            require_admin(true);
            $result = save_mail_template($m[1], request_body());
        }
        if ($method === 'DELETE' && preg_match('#^/(applications|member-updates|corporate-applications|receipts|nodyo-members)/([A-Z0-9-]+)$#', $path, $m)) {
            require_admin(true);
            $result = soft_delete_record($m[1], $m[2]);
        }
        if ($method === 'POST' && preg_match('#^/(applications|corporate-applications)/([A-Z0-9-]+)/(approve|reject)$#', $path, $m)) {
            require_admin(true);
            $result = decide_application(
                $m[1] === 'corporate-applications' ? 'corporate' : 'individual',
                $m[2],
                $m[3] === 'approve' ? 'approved' : 'rejected',
                (string) (request_body()['email_html'] ?? '')
            );
        }
    }

    if ($result === null) {
        throw new HttpError(404, 'Bulunamadı.');
    }
    json_response($result);
} catch (HttpError $e) {
    json_response(['ok' => false, 'message' => $e->getMessage(), 'errors' => $e->errors], $e->getCode());
} catch (Throwable $e) {
    error_log('API hatası ' . $method . ' ' . $path . ': ' . $e);
    json_response(['ok' => false, 'message' => 'Beklenmeyen bir hata oluştu. Lütfen daha sonra tekrar deneyin.'], 500);
}
