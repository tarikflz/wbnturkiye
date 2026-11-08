<?php
require __DIR__ . '/_app.php';

layout_start([
    'footer_cta' => false, // sayfada zaten katılım çağrısı/form var
    'title' => 'WBN Türkiye | Güçlü Kadınlar, Güçlü Gelecek',
    'description' => 'WBN Türkiye; kadın liderleri ortak değerler, mentorluk, ilham ve etki etrafında buluşturan seçkin bir topluluktur.',
    'css' => ['pages/index'],
    'js' => ['forms'],
    'active' => 'home',
    'fonts' => ['cormorant'],
    'schema' => [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'WBN Türkiye',
        'url' => url('/'),
        'logo' => url('/assets/img/wbn-logo.png'),
        'description' => 'Türkiye\'nin finans ve teknoloji başta olmak üzere tüm sektörlerden kadın liderleri buluşturan etkileşim platformu.',
        'sameAs' => [
            'https://www.linkedin.com/company/wbn-turkiye/',
            'https://www.instagram.com/wbuzznetwork/',
        ],
    ],
]);
?>
<main id="main" tabindex="-1">
  <section class="hero" id="anasayfa">
    <div class="container">
      <div class="hero-content">
        <div class="eyebrow">WBN Türkiye</div>

        <h1>
          <span>Güçlü Kadınlar.</span>
          <span>Güçlü Bağlar.</span>
          <span>Güçlü Gelecek.</span>
        </h1>

        <p class="hero-subtitle">
          <span>Türkiye'nin kadın liderlerini ortak değerler</span>
          <span>etrafında buluşturan seçkin bir topluluk.</span>
        </p>

        <div class="community-action">
          <a href="/uyelik-basvurusu" class="community-button">
            Topluluğa Katılın
          </a>

          <div class="community-values">
            Liderlik. Dayanışma. Sosyal Etki.
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="keyword-marquee" aria-label="WBN Türkiye odak alanları">
    <div class="keyword-marquee__track">
      <div class="keyword-marquee__group">
        <span>Finans</span><i>•</i>
        <span>Bankacılık</span><i>•</i>
        <span>Sigortacılık</span><i>•</i>
        <span>Ödeme Sistemleri</span><i>•</i>
        <span>Fintek</span><i>•</i>
        <span>Teknoloji</span><i>•</i>
        <span>Yapay Zekâ</span><i>•</i>
        <span>Telekomünikasyon</span><i>•</i>
        <span>Perakende</span><i>•</i>
        <span>E-Ticaret</span><i>•</i>
        <span>Hızlı - Dayanıklı Tüketim</span><i>•</i>
        <span>Üretim</span><i>•</i>
        <span>Sanayi</span><i>•</i>
        <span>Enerji</span><i>•</i>
        <span>Sağlık</span><i>•</i>
        <span>İlaç</span><i>•</i>
        <span>Otomotiv</span><i>•</i>
        <span>Lojistik</span><i>•</i>
        <span>Havacılık</span><i>•</i>
        <span>Turizm</span><i>•</i>
        <span>Eğitim</span><i>•</i>
        <span>Akademi</span><i>•</i>
        <span>Medya</span><i>•</i>
        <span>İletişim</span><i>•</i>
        <span>Hukuk</span><i>•</i>
        <span>Danışmanlık</span><i>•</i>
        <span>Girişimcilik</span><i>•</i>
        <span>Sivil Toplum</span><i>•</i>
        <span>Networking</span><i>•</i>
        <span>Liderlik</span><i>•</i>
        <span>Mentorluk</span><i>•</i>
        <span>Eğitim</span><i>•</i>
        <span>İş Birlikleri</span><i>•</i>
        <span>Sosyal Etki</span><i>•</i>
        <span>TEV Geleceğin Arıları Burs Fonu</span><i>•</i>
        <span>Staj Fırsatları</span><i>•</i>
        <span>Kadın Liderler</span><i>•</i>
      </div>

      <div class="keyword-marquee__group" aria-hidden="true">
        <span>Finans</span><i>•</i>
        <span>Bankacılık</span><i>•</i>
        <span>Sigortacılık</span><i>•</i>
        <span>Ödeme Sistemleri</span><i>•</i>
        <span>Fintek</span><i>•</i>
        <span>Teknoloji</span><i>•</i>
        <span>Yapay Zekâ</span><i>•</i>
        <span>Telekomünikasyon</span><i>•</i>
        <span>Perakende</span><i>•</i>
        <span>E-Ticaret</span><i>•</i>
        <span>Hızlı - Dayanıklı Tüketim</span><i>•</i>
        <span>Üretim</span><i>•</i>
        <span>Sanayi</span><i>•</i>
        <span>Enerji</span><i>•</i>
        <span>Sağlık</span><i>•</i>
        <span>İlaç</span><i>•</i>
        <span>Otomotiv</span><i>•</i>
        <span>Lojistik</span><i>•</i>
        <span>Havacılık</span><i>•</i>
        <span>Turizm</span><i>•</i>
        <span>Eğitim</span><i>•</i>
        <span>Akademi</span><i>•</i>
        <span>Medya</span><i>•</i>
        <span>İletişim</span><i>•</i>
        <span>Hukuk</span><i>•</i>
        <span>Danışmanlık</span><i>•</i>
        <span>Girişimcilik</span><i>•</i>
        <span>Sivil Toplum</span><i>•</i>
        <span>Networking</span><i>•</i>
        <span>Liderlik</span><i>•</i>
        <span>Mentorluk</span><i>•</i>
        <span>Eğitim</span><i>•</i>
        <span>İş Birlikleri</span><i>•</i>
        <span>Sosyal Etki</span><i>•</i>
        <span>TEV Geleceğin Arıları Burs Fonu</span><i>•</i>
        <span>Staj Fırsatları</span><i>•</i>
        <span>Kadın Liderler</span><i>•</i>
      </div>
    </div>
  </section>

  <section class="statement-section">
    <div class="container">
      <div class="statement-card">
        <p>
          Kadınların birbirini güçlendirdiği, genç kadınların eğitimle desteklendiği
          ve liderliğin çoğaldığı bir gelecek için birlikte çalışıyoruz.
        </p>
      </div>
    </div>
  </section>

  <section class="contact-section" id="topluluk-formu">
    <div class="container contact-grid">
      <div class="contact-intro">
        <p class="section-kicker">Topluluğa Katılın</p>
        <p class="contact-lead">
          Güçlü bağlar kurmak, deneyimlerinizi paylaşmak ve geleceğin kadın liderlerine
          birlikte değer katmak için sizi de WBN Türkiye topluluğuna bekliyoruz.
        </p>

        <img class="contact-logo" src="/assets/img/wbn-logo.png" alt="WBN Türkiye logosu" />
      </div>

      <div class="contact-card">
        <form id="contactForm" data-api="/api/contact">
          <div class="hp-field" aria-hidden="true">
            <label>Bu alanı boş bırakın <input type="text" name="_hp" tabindex="-1" autocomplete="off"></label>
          </div>

          <div class="form-grid">
            <div class="field">
              <label for="adSoyad">Adı Soyadı</label>
              <input id="adSoyad" name="full_name" type="text" autocomplete="name" required />
            </div>

            <div class="field">
              <label for="email">E-Mail Adresi</label>
              <input id="email" name="email" type="email" autocomplete="email" required />
            </div>

            <div class="field">
              <label for="telefon">Telefon</label>
              <input id="telefon" name="phone" type="tel" autocomplete="tel" required />
            </div>

            <div class="field">
              <label for="firma">Firma Adı</label>
              <input id="firma" name="company" type="text" autocomplete="organization" />
            </div>

            <div class="field full">
              <label for="unvan">Ünvan</label>
              <input id="unvan" name="title" type="text" autocomplete="organization-title" />
            </div>

            <div class="field full">
              <label for="mesaj">Mesaj</label>
              <textarea id="mesaj" name="message" required></textarea>
            </div>
          </div>

          <button class="submit-button" type="submit">Gönder</button>
          <p class="form-note">
            Formu göndererek iletişim amacıyla sizinle bağlantı kurulmasını kabul etmiş olursunuz.
          </p>
        </form>
      </div>
    </div>
  </section>
</main>
<?php layout_end(); ?>
