<?php
declare(strict_types=1);

const NAV_ITEMS = [
    'hakkimizda' => ['/hakkimizda', 'Hakkımızda'],
    'kimler-katilabilir' => ['/kimler-katilabilir', 'Kimler Katılabilir'],
    'kurumsal-uyelik' => ['/kurumsal-uyelik', 'Kurumsal Üyelik Başvurusu'],
    'uyelik-basvurusu' => ['/uyelik-basvurusu', 'Yeni Üye Başvurusu'],
    'uye-bilgi-guncelleme' => ['/uye-bilgi-guncelleme', 'Üye Bilgi Güncelleme Formu'],
];

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    return rtrim((string) config('app.base_url'), '/') . $path;
}

// Önbelleği dosya değiştiğinde kırmak için ?v=<değişiklik zamanı> ekler.
function asset(string $path): string
{
    $file = PUBLIC_DIR . '/assets/' . $path;
    $version = is_file($file) ? filemtime($file) : 0;
    return '/assets/' . $path . '?v=' . $version;
}

/**
 * Sayfa başlangıcı: <head>, ortak site header'ı.
 * $page anahtarları: title, description, css[], js[], active, fonts[], noindex, path
 */
function layout_start(array $page): void
{
    $GLOBALS['page'] = $page;
    require TEMPLATES_DIR . '/head.php';
    require TEMPLATES_DIR . '/header.php';
}

function layout_end(): void
{
    $page = $GLOBALS['page'] ?? [];
    require TEMPLATES_DIR . '/footer.php';
}
