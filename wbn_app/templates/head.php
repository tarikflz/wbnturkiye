<?php
$page = $GLOBALS['page'];
$fonts = 'family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700';
if (in_array('cormorant', $page['fonts'] ?? [], true)) {
    $fonts = 'family=Cormorant+Garamond:ital,wght@1,600;1,700&' . $fonts;
}
$path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
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
  <link rel="canonical" href="<?= e(url($path)) ?>">
<?php endif; ?>
  <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">
  <link rel="icon" type="image/png" sizes="512x512" href="<?= e(asset('img/favicon.png')) ?>">
  <link rel="apple-touch-icon" href="<?= e(asset('img/favicon.png')) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?<?= $fonts ?>&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= e(asset('css/base.css')) ?>">
<?php foreach ($page['css'] ?? [] as $css): ?>
  <link rel="stylesheet" href="<?= e(asset("css/$css.css")) ?>">
<?php endforeach; ?>
</head>

<body>
