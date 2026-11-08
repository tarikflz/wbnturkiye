<?php
require __DIR__ . '/_app.php';

// Menüde linki yok; yalnızca İcra Kurulumuz sayfasındaki bağlantıdan erişilir.
layout_start([
    'title' => 'WBN Türkiye | İcra Kurulumuz 2025',
    'description' => 'WBN Türkiye İcra Kurulu 2025 üyeleri.',
    'css' => ['pages/ekip'],
    'active' => 'icra-kurulu',
]);

// [ad, unvan, LinkedIn, fotoğraf]
$members = [
    ['Aslı Ulusoy', '', 'https://www.linkedin.com/in/asli-ulusoy-a388036/', 'asli-ulusoy.jpg'],
    ['Gül Bilgin Mokan', '', 'https://www.linkedin.com/in/g%C3%BCl-bilgin-mokan-40b38114/', 'gul-bilgin-mokan.jpg'],
    ['Günışığı Geçgil', '', 'https://www.linkedin.com/in/gunisigigecgil/', 'gunisigi-gecgil.jpg'],
    ['Melis Yağlı', '', 'https://www.linkedin.com/in/melis-ya%C4%9Fl%C4%B1-4916651b/', 'melis-yagli.jpg'],
    ['Seda Birinci Karatekin', '', 'https://www.linkedin.com/in/sbkaratekin/', 'seda-birinci-karatekin.jpg'],
    ['Selin Ökten', '', 'https://www.linkedin.com/in/selin-okten-020807a7/', 'selin-okten.jpg'],
    ['Olcay Çat', '', 'https://www.linkedin.com/in/olcaycat/', 'olcay-cat.jpg'],
    ['Tulin Çakmak', '', 'https://www.linkedin.com/in/tulincakmak/', 'tulin-cakmak.jpg'],
];
?>
<main>
  <section class="board-hero" data-year="2025">
    <div class="container">
      <div class="board-eyebrow">WBN Türkiye</div>
      <h1 class="board-title">İcra Kurulu <span>2025</span></h1>
    </div>
  </section>

  <section class="leaders-section">
    <div class="container">
      <div class="leaders-grid leaders-grid--last-two">
<?php foreach ($members as $m) { leader_card($m); } ?>
      </div>
    </div>
  </section>
</main>
<?php layout_end(); ?>
