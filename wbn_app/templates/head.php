<?php
$page = $GLOBALS['page'];
$fonts = 'family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700';
if (in_array('cormorant', $page['fonts'] ?? [], true)) {
    $fonts = 'family=Cormorant+Garamond:ital,wght@1,600;1,700&' . $fonts;
}
$path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$canonical = url($path === '/' ? '/' : rtrim($path, '/'));
$ogImage = url('/assets/img/og-wbn.jpg');
?>
<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page['title'] ?? 'WBN Türkiye') ?></title>
<?php if (!empty($page['description'])): ?>
  <meta name="description" content="<?= e($page['description']) ?>">
<?php endif; ?>
<?php if (!empty($page['noindex'])): ?>
  <meta name="robots" content="noindex, nofollow">
<?php else: ?>
  <link rel="canonical" href="<?= e($canonical) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="WBN Türkiye">
  <meta property="og:locale" content="tr_TR">
  <meta property="og:title" content="<?= e($page['title'] ?? 'WBN Türkiye') ?>">
<?php if (!empty($page['description'])): ?>
  <meta property="og:description" content="<?= e($page['description']) ?>">
<?php endif; ?>
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="675">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($page['title'] ?? 'WBN Türkiye') ?>">
<?php if (!empty($page['description'])): ?>
  <meta name="twitter:description" content="<?= e($page['description']) ?>">
<?php endif; ?>
  <meta name="twitter:image" content="<?= e($ogImage) ?>">
<?php endif; ?>
  <meta name="theme-color" content="#17021D">
  <link rel="icon" type="image/png" sizes="48x48" href="<?= e(asset('img/favicon-48.png')) ?>">
  <link rel="icon" type="image/png" sizes="192x192" href="<?= e(asset('img/favicon-192.png')) ?>">
  <link rel="apple-touch-icon" href="<?= e(asset('img/apple-touch-icon.png')) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?<?= $fonts ?>&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= e(asset('css/base.css')) ?>">
<?php foreach ($page['css'] ?? [] as $css): ?>
  <link rel="stylesheet" href="<?= e(asset("css/$css.css")) ?>">
<?php endforeach; ?>
<?php if (!empty($page['schema'])): ?>
  <script type="application/ld+json"><?= json_encode($page['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endif; ?>
</head>

<body>
