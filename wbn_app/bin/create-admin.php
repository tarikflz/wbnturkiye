<?php
// Admin kullanıcısı oluşturmak için SQL üretir.
// Kullanım (kendi bilgisayarınızda): php wbn_app/bin/create-admin.php kullaniciadi
// Çıkan INSERT satırını cPanel > phpMyAdmin > SQL sekmesinde çalıştırın.
// Sunucuda SSH varsa --apply ile doğrudan veritabanına yazar.

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$username = $argv[1] ?? '';
if (!preg_match('/^[a-zA-Z0-9._-]{3,60}$/', $username)) {
    fwrite(STDERR, "Kullanım: php create-admin.php <kullaniciadi> [--apply]\n(3-60 karakter: harf, rakam, . _ -)\n");
    exit(1);
}

function ask_hidden(string $prompt): string
{
    fwrite(STDERR, $prompt);
    system('stty -echo');
    $value = rtrim((string) fgets(STDIN), "\r\n");
    system('stty echo');
    fwrite(STDERR, "\n");
    return $value;
}

$password = ask_hidden('Şifre (en az 12 karakter): ');
if (mb_strlen($password) < 12) {
    fwrite(STDERR, "Şifre en az 12 karakter olmalı.\n");
    exit(1);
}
if (ask_hidden('Şifre (tekrar): ') !== $password) {
    fwrite(STDERR, "Şifreler eşleşmiyor.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

if (in_array('--apply', $argv, true)) {
    define('PUBLIC_DIR', dirname(__DIR__, 2) . '/public_html');
    require dirname(__DIR__) . '/bootstrap.php';
    db()->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)')
        ->execute([$username, $hash]);
    fwrite(STDERR, "Kullanıcı kaydedildi: $username\n");
    exit(0);
}

printf(
    "INSERT INTO admin_users (username, password_hash) VALUES ('%s', '%s') ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash);\n",
    $username,
    $hash
);
