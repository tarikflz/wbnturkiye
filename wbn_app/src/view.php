<?php
declare(strict_types=1);

// Menü ağacı: [anahtar => [href, etiket, alt öğeler]]. href null ise öğe tıklanamaz.
// Alt öğeler [anahtar => [href, etiket]] biçimindedir; href null olanlar henüz sayfası olmayan başlıklardır.
const NAV_ITEMS = [
    'hakkimizda' => ['/hakkimizda', 'Hakkımızda', [
        'kurucularimiz' => [null, 'Kurucularımız'],
        'icra-kurulu' => [null, 'İcra Kurulumuz'],
    ]],
    'uyelik' => [null, 'Üyelik', [
        'kimler-katilabilir' => ['/kimler-katilabilir', 'Kimler Katılabilir?'],
        'uyelik-basvurusu' => ['/uyelik-basvurusu', 'Yeni Üye Başvurusu'],
        'kurumsal-uyelik' => ['/kurumsal-uyelik', 'Kurumsal Üyelik Başvurusu'],
        'uye-bilgi-guncelleme' => ['/uye-bilgi-guncelleme', 'Üye Bilgi Güncelleme Formu'],
    ]],
    'etkinlikler' => [null, 'Etkinlikler', [
        'yaklasan-etkinlikler' => [null, 'Yaklaşan Etkinlikler'],
        'gecmis-etkinlikler' => [null, 'Geçmiş Etkinlikler'],
    ]],
    'is-birlikleri' => [null, 'İş Birliklerimiz', [
        'wbn-ayricaliklari' => [null, 'WBN Ayrıcalıkları'],
    ]],
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
