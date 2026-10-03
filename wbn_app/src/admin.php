<?php
declare(strict_types=1);

// Admin paneli veri işlemleri. Alan adları Yönetim Paneli'nin beklediği biçimdedir;
// panelde görünen "id" değeri tablodaki ref alanıdır (ör. WBN-2026-001).

function iso_date(?string $value): ?string
{
    return $value ? (new DateTime($value))->format(DATE_ATOM) : null;
}

function decode_list(?string $json): array
{
    $list = json_decode((string) $json, true);
    return is_array($list) ? $list : [];
}

function list_applications(): array
{
    $rows = db()->query('SELECT * FROM applications ORDER BY created_at DESC, id DESC')->fetchAll();
    return array_map(fn ($r) => [
        'id' => $r['ref'],
        'created_at' => iso_date($r['created_at']),
        'full_name' => $r['full_name'],
        'phone' => $r['phone'],
        'email' => $r['email'],
        'reference' => $r['reference'],
        'kvkk' => $r['kvkk'] ? 'Onaylandı' : '-',
        'company' => $r['company'],
        'title' => $r['title'],
        'sector' => $r['sector'],
        'other_sector' => $r['other_sector'],
        'expertise' => decode_list($r['expertise']),
        'other_expertise' => $r['other_expertise'],
        'linkedin' => $r['linkedin'],
        'active_areas' => decode_list($r['active_areas']),
        'other_active_area' => $r['other_active_area'],
        'database_update_consent' => $r['database_update_consent'] ?: 'Hayır',
        'extra_info' => (string) $r['extra_info'],
        'status' => $r['status'],
        'email_status' => $r['email_status'],
    ], $rows);
}

function list_member_updates(): array
{
    $rows = db()->query('SELECT * FROM member_updates ORDER BY updated_at DESC, id DESC')->fetchAll();
    return array_map(fn ($r) => [
        'id' => $r['ref'],
        'updated_at' => iso_date($r['updated_at']),
        'full_name' => $r['full_name'],
        'phone' => $r['phone'],
        'email' => $r['email'],
        'kvkk' => $r['kvkk'] ? 'Onaylandı' : '-',
        'company' => $r['company'],
        'title' => $r['title'],
        'sector' => $r['sector'],
        'other_sector' => $r['other_sector'],
        'expertise' => decode_list($r['expertise']),
        'other_expertise' => $r['other_expertise'],
        'linkedin' => $r['linkedin'],
        'attended_events' => $r['attended_events'] ?: '-',
        'active_areas' => decode_list($r['active_areas']),
        'other_active_area' => $r['other_active_area'],
        'database_update_consent' => $r['database_update_consent'] ?: 'Hayır',
        'extra_info' => (string) $r['extra_info'],
    ], $rows);
}

function list_corporate_applications(): array
{
    $rows = db()->query('SELECT * FROM corporate_applications ORDER BY created_at DESC, id DESC')->fetchAll();
    return array_map(fn ($r) => [
        'id' => $r['ref'],
        'created_at' => iso_date($r['created_at']),
        'company_title' => $r['company_title'],
        'registry_no' => $r['registry_no'],
        'website' => $r['website'],
        'applicant_name' => $r['applicant_name'],
        'applicant_title' => $r['applicant_title'],
        'phone' => $r['phone'],
        'email' => $r['email'],
        'status' => $r['status'],
        'email_status' => $r['email_status'],
    ], $rows);
}

function list_receipts(): array
{
    $rows = db()->query('SELECT * FROM receipts ORDER BY submitted_at DESC, id DESC')->fetchAll();
    return array_map(fn ($r) => [
        'id' => $r['ref'],
        'submitted_at' => iso_date($r['submitted_at']),
        'full_name' => $r['full_name'],
        'company_name' => $r['company_name'],
        'email' => $r['email'],
        'membership_status' => $r['membership_status'],
        'membership_type' => $r['membership_type'] ?: '-',
        'receipt_type' => $r['membership_type'] === 'Kurumsal' ? 'corporate' : 'individual',
        'receipt_name' => $r['receipt_name'],
        'receipt_mime' => $r['receipt_mime'],
        'receipt_url' => '/api/receipts/' . rawurlencode($r['ref']) . '/file',
    ], $rows);
}

function stream_receipt_file(string $ref, bool $download): never
{
    $stmt = db()->prepare('SELECT receipt_name, receipt_mime, stored_name FROM receipts WHERE ref = ?');
    $stmt->execute([$ref]);
    $r = $stmt->fetch();
    $path = $r ? STORAGE_DIR . '/receipts/' . $r['stored_name'] : null;
    if (!$path || !is_file($path)) {
        throw new HttpError(404, 'Dekont bulunamadı.');
    }

    $name = str_replace(['"', "\r", "\n"], '', $r['receipt_name']);
    header('Content-Type: ' . $r['receipt_mime']);
    header('Content-Length: ' . filesize($path));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    header(sprintf(
        'Content-Disposition: %s; filename="%s"; filename*=UTF-8\'\'%s',
        $download ? 'attachment' : 'inline',
        preg_replace('/[^\x20-\x7E]/', '_', $name),
        rawurlencode($name)
    ));
    readfile($path);
    exit;
}

/**
 * Başvuruyu onaylar/reddeder ve başvuru sahibine e-posta gönderir.
 * $type: 'individual' | 'corporate', $decision: 'approved' | 'rejected'
 */
function decide_application(string $type, string $ref, string $decision, string $emailHtml): array
{
    $table = $type === 'corporate' ? 'corporate_applications' : 'applications';
    $stmt = db()->prepare("SELECT * FROM $table WHERE ref = ?");
    $stmt->execute([$ref]);
    $app = $stmt->fetch();
    if (!$app) {
        throw new HttpError(404, 'Başvuru bulunamadı.');
    }

    // Karar bir kez verilir; yalnızca e-posta hatası olduysa aynı karar tekrar gönderilebilir.
    $retry = $app['status'] === $decision && $app['email_status'] === 'error';
    if ($app['status'] !== 'pending' && !$retry) {
        throw new HttpError(409, 'Bu başvuru için daha önce karar verilmiş.');
    }

    $email = $app['email'];
    $name = $type === 'corporate' ? $app['applicant_name'] : $app['full_name'];
    $body = sanitize_mail_html($emailHtml) ?: default_decision_html($type, $decision, $name);
    $label = $type === 'corporate' ? 'Kurumsal Üyelik Başvurunuz' : 'Yeni Üye Başvurunuz';
    $subject = 'WBN Türkiye | ' . $label . ($decision === 'approved' ? ' Onaylandı' : ' Hakkında');

    $sent = send_mail([$email], $subject, mail_layout($body));

    db()->prepare("UPDATE $table SET status = ?, email_status = ?, decided_at = NOW(), decided_by = ? WHERE id = ?")
        ->execute([$decision, $sent ? 'sent' : 'error', current_admin(), $app['id']]);

    return ['id' => $ref, 'status' => $decision, 'email_status' => $sent ? 'sent' : 'error'];
}

function default_decision_html(string $type, string $decision, string $name): string
{
    $label = $type === 'corporate' ? 'Kurumsal Üyelik Başvurunuz' : 'Yeni Üye Başvurunuz';
    if ($decision === 'rejected') {
        return '<p><strong>Merhaba ' . e($name) . ',</strong></p>'
            . "<p>WBN Türkiye $label değerlendirilmiş ve başvurunuz onaylanmamıştır.</p>"
            . "<p>WBN Türkiye'ye göstermiş olduğunuz ilgi ve başvurunuz için teşekkür ederiz.</p>";
    }
    return sanitize_mail_html(
        '<p><strong>Merhaba ' . e($name) . ',</strong></p>'
        . "<p>WBN Türkiye $label değerlendirilmiş ve başvurunuz onaylanmıştır.</p>"
        . '<p>Topluluğumuza hoş geldiniz. Üyelik sürecinizi tamamlamak için üyelik bağışınızı TEV “WBN Türkiye Geleceğin Arıları” burs fonuna gerçekleştirebilirsiniz.</p>'
        . '<a class="donation-preview" href="' . e(config('links.tev_donation')) . '">Bağış Yap</a>'
        . '<p class="receipt-mail-note">Bağış yaptıktan sonra; Bağış Dekontunu <span class="placeholder-link">....</span> linki tıklayarak yüklemenizi ve göndermenizi rica ederiz.</p>'
    );
}

/**
 * Admin panelinde düzenlenen e-posta HTML'ini temizler:
 * yalnızca basit etiketler kalır, linkler http(s) olmalı, "Bağış Yap" butonu ve
 * dekont linki (/dekont-gonder) e-postaya uygun biçimde yerleştirilir.
 */
function sanitize_mail_html(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $root = $doc->getElementById('root');
    if (!$root) {
        return '';
    }

    $allowed = ['p', 'strong', 'b', 'em', 'i', 'u', 'br', 'a', 'span', 'ul', 'ol', 'li'];
    $receiptLink = url('/dekont-gonder');

    $walk = function (DOMNode $node) use (&$walk, $allowed, $doc, $receiptLink): void {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMComment) {
                $node->removeChild($child);
                continue;
            }
            if (!$child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            $class = $child->getAttribute('class');

            if ($tag === 'span' && str_contains($class, 'placeholder-link')) {
                $a = $doc->createElement('a', 'Dekont Gönderme Formu');
                $a->setAttribute('href', $receiptLink);
                $a->setAttribute('style', 'color:#632389;font-weight:bold');
                $node->replaceChild($a, $child);
                continue;
            }
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'template'], true)) {
                $node->removeChild($child);
                continue;
            }
            if (!in_array($tag, $allowed, true)) {
                // İzin verilmeyen etiketi kaldır, içindeki metni koru
                $walk($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            $href = $tag === 'a' ? $child->getAttribute('href') : '';
            foreach (iterator_to_array($child->attributes) as $attr) {
                $child->removeAttribute($attr->name);
            }
            if ($tag === 'a') {
                if (!is_http_url($href)) {
                    $href = '#';
                }
                $child->setAttribute('href', $href);
                $child->setAttribute('target', '_blank');
                if (str_contains($class, 'donation-preview')) {
                    $child->setAttribute('style', 'display:inline-block;margin:6px 0 14px;padding:12px 26px;border-radius:999px;background:#632389;color:#ffffff;font-weight:bold;text-decoration:none');
                } else {
                    $child->setAttribute('style', 'color:#632389');
                }
            }
            $walk($child);
        }
    };
    $walk($root);

    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    return trim($out);
}
