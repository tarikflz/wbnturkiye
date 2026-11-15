<?php
declare(strict_types=1);

// Yönetim panelindeki onay/red e-posta şablonları. Panel her alanın HTML'ini bir dizi olarak gönderir;
// ad yerine {{name}} yer tutucusu kullanılır. Anahtarlar index.php'deki data-template-key değerleridir.
const MAIL_TEMPLATE_KEYS = ['wbn_individual_approve', 'wbn_individual_reject', 'wbn_corporate_approve', 'wbn_corporate_reject'];

/** @return array<string, string[]> anahtar => alan HTML'leri */
function list_mail_templates(): array
{
    $out = [];
    foreach (db()->query('SELECT template_key, content FROM mail_templates')->fetchAll() as $r) {
        $values = json_decode($r['content'], true);
        if (is_array($values)) {
            $out[$r['template_key']] = $values;
        }
    }
    return $out;
}

function save_mail_template(string $key, array $input): array
{
    if (!in_array($key, MAIL_TEMPLATE_KEYS, true)) {
        throw new HttpError(404, 'Şablon bulunamadı.');
    }
    $values = $input['values'] ?? null;
    if (!is_array($values) || !array_is_list($values) || count($values) > 20) {
        throw new HttpError(422, 'Geçersiz şablon verisi.');
    }
    $clean = [];
    foreach ($values as $v) {
        if (!is_string($v) || strlen($v) > 20000) {
            throw new HttpError(422, 'Geçersiz şablon verisi.');
        }
        // Yöneticiden yöneticiye HTML aktarıldığı için tehlikeli etiket/öznitelikleri at.
        $v = preg_replace('#<\s*(script|style|iframe|object|embed|template)\b.*?(</\s*\1\s*>|$)#is', '', $v) ?? '';
        $v = preg_replace('#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $v) ?? '';
        $v = preg_replace('#(href|src)\s*=\s*(["\']?)\s*javascript:#i', '$1=$2#', $v) ?? '';
        $clean[] = $v;
    }

    db()->prepare('INSERT INTO mail_templates (template_key, content, updated_by) VALUES (?, ?, ?)
                   ON DUPLICATE KEY UPDATE content = VALUES(content), updated_by = VALUES(updated_by)')
        ->execute([$key, json_encode($clean, JSON_UNESCAPED_UNICODE), current_admin()]);
    return ['ok' => true, 'key' => $key];
}
