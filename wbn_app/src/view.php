<?php
declare(strict_types=1);

// Menü ağacı: [anahtar => [href, etiket, alt öğeler]]. href null ise öğe tıklanamaz.
// Alt öğeler [anahtar => [href, etiket]] biçimindedir; href null olanlar henüz sayfası olmayan başlıklardır.
const NAV_ITEMS = [
    'hakkimizda' => ['/hakkimizda', 'Hakkımızda', [
        'kurucularimiz' => ['/kurucularimiz', 'Kurucularımız'],
        'icra-kurulu' => ['/icra-kurulumuz', 'İcra Kurulumuz'],
    ]],
    'uyelik' => [null, 'Üyelik', [
        'kimler-katilabilir' => ['/kimler-katilabilir', 'Kimler Katılabilir?'],
        'uyelik-basvurusu' => ['/uyelik-basvurusu', 'Yeni Üye Başvurusu'],
        'kurumsal-uyelik' => ['/kurumsal-uyelik', 'Kurumsal Üyelik Başvurusu'],
        'uye-bilgi-guncelleme' => ['/uye-bilgi-guncelleme', 'Üye Bilgi Güncelleme Formu'],
    ]],
    'etkinlikler' => [null, 'Etkinlikler', [
        'yaklasan-etkinlikler' => ['/yaklasan-etkinlikler', 'Yaklaşan Etkinlikler'],
        'gecmis-etkinlikler' => [null, 'Geçmiş Etkinlikler'],
    ]],
    'is-birlikleri' => ['/is-birliklerimiz', 'İş Birliklerimiz', [
        'wbn-ayricaliklari' => [null, 'WBN Ayrıcalıkları'],
    ]],
];

// İş birliği yapılan firmalar, soldan sağa sıra önemlidir: [ad, web sitesi, logo dosyası (assets/img/)]
const PARTNERS = [
    ['Inspira7', 'https://inspira7.com/', '1_inspira7.png'],
    ['Odarama', 'https://odarama.com/', '2_odarama.png'],
    ['Nodyo', 'https://nodyo.co/', '3_nodyo.png'],
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

// İş birliği logolarını link olarak basar. Logo dosyası yoksa firma adı yazı olarak görünür.
function partner_logos(): void
{
    echo '<ul class="partners-list">';
    foreach (PARTNERS as [$name, $link, $logo]) {
        echo '<li><a class="partner-link" href="' . e($link) . '" target="_blank" rel="noopener noreferrer" aria-label="' . e($name) . '">';
        if (is_file(PUBLIC_DIR . '/assets/img/' . $logo)) {
            echo '<img src="' . e(asset('img/' . $logo)) . '" alt="' . e($name) . ' logosu" loading="lazy">';
        } else {
            echo '<span>' . e($name) . '</span>';
        }
        echo '</a></li>';
    }
    echo '</ul>';
}

// İcra kurulu üye kartı. $m: [ad, unvan (boş olabilir), LinkedIn adresi, fotoğraf dosyası (assets/img/ekip/), ek sınıf]
function leader_card(array $m): void
{
    [$name, $title, $linkedin, $photo, $class] = $m + [4 => ''];
    echo '<article class="leader-card' . ($class !== '' ? ' ' . e($class) : '') . '">';
    echo '<div class="leader-photo-wrap"><img class="leader-photo" src="' . e(asset('img/ekip/' . $photo)) . '" alt="' . e($name) . '" width="640" height="640" loading="lazy"></div>';
    echo '<div class="leader-content"><h2 class="leader-name">' . e($name) . '</h2>';
    if ($title !== '') {
        echo '<p class="leader-title">' . e($title) . '</p>';
    }
    echo '<div class="leader-footer' . ($title === '' ? ' leader-footer--bare' : '') . '">';
    echo '<a class="linkedin-link" href="' . e($linkedin) . '" target="_blank" rel="noopener noreferrer" aria-label="' . e($name) . ' LinkedIn profili">';
    echo '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.87 6 2.49 6A2.5 2.5 0 1 1 4.98 3.5ZM.36 8.04h4.26V21H.36V8.04ZM7.34 8.04h4.08v1.77h.06c.57-1.08 1.96-2.22 4.03-2.22 4.31 0 5.11 2.84 5.11 6.53V21h-4.25v-6.1c0-1.46-.03-3.33-2.03-3.33-2.03 0-2.34 1.58-2.34 3.22V21H7.34V8.04Z"/></svg>';
    echo '</a></div></div></article>';
}
