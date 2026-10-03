<?php
require __DIR__ . '/_app.php';

layout_start([
    'title' => 'WBN Türkiye | Yeni Üye Başvuru Formu',
    'description' => 'WBN Türkiye Yeni Üye Başvuru Formu',
    'css' => ['pages/member-form'],
    'js' => ['forms', 'pages/member-form'],
    'active' => 'uyelik-basvurusu',
]);
?>
<section class="page-hero">
  <div class="container hero-grid">
    <div>
      <img class="brand-logo" src="data:image/png;base64,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" alt="WBN Türkiye logosu">
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
    </aside>
  </div>
</section>

<main class="form-section">
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
