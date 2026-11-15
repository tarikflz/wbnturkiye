<?php
require __DIR__ . '/_app.php';

layout_start([
    'footer_cta' => false,
    'title' => 'WBN Türkiye | Uzman Danışmanlığı',
    'description' => 'WBN Türkiye üyeleri için uzman danışmanlığı: girişimcilik, pazarlama, teknoloji, liderlik ve kariyer alanlarında uzmanlarla birebir video görüşme.',
    'css' => ['pages/member-form'],
    'js' => ['forms'],
    'active' => 'danismanlik',
    'noindex' => !login_enabled(), // buton kapalıyken arama motorlarına da kapalı
]);
?>
<section class="page-hero">
  <div class="container hero-grid">
    <div>
      <img class="brand-logo" src="<?= e(asset('img/wbn-logo-white.png')) ?>" alt="WBN Türkiye logosu" width="188" height="94">
      <div class="eyebrow">WBN Türkiye</div>
      <h1>Uzman Danışmanlığı</h1>
      <p class="hero-copy">
        Girişimcilik, pazarlama, teknoloji, liderlik ve kariyer gibi alanlarda deneyimli uzmanlarla
        birebir video görüşme yapabileceğiniz bir danışmanlık ve deneyim paylaşım platformudur.
      </p>
    </div>
    <aside class="hero-note">
      <strong>Nasıl çalışır?</strong>
      <p>İhtiyacınızı yazın, size uygun uzmanı seçin, uzmanın takviminden randevu alıp video görüşme yapın. Üyelik ücretsizdir; görüşme ücretini her uzman kendisi belirler.</p>
    </aside>
  </div>
</section>

<main id="main" tabindex="-1" class="form-section">
  <div class="container form-shell" style="grid-template-columns:minmax(0,720px);justify-content:center">
    <section class="form-card">
      <div class="form-heading">
        <h2>Giriş yapın veya kayıt olun</h2>
        <p>E-posta adresinize tek kullanımlık bir giriş bağlantısı göndereceğiz. Daha önce kayıt olduysanız aynı adresi yazmanız yeterli.</p>
      </div>

      <form id="consultingForm" data-api="/api/danismanlik/signup">
        <div class="hp-field" aria-hidden="true">
          <label>Bu alanı boş bırakın <input type="text" name="_hp" tabindex="-1" autocomplete="off"></label>
        </div>
        <div class="form-grid">
          <div class="field">
            <label class="required" for="consultingAd">Ad Soyad</label>
            <input id="consultingAd" name="full_name" type="text" autocomplete="name" minlength="2" maxlength="64" required>
          </div>
          <div class="field">
            <label class="required" for="consultingEmail">E-posta Adresi</label>
            <input id="consultingEmail" name="email" type="email" autocomplete="email" required>
          </div>
        </div>
        <div class="kvkk-box">
          <label class="choice">
            <input type="checkbox" name="kvkk" value="Onaylandı" required>
            <span><strong><a href="/kvkk" target="_blank" class="kvkk-link">KVKK ve Gizlilik</a> Onayı:</strong> Ad ve e-posta bilgilerimin hesabımın oluşturulması ve danışmanlık hizmetinin sunulması için hizmet sağlayıcı ile paylaşılmasını kabul ediyorum.</span>
          </label>
        </div>
        <button class="submit-button" type="submit">Giriş Yap</button>
      </form>
    </section>
  </div>
</main>
<?php layout_end(); ?>
