<?php
require __DIR__ . '/_app.php';

layout_start([
    'title' => 'WBN Türkiye | İcra Kurulumuz',
    'description' => 'WBN Türkiye İcra Kurulu 2026 üyeleri.',
    'css' => ['pages/ekip'],
    'active' => 'icra-kurulu',
]);

// [ad, unvan, LinkedIn, fotoğraf, ek sınıf] — sıra: eş başkanlar, üyeler, kurucular
$members = [
    ['Aslı Ulusoy', 'WBN İcra Kurulu Eş Başkanı - Nobelis Danışmanlık Kurucusu', 'https://www.linkedin.com/in/asli-ulusoy-a388036/', 'asli-ulusoy.jpg', 'leader-card--cochair'],
    ['Gül Bilgin Mokan', 'WBN İcra Kurulu Eş Başkanı - Multinet Chief Experience Officer', 'https://www.linkedin.com/in/g%C3%BCl-bilgin-mokan-40b38114/', 'gul-bilgin-mokan.jpg', 'leader-card--cochair'],
    ['Burcu Kıstak', 'Markamı Yönet Kurucu Ortağı', 'https://www.linkedin.com/in/burcu-kistak/', 'burcu-kistak.jpg'],
    ['Ebru Zeybek', 'Aidea Kurucu Ortağı', 'https://www.linkedin.com/in/ebruzeybek/', 'ebru-zeybek.jpg'],
    ['Nesrin İlker Peker', 'Danışman', 'https://www.linkedin.com/in/nesrin-ilker-peker-7971725/', 'nesrin-ilker-peker.jpg'],
    ['Olcay Çat', 'WBN Kurucu Ortağı - Ensight CEO\'su', 'https://www.linkedin.com/in/olcaycat/', 'olcay-cat.jpg', 'leader-card--founder'],
    ['Tulin Çakmak', 'WBN Kurucu Ortağı - NovaForm Danışmanlık Kurucusu', 'https://www.linkedin.com/in/tulincakmak/', 'tulin-cakmak.jpg', 'leader-card--founder'],
];
?>
<main>
  <section class="board-hero" data-year="2026">
    <div class="container">
      <div class="board-eyebrow">WBN Türkiye</div>
      <h1 class="board-title">İcra Kurulu</h1>
    </div>
  </section>

  <section class="leaders-section">
    <div class="container">
      <div class="leaders-grid">
<?php foreach ($members as $m) { leader_card($m); } ?>
      </div>
    </div>
  </section>

  <section class="previous-board">
    <div class="container">
      <a href="/icra-kurulumuz-2025">İcra Kurulumuz 2025</a>
    </div>
  </section>
</main>
<?php layout_end(); ?>
