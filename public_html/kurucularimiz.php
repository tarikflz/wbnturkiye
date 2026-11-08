<?php
require __DIR__ . '/_app.php';

layout_start([
    'title' => 'WBN Türkiye | Kurucularımız',
    'description' => 'WBN Türkiye kurucuları Olcay Çat ve Tülin Çakmak’ın topluluğun doğuşunu anlattığı hikâye.',
    'css' => ['pages/kurucular'],
    'active' => 'kurucularimiz',
]);
?>
<main>
  <section class="founders-hero">
    <div class="container">
      <div class="founders-eyebrow">WBN Türkiye</div>
      <h1 class="founders-title">Kurucularımız</h1>
    </div>
  </section>

  <section class="founders-story">
    <div class="container founders-layout">
      <div class="founders-copy">
        <p>
          2000’lerin başında bilişim dünyasında kesişen yollarımız, yıllar içinde güçlü bir dostluğa dönüştü. Biz, Olcay ve Tülin, farklı uzmanlık alanlarımız ve ortak değerlerimizle insanları bir araya getiren bağların, birlikte daha fazlasını başarmanın anahtarı olduğuna inandık.
        </p>

        <div class="founders-statement">
          İki kurucu, bir ortak hayal. Sınırları aşan kadın dayanışması.
        </div>

        <p>
          Nisan 2023'te finans ve teknoloji dünyasındaki kadınlar arasında güçlü bağlantılar kurmak amacıyla çıktığımız yolculuk, bugün farklı sektörlerden kadınların deneyimlerini paylaştığı, birbirini desteklediği ve yeni iş birlikleri geliştirdiği bir topluluğa dönüştü.
        </p>

        <p>
          Bizim için WBN, yalnızca kadınları bir araya getiren bir ağ değil; kurulan her bağın yeni bir fırsata, paylaşılan her deneyimin yeni bir fikre, dayanışmanın ise daha güçlü bir geleceğe dönüşebileceği ortak bir yolculuk.
        </p>

        <p>
          Bu yolculuğun bir başka anlamı daha var: Bugünün kadınları arasında kurulan güçlü bağların, yarının kadınlarına yeni yollar açması. TEV iş birliğiyle yürüttüğümüz WBN Türkiye Geleceğin Arıları Burs Fonu da bu inancımızın en değerli parçalarından biri.
        </p>

        <div class="founders-closing">
          <p>
            Biz bu hikâyeyi iki kişi başlattık. Bugün onu birlikte büyüten çok daha fazla kadınız.
          </p>

          <p class="founders-slogan">
            Güçlü Kadınlar. Güçlü Bağlar. Güçlü Gelecek.
          </p>

          <div class="founders-signature">
            <strong>Olcay Çat &amp; Tülin Çakmak</strong>
            <span>WBN Türkiye Kurucu Ortakları</span>
          </div>
        </div>
      </div>

      <aside class="founders-photo-column">
        <div class="founders-photo-shell">
          <img class="founders-photo" src="<?= e(asset('img/ekip/olcay-tulin.jpeg')) ?>" alt="Olcay Çat ve Tülin Çakmak" width="1066" height="1600">
        </div>
      </aside>
    </div>
  </section>
</main>
<?php layout_end(); ?>
