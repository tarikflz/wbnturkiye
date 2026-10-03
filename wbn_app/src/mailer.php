<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

require_once APP_DIR . '/vendor/autoload.php';

/**
 * E-posta gönderir. Hata olursa false döner ve hatayı log'a yazar.
 * @param string[] $to
 * @param array<int, array{path: string, name: string}> $attachments
 */
function send_mail(array $to, string $subject, string $html, ?string $replyTo = null, array $attachments = []): bool
{
    if (config('mail.transport') === 'log') {
        $entry = sprintf(
            "==== %s\nTo: %s\nReply-To: %s\nSubject: %s\nAttachments: %s\n\n%s\n\n",
            date('c'),
            implode(', ', $to),
            $replyTo ?? config('mail.reply_to'),
            $subject,
            implode(', ', array_column($attachments, 'name')) ?: '-',
            $html
        );
        file_put_contents(STORAGE_DIR . '/logs/mail.log', $entry, FILE_APPEND | LOCK_EX);
        return true;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->Port = (int) config('mail.port');
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = config('mail.secure') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Timeout = 20;

        $mail->setFrom(config('mail.from_email'), config('mail.from_name'));
        foreach ($to as $address) {
            $mail->addAddress($address);
        }
        $mail->addReplyTo($replyTo ?? config('mail.reply_to'));
        foreach ($attachments as $file) {
            $mail->addAttachment($file['path'], $file['name']);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = trim(html_entity_decode(strip_tags(preg_replace('/<(br|\/p|\/tr|\/h\d)>/i', "\n", $html)), ENT_QUOTES, 'UTF-8'));
        $mail->send();
        return true;
    } catch (MailException $e) {
        error_log('Mail gönderilemedi (' . implode(', ', $to) . '): ' . $mail->ErrorInfo);
        return false;
    }
}

/** Ekiptekilere giden bildirim e-postası için basit HTML tablo. */
function mail_table(string $title, array $rows): string
{
    $html = '<div style="font-family:Arial,sans-serif;color:#24172A">'
        . '<h2 style="color:#3A1044;margin:0 0 16px">' . e($title) . '</h2>'
        . '<table cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;max-width:680px">';
    foreach ($rows as $label => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $html .= '<tr><th align="left" style="background:#F1DAFD;border:1px solid #E2B9F8;width:38%;vertical-align:top">'
            . e((string) $label) . '</th><td style="border:1px solid #E2B9F8">'
            . nl2br(e((string) ($value !== '' ? $value : '-'))) . '</td></tr>';
    }
    return $html . '</table></div>';
}

/** Başvuru sahibine giden e-postalar için WBN çerçevesi. */
function mail_layout(string $innerHtml): string
{
    return '<div style="background:#FFF9FF;padding:24px 0;font-family:Arial,sans-serif;color:#24172A">'
        . '<div style="max-width:620px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #F1DAFD">'
        . '<div style="background:#24052C;padding:22px;text-align:center">'
        . '<img src="' . e(url('/assets/img/wbn-logo.png')) . '" alt="WBN Türkiye" width="120" style="display:inline-block"></div>'
        . '<div style="padding:28px 30px;font-size:15px;line-height:1.6">' . $innerHtml . '</div>'
        . '<div style="padding:16px;text-align:center;font-size:12px;color:#6F5D76;background:#FFF9FF">WBN Türkiye · '
        . e(url('/')) . '</div></div></div>';
}
