<?php
declare(strict_types=1);

define('APP_DIR', __DIR__);
define('TEMPLATES_DIR', APP_DIR . '/templates');
define('STORAGE_DIR', APP_DIR . '/storage');

// WBN_CONFIG ortam değişkeni ile farklı bir config dosyası seçilebilir (Docker bunu kullanır).
$configFile = getenv('WBN_CONFIG') ?: APP_DIR . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('config.php bulunamadı. wbn_app/config.example.php dosyasını config.php olarak kopyalayın.');
}

$GLOBALS['config'] = require $configFile;

function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['config'];
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

date_default_timezone_set(config('app.timezone', 'Europe/Istanbul'));
mb_internal_encoding('UTF-8');

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_DIR . '/logs/php-error.log');
ini_set('display_errors', config('app.env') === 'production' ? '0' : '1');

foreach (glob(APP_DIR . '/src/*.php') as $file) {
    require_once $file;
}
