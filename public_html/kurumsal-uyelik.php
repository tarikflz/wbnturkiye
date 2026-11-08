<?php
require __DIR__ . '/_app.php';

layout_start([
    'title' => 'WBN Türkiye | Kurumsal Üyelik Başvuru Formu',
    'description' => 'WBN Türkiye kurumsal üyelik başvuru formu: kurumunuzun kadın liderlerle birlikte topluluğumuzun bir parçası olması için başvurun.',
    'css' => ['pages/kurumsal-uyelik'],
    'js' => ['forms'],
    'active' => 'kurumsal-uyelik',
]);
?>
<section class="page-hero">
  <div class="container hero-grid">
    <div>
      <img class="brand-logo" src="<?= e(asset('img/wbn-logo-white.png')) ?>" alt="WBN Türkiye logosu" width="188" height="94">
      <div class="eyebrow">WBN Türkiye</div>
      <h1>Kurumsal Üyelik Başvuru Formu</h1>
      <p class="hero-copy">
        WBN Türkiye kurumsal üyelik başvurunuzu iletmek için şirket ve başvuru sahibi bilgilerinizi paylaşabilirsiniz.
      </p>
    </div>

    <aside class="hero-note">
      <strong>Kurumsal başvurunuzu tamamlayın</strong>
      <p>Zorunlu alanları eksiksiz doldurun. Başvurunuz WBN Türkiye ekibine e-posta olarak iletilir.</p>
    </aside>
  </div>
</section>

<main id="main" tabindex="-1" class="form-section">
  <div class="container form-shell">
    <aside class="form-nav">
      <small>Form kapsamı</small>
      <div class="progress"><span></span></div>
      <div class="nav-list">
        <span><b>1</b> Firma Bilgileri</span>
        <span><b>2</b> Başvuru Sahibi Bilgileri</span>
      </div>
    </aside>

    <section class="form-card">
      <div id="formContent">
        <div class="form-heading">
          <h2>Kurumsal üyelik başvurusu</h2>
          <p>Zorunlu alanlar yıldız (*) ile işaretlenmiştir.</p>
        </div>

        <form id="corporateMembershipForm" data-api="/api/corporate-applications" data-hide="#formContent" data-success="#successMessage">
          <div class="hp-field" aria-hidden="true">
            <label>Bu alanı boş bırakın <input type="text" name="_hp" tabindex="-1" autocomplete="off"></label>
          </div>

          <div class="section-block">
            <div class="section-title"><span>01</span> Firma Bilgileri</div>
            <div class="form-grid">
              <div class="field full">
                <label class="required" for="companyTitle">Firma Ticari Ünvanı</label>
                <input id="companyTitle" name="company_title" type="text" autocomplete="organization" required>
              </div>

              <div class="field">
                <label for="registryNo">Ticaret Odası Sicil Numarası</label>
                <input id="registryNo" name="registry_no" type="text" placeholder="İsteğe bağlı">
              </div>

              <div class="field">
                <label class="required" for="website">Web Sitesi</label>
                <input id="website" name="website" type="url" placeholder="https://www.firmaniz.com" required>
              </div>
            </div>
          </div>

          <div class="section-block">
            <div class="section-title"><span>02</span> Başvuru Sahibi Bilgileri</div>
            <div class="form-grid">
              <div class="field">
                <label class="required" for="fullName">Adı Soyadı</label>
                <input id="fullName" name="applicant_name" type="text" autocomplete="name" required>
              </div>

              <div class="field">
                <label class="required" for="title">Ünvanı</label>
                <input id="title" name="applicant_title" type="text" autocomplete="organization-title" required>
              </div>

              <div class="field">
                <label class="required" for="phone">Telefonu</label>
                <input id="phone" name="phone" type="tel" autocomplete="tel" required>
              </div>

              <div class="field">
                <label class="required" for="email">E-Mail Adresi</label>
                <input id="email" name="email" type="email" autocomplete="email" required>
              </div>
            </div>
          </div>

          <div class="submit-area">
            <button class="submit-button" id="submitButton" type="submit">Başvuruyu Gönder</button>
            <p class="form-note">Gönderdiğiniz başvuru bilgileri WBN Türkiye ekibine iletilir.</p>
          </div>
        </form>
      </div>

      <div class="success" id="successMessage">
        <div class="success-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="m5 12 4 4L19 6"></path>
          </svg>
        </div>
        <h3>Teşekkür ederiz</h3>
        <p>Kurumsal üyelik başvurunuz alınmıştır.</p>
      </div>
    </section>
  </div>
</main>
<?php layout_end(); ?>
