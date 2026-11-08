<?php
require __DIR__ . '/_app.php';

layout_start([
    'title' => 'WBN Türkiye | Dekont Gönderme Formu',
    'description' => 'WBN Türkiye Bağış Dekontu Gönderme Formu',
    'css' => ['pages/dekont-gonder'],
    'js' => ['forms', 'pages/dekont-gonder'],
    'noindex' => true,
]);
?>
<main id="main" tabindex="-1" class="page">
  <section class="visual">
    <img class="brand-logo" src="<?= e(asset('img/wbn-logo-white.png')) ?>" alt="WBN Türkiye logosu" width="188" height="94">

    <div class="hero-copy">
      <div class="eyebrow">WBN Türkiye</div>
      <h1>Dekont Gönderme Formu</h1>
      <p>Üyelik bağışınıza ait dekontu bu form üzerinden güvenli ve hızlı biçimde WBN Türkiye ekibine iletebilirsiniz.</p>
    </div>

  </section>

  <section class="form-side">
    <div class="form-card">
      <div id="formContent">
        <h2>Bağış Dekontunuzu Gönderin</h2>
        <p>Zorunlu alanlar yıldız (*) ile işaretlenmiştir.</p>

        <form id="receiptForm" enctype="multipart/form-data" data-api="/api/receipts" data-hide="#formContent" data-success="#successMessage">
          <div class="hp-field" aria-hidden="true">
            <label>Bu alanı boş bırakın <input type="text" name="_hp" tabindex="-1" autocomplete="off"></label>
          </div>

          <div class="field">
            <label class="required" for="fullName">Adı Soyadı</label>
            <input id="fullName" name="full_name" type="text" autocomplete="name" required>
          </div>

          <div class="field">
            <label class="required" for="companyName">Firma Adı</label>
            <input id="companyName" name="company_name" type="text" autocomplete="organization" required>
          </div>

          <div class="field">
            <label class="required" for="email">E-Mail Adresiniz</label>
            <input id="email" name="email" type="email" autocomplete="email" required>
          </div>

          <div class="field">
            <label class="required">Üye Durumu</label>
            <div class="membership-choice-group" id="membershipChoiceGroup">
              <label class="membership-choice">
                <input id="newMemberCheck" type="checkbox" name="membership_status" value="Yeni Üye">
                <span>Yeni Üyeyim</span>
              </label>
              <label class="membership-choice">
                <input id="renewalCheck" type="checkbox" name="membership_status" value="Mevcut Üyelik Yenileme">
                <span>Mevcut üyeliğimi yeniliyorum</span>
              </label>
            </div>
          </div>

          <div class="field">
            <label>Üyelik Tipi</label>
            <div class="membership-choice-group" id="memberTypeChoiceGroup">
              <label class="membership-choice">
                <input id="corporateCheck" type="checkbox" name="membership_type" value="Kurumsal">
                <span>Kurumsal</span>
              </label>
              <label class="membership-choice">
                <input id="individualCheck" type="checkbox" name="membership_type" value="Bireysel">
                <span>Bireysel</span>
              </label>
            </div>
          </div>

          <div class="field">
            <label class="required" for="receipt">Dekont Upload</label>
            <div class="upload-box">
              <div class="upload-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
                  <path d="M12 16V4"></path>
                  <path d="m7 9 5-5 5 5"></path>
                  <path d="M5 20h14"></path>
                </svg>
              </div>
              <strong>Dekont dosyanızı seçin</strong>
              <span>PDF, JPG veya PNG formatı (en fazla 10 MB)</span>
              <input id="receipt" name="receipt" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp" required>
            </div>
            <div class="file-name" id="fileName"></div>
          </div>

          <button class="submit-btn" id="submitBtn" type="submit">Gönder</button>

          <div class="error-message" id="errorMessage"></div>
          <p class="form-note">Gönderdiğiniz bilgiler ve dekont yalnızca ilgili üyelik sürecinin yürütülmesi amacıyla iletilir.</p>
        </form>
      </div>

      <div class="success" id="successMessage">
        <div class="success-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="m5 12 4 4L19 6"></path>
          </svg>
        </div>
        <h3>Teşekkür ederiz</h3>
        <p>Dekontunuz gönderildi.</p>
      </div>
    </div>
  </section>
</main>
<?php layout_end(); ?>
