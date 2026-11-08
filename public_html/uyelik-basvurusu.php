<?php
require __DIR__ . '/_app.php';

layout_start([
    'footer_cta' => false, // sayfada zaten katılım çağrısı/form var
    'title' => 'WBN Türkiye | Yeni Üye Başvuru Formu',
    'description' => 'WBN Türkiye topluluğuna katılmak için yeni üye başvuru formunu doldurun; başvurunuz değerlendirildikten sonra e-posta ile bilgilendirilirsiniz.',
    'css' => ['pages/member-form'],
    'js' => ['forms', 'pages/member-form'],
    'active' => 'uyelik-basvurusu',
]);
?>
<section class="page-hero">
  <div class="container hero-grid">
    <div>
      <img class="brand-logo" src="<?= e(asset('img/wbn-logo-white.png')) ?>" alt="WBN Türkiye logosu" width="188" height="94">
      <div class="eyebrow">WBN Türkiye</div>
      <h1>Yeni Üye Başvuru Formu</h1>
      <p class="hero-copy">
        WBN Türkiye topluluğuna katılım başvurunuzu iletmek için iletişim ve profesyonel bilgilerinizi,
        uzmanlık alanlarınızı ve topluluk içinde yer almak istediğiniz çalışma alanlarını paylaşabilirsiniz.
      </p>
    </div>
    <aside class="hero-note">
      <strong>Başvurunuzu tamamlayın</strong>
      <p>Zorunlu alanları eksiksiz doldurun. Başvurunuz WBN Türkiye ekibine ayrıntılı e-posta olarak iletilir.</p>
      <ol class="hero-steps">
        <li>Formu eksiksiz doldurun</li>
        <li>Ekibimiz başvurunuzu değerlendirir</li>
        <li>Sonuç e-posta ile size iletilir</li>
      </ol>
    </aside>
  </div>
</section>

<main id="main" tabindex="-1" class="form-section">
  <div class="container form-shell">
    <aside class="form-nav" aria-label="Form bölümleri">
      <small>Form kapsamı</small>
      <div class="progress"><span></span></div>
      <div class="nav-list">
        <span><b>1</b> Kişisel & İletişim Bilgileri</span>
        <span><b>2</b> Profesyonel Bilgiler</span>
        <span><b>3</b> İlgi & Katılım Alanları</span>
        <span><b>4</b> KVKK & Ek Bilgi</span>
      </div>
    </aside>

    <section class="form-card">
      <div class="form-heading">
        <h2>WBN Türkiye'ye başvurun</h2>
        <p>Zorunlu alanlar yıldız (*) ile işaretlenmiştir.</p>
      </div>

      <form id="wbnNewMemberForm" data-api="/api/applications">
        <div class="hp-field" aria-hidden="true">
          <label>Bu alanı boş bırakın <input type="text" name="_hp" tabindex="-1" autocomplete="off"></label>
        </div>

        <div class="section-block">
          <div class="section-title"><span>01</span> Kişisel & İletişim Bilgileri</div>
          <div class="form-grid">
            <div class="field">
              <label class="required" for="adSoyad">Ad Soyad</label>
              <input id="adSoyad" name="full_name" type="text" autocomplete="name" required>
            </div>

            <div class="field">
              <label class="required" for="telefon">Cep Telefonu</label>
              <input id="telefon" name="phone" type="tel" autocomplete="tel" required>
            </div>

            <div class="field full">
              <label class="required" for="email">E-posta Adresi</label>
              <input id="email" name="email" type="email" autocomplete="email" required>
            </div>

            <div class="field full">
              <label class="required" for="referans">Kimin referansı ile katılmaktasınız?</label>
              <input id="referans" name="reference" type="text" required>
            </div>
          </div>

          <div class="kvkk-box">
            <label class="choice">
              <input type="checkbox" name="kvkk" value="Onaylandı" required>
              <span><strong><a href="/kvkk" target="_blank" class="kvkk-link">KVKK ve Gizlilik</a> Onayı:</strong> Bilgilerimin WBN Türkiye üyelik ve iletişim süreçleri kapsamında işlenmesini kabul ediyorum.</span>
            </label>
          </div>
        </div>

        <div class="section-block">
          <div class="section-title"><span>02</span> Profesyonel Bilgiler</div>
          <div class="form-grid">
            <div class="field">
              <label class="required" for="kurum">Çalıştığınız Kurum / Şirket</label>
              <input id="kurum" name="company" type="text" autocomplete="organization" required>
            </div>

            <div class="field">
              <label class="required" for="unvan">Ünvanınız</label>
              <input id="unvan" name="title" type="text" autocomplete="organization-title" required>
            </div>

            <div class="field full">
              <label class="required" for="sektor">Çalıştığınız sektör</label>
              <select id="sektor" name="sector" required>
                <option value="" selected disabled>Seçiniz</option>
                <?php foreach (SECTOR_OPTIONS as $option): ?>
                <option><?= e($option) ?></option>
                <?php endforeach; ?>
              </select>
              <input id="sektorDiger" name="other_sector" type="text"
                     placeholder="Diğer seçeneğini işaretlediyseniz yazınız"
                     style="display:none; margin-top:12px;">
            </div>

            <div class="field full">
              <label class="required" for="linkedin">LinkedIn Profiliniz</label>
              <input id="linkedin" name="linkedin" type="url" placeholder="https://www.linkedin.com/in/..." required>
            </div>
          </div>
        </div>

        <div class="section-block">
          <div class="section-title"><span>03</span> Uzmanlık / İlgi Alanları</div>
          <div class="field full">
            <label>8. Uzmanlık / İlgi Alanlarınız</label>
            <div class="choice-grid">
              <?php foreach (EXPERTISE_OPTIONS as $option): ?>
              <label class="choice"><input<?= $option === 'Diğer' ? ' id="uzmanlikDigerCheck"' : '' ?> type="checkbox" name="expertise[]" value="<?= e($option) ?>"><span><?= e($option) ?></span></label>
              <?php endforeach; ?>
            </div>
            <input id="uzmanlikDiger" name="other_expertise" type="text"
                   placeholder="Diğer seçeneğini işaretlediyseniz yazınız"
                   style="display:none; margin-top:12px;">
          </div>
        </div>

        <div class="section-block">
          <div class="section-title"><span>04</span> WBN Katılım Bilgileri</div>

          <div class="field full">
            <label>10. WBN içinde aktif olarak yer almak istediğiniz alanlar</label>
            <div class="choice-grid">
              <?php foreach (ACTIVE_AREA_OPTIONS as $option): ?>
              <label class="choice"><input type="checkbox" name="active_areas[]" value="<?= e($option) ?>"><span><?= e($option) ?></span></label>
              <?php endforeach; ?>
              <label class="choice">
                <input id="activeOtherCheck" type="checkbox" value="Diğer">
                <span>Diğer</span>
              </label>
            </div>
            <input id="activeOtherText" name="other_active_area" type="text" placeholder="Diğer seçeneğini işaretlediyseniz yazınız..." style="margin-top:12px;">
          </div>
        </div>

        <div class="section-block">
          <div class="section-title"><span>05</span> KVKK & Ek Bilgi</div>

          <div class="field full" style="margin-bottom:22px;">
            <label>11. Bilgilerinizde değişiklik olduğunda WBN veri tabanını güncel tutabilmek için bize bilgi vermeyi kabul ediyor musunuz? (KVKK)</label>
            <div class="choice-grid">
              <label class="choice">
                <input type="checkbox" name="database_update_consent" value="Evet">
                <span>Evet</span>
              </label>
            </div>
          </div>

          <div class="field full">
            <label for="ekBilgi">12. Eklemek veya bizimle paylaşmak istediğiniz başka bir bilgi var mı?</label>
            <textarea id="ekBilgi" name="extra_info" placeholder="İsteğe bağlı"></textarea>
          </div>

        </div>

        <div class="submit-area">
          <button class="submit-button" type="submit">Başvurumu Gönder</button>
          <p class="form-note">
            Gönderdiğiniz bilgiler WBN Türkiye ekibine iletilir ve yalnızca üyelik süreçleri kapsamında kullanılır.
          </p>
        </div>
      </form>
    </section>
  </div>
</main>
<?php layout_end(); ?>
