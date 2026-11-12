<?php
require __DIR__ . '/_app.php';

layout_start([
  'title' => 'WBN Türkiye | Yaklaşan Etkinlikler',
  'description' => 'WBN CONNECT | FUTURE IN ACTION: 10 Aralık 2026, Mutfak Sanatları Akademisi Maslak İstanbul. Yapay zekâ, sürdürülebilirlik ve ortak deneyim etrafında buluşuyoruz.',
  'css' => ['pages/etkinlik'],
  'active' => 'yaklasan-etkinlikler',
]);

$registerUrl = 'https://forms.gle/DjUq5YQgLLmweens6';
?>
<main id="main" class="event-page" tabindex="-1">
  <div class="container event-list">
    <!-- Yeni etkinlik kartlarını listenin başına ekleyin. -->
    <article class="event-card">
      <div class="event-intro">
        <h2 class="event-title">WBN CONNECT | <span>FUTURE IN ACTION</span></h2>
        <p class="event-subtitle">Yapay Zekâ ve Sürdürülebilirlik</p>
        <a class="event-register" href="<?= e($registerUrl) ?>" target="_blank" rel="noopener noreferrer">Kayıt Ol</a>
      </div>

      <div class="event-details">
        <p class="event-date">
          <time datetime="2026-12-10">10 Aralık 2026</time> | <time datetime="16:00">16.00</time>–<time
            datetime="20:00">20.00</time>
        </p>
        <p class="event-location">Mutfak Sanatları Akademisi (MSA) – Kampüs Maslak İstanbul</p>

        <h3 class="event-program-title">Program</h3>
        <ul class="event-program">
          <li class="event-session">
            <span class="event-session-time">16.00–16.45</span>
            <div>
              <h4 class="event-session-title">Yapay Zekâ ile Geleceği Anlamak</h4>
              <p class="event-speaker">Dr. Erinç Albey · Özyeğin Üniversitesi Yapay Zekâ ve Veri Mühendisliği Bölümü
                Öğretim Üyesi</p>
            </div>
          </li>
          <li class="event-session">
            <span class="event-session-time">17.00–19.30</span>
            <div>
              <h4 class="event-session-title">Sürdürülebilir Mutfak Atölyesi</h4>
            </div>
          </li>
          <li class="event-session">
            <span class="event-session-time">19.30–20.00</span>
            <div>
              <h4 class="event-session-title">Networking – Lezzetleri Birlikte Keşfediyoruz</h4>
            </div>
          </li>
        </ul>
        <p class="event-partners">WBN Türkiye | MetropolCard İş Birliği</p>
      </div>
    </article>
  </div>
</main>
<?php layout_end(); ?>