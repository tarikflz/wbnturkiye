<?php
declare(strict_types=1);

const RECEIPT_MIME_TYPES = [
    'application/pdf' => 'pdf',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

/**
 * Yüklenen dekontu doğrular ve storage/receipts altına rastgele adla kaydeder.
 * @return array{original: string, mime: string, size: int, stored: string}
 */
function store_receipt_upload(?array $file): array
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new HttpError(422, 'Lütfen dekont dosyanızı seçin.', ['receipt' => 'Dekont dosyası zorunludur.']);
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
        || $file['size'] > config('uploads.max_bytes')) {
        $mb = (int) (config('uploads.max_bytes') / 1024 / 1024);
        throw new HttpError(422, "Dosya en fazla $mb MB olabilir.", ['receipt' => "Dosya en fazla $mb MB olabilir."]);
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        throw new HttpError(400, 'Dosya yüklenemedi. Lütfen tekrar deneyin.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: '';
    if (!isset(RECEIPT_MIME_TYPES[$mime])) {
        throw new HttpError(422, 'Yalnızca PDF, JPG, PNG veya WEBP dosyası yükleyebilirsiniz.', ['receipt' => 'Geçersiz dosya türü.']);
    }

    $dir = STORAGE_DIR . '/receipts/' . date('Y/m');
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
        throw new RuntimeException("Klasör oluşturulamadı: $dir");
    }
    $stored = date('Y/m') . '/' . bin2hex(random_bytes(16)) . '.' . RECEIPT_MIME_TYPES[$mime];
    if (!move_uploaded_file($file['tmp_name'], STORAGE_DIR . '/receipts/' . $stored)) {
        throw new RuntimeException('Dosya taşınamadı.');
    }

    $original = mb_substr(basename(str_replace('\\', '/', (string) $file['name'])), 0, 200);
    return ['original' => $original, 'mime' => $mime, 'size' => (int) $file['size'], 'stored' => $stored];
}
