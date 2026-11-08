<?php
require __DIR__ . '/_app.php';

layout_start([
    'title' => 'WBN Türkiye | İş Birliklerimiz',
    'description' => 'WBN Türkiye’nin iş birliği yaptığı kurum ve firmalar; kadın liderliğine değer katan çözüm ortaklarımız.',
    'css' => ['pages/content'],
    'active' => 'is-birlikleri',
]);
?>
<main id="main" tabindex="-1">
  <section class="about-hero">
    <div class="container">
      <div class="hero-copy">
        <div class="eyebrow">WBN Türkiye</div>
        <h1>İş<span>Birliklerimiz</span></h1>
        <p class="hero-lead">
          WBN Türkiye topluluğunu güçlendiren, kadın liderliğine değer katan iş birliklerimiz.
        </p>
      </div>
    </div>
  </section>

  <section class="partners-section">
    <div class="container">
      <p class="section-kicker">Çözüm Ortaklarımız</p>
      <?php partner_logos(); ?>
    </div>
  </section>
</main>
<?php layout_end(); ?>
