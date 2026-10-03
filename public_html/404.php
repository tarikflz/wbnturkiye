<?php
require __DIR__ . '/_app.php';

http_response_code(404);
layout_start([
    'title' => 'WBN Türkiye | Sayfa Bulunamadı',
    'noindex' => true,
]);
?>
<main class="not-found">
  <div class="container">
    <p class="not-found__code">404</p>
    <h1>Aradığınız sayfa bulunamadı.</h1>
    <p>Sayfa taşınmış ya da kaldırılmış olabilir.</p>
    <a class="not-found__button" href="/">Ana Sayfaya Dön</a>
  </div>
</main>
<?php layout_end(); ?>
