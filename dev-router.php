<?php
// Yalnızca yerel geliştirme içindir: php -S localhost:8000 -t public_html dev-router.php
// PHP'nin yerleşik sunucusu .htaccess okumaz; buradaki kurallar onu taklit eder.
// Sunucuya yüklenmez.

const PUBLIC_ROOT = __DIR__ . '/public_html';

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = PUBLIC_ROOT . $path;

if ($path === '/dev-router.php' || $path === '/_app.php') {
    http_response_code(403);
    exit;
}
if (str_starts_with($path, '/api')) {
    require PUBLIC_ROOT . '/api/index.php';
    return true;
}
if ($path !== '/' && is_file($file) && !str_ends_with($file, '.php')) {
    return false; // statik dosya
}
if (is_dir($file) && is_file(rtrim($file, '/') . '/index.php')) {
    if (!str_ends_with($path, '/')) {
        header('Location: ' . $path . '/', true, 301);
        return true;
    }
    chdir(rtrim($file, '/'));
    require rtrim($file, '/') . '/index.php';
    return true;
}
if (is_file($file) && str_ends_with($file, '.php')) {
    chdir(dirname($file));
    require $file;
    return true;
}
$candidate = PUBLIC_ROOT . rtrim($path, '/') . '.php';
if (preg_match('#^/[a-z0-9-]+/?$#', $path) && is_file($candidate)) {
    require $candidate;
    return true;
}
require PUBLIC_ROOT . '/404.php';
return true;
