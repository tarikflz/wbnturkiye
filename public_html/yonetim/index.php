<?php
require dirname(__DIR__) . '/_app.php';

$admin = current_admin();
if (!$admin) {
    header('Location: /yonetim/login.php');
    exit;
}
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <title>WBN Türkiye | Yönetim Paneli</title>
  <link rel="icon" type="image/png" sizes="48x48" href="<?= e(asset('img/favicon-48.png')) ?>">
  <link rel="apple-touch-icon" href="<?= e(asset('img/favicon.png')) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/yonetim/assets/admin.css?v=<?= filemtime(__DIR__ . '/assets/admin.css') ?>">
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <small>WBN Türkiye</small>
      <strong>Yönetim Paneli</strong>
    </div>

    <div class="menu-label">Üyelik Yönetimi</div>
    <button class="nav-item" id="applicationsTab">
      <span class="nav-left">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M19 8v6M22 11h-6"/>
          </svg>
        </span>
        Yeni Üye Başvuruları
      </span>
      <span class="nav-badge" id="applicationCount">0</span>
    </button>

    <button class="nav-item nav-item-secondary" id="updatesTab" style="margin-top:10px;">
      <span class="nav-left">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M12 20h9"/>
            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
          </svg>
        </span>
        Üye Bilgi Güncellemeleri
      </span>
      <span class="nav-badge" id="updateCount">0</span>
    </button>

    <button class="nav-item nav-item-secondary" id="receiptsTab" style="margin-top:10px;">
      <span class="nav-left">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M4 4h16v16H4z"/>
            <path d="M8 8h8M8 12h8M8 16h5"/>
          </svg>
        </span>
        Dekontlar
      </span>
      <span class="nav-badge" id="receiptCount">0</span>
    </button>

    <button class="nav-item nav-item-secondary" id="corporateTab" style="margin-top:10px;">
      <span class="nav-left">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M3 21h18"/>
            <path d="M5 21V7l7-4 7 4v14"/>
            <path d="M9 10h2M13 10h2M9 14h2M13 14h2"/>
          </svg>
        </span>
        Kurumsal Üye Başvuruları
      </span>
      <span class="nav-badge" id="corporateCount">0</span>
    </button>

    <button class="nav-item nav-item-secondary" id="nodyoTab" style="margin-top:10px;">
      <span class="nav-left">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M17 5.5a3 3 0 0 1 0 5M18 14.3c1.8.7 3 2.5 3 4.7"/>
          </svg>
        </span>
        Nodyo Üyeleri
      </span>
      <span class="nav-badge" id="nodyoCount">0</span>
    </button>

    <button class="nav-item nav-item-secondary" id="nodyoMeetTab" style="margin-top:10px;">
      <span class="nav-left">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3"/>
          </svg>
        </span>
        Nodyo Görüşmeleri
      </span>
    </button>

    <div class="sidebar-user">
      Oturum açan
      <strong><?= e($admin) ?></strong>
      <a class="logout-link" href="/yonetim/logout.php">Çıkış Yap</a>
    </div>
  </aside>

  <main class="main">
    <section class="view active" id="listView">
      <div class="page-top">
        <div>
          <h1>Yeni Üye Başvuruları</h1>
          <p>Yeni üyelik taleplerini inceleyin, değerlendirin ve başvuru durumunu yönetin.</p>
        </div>
        <div class="backend-state" id="backendState">
          <span class="state-dot"></span>
          Veriler yükleniyor...
        </div>
      </div>

      <div class="toolbar">
        <div class="search">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-3.2-3.2"></path>
          </svg>
          <input type="search" id="searchInput" placeholder="Ad, e-posta veya ünvan ara">
        </div>

        <button class="excel-btn" id="exportApplicationsExcel">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9">
            <path d="M12 3v12"></path>
            <path d="m7 10 5 5 5-5"></path>
            <path d="M5 21h14"></path>
          </svg>
          Excel'e Aktar
        </button>
      </div>

      <div class="table-card">
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Başvuru Tarihi</th>
                <th>Adı Soyadı</th>
                <th>Ünvanı</th>
                <th>E-posta Adresi</th>
                <th>Durum</th>
                <th>Karar Tarihi</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="applicationRows"></tbody>
          </table>
        </div>
        <div class="empty" id="emptyState">Başvuru bulunamadı.</div>
      </div>
    </section>


    <section class="view" id="updatesView">
      <div class="page-top">
        <div>
          <h1>Üye Bilgi Güncellemeleri</h1>
          <p>Üye Bilgi Güncelleme Formu'nu dolduran üyeleri ve paylaştıkları güncel bilgileri görüntüleyin.</p>
        </div>
      </div>

      <div class="toolbar">
        <div class="search">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-3.2-3.2"></path>
          </svg>
          <input type="search" id="updateSearchInput" placeholder="Ad, e-posta, şirket veya ünvan ara">
        </div>

        <button class="excel-btn" id="exportUpdatesExcel">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9">
            <path d="M12 3v12"></path>
            <path d="m7 10 5 5 5-5"></path>
            <path d="M5 21h14"></path>
          </svg>
          Excel'e Aktar
        </button>
      </div>

      <div class="table-card">
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Güncelleme Tarihi</th>
                <th>Adı Soyadı</th>
                <th>Ünvanı</th>
                <th>E-posta Adresi</th>
                <th>Şirket</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="updateRows"></tbody>
          </table>
        </div>
        <div class="empty" id="updateEmptyState">Kayıt bulunamadı.</div>
      </div>
    </section>

    <section class="view" id="updateDetailView">
      <div class="detail-top">
        <button class="back-btn" id="updateBackButton">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="m15 18-6-6 6-6"/>
          </svg>
          Üye Bilgi Güncellemeleri
        </button>
      </div>

      <div class="detail-grid" style="grid-template-columns:1fr;">
        <article class="panel">
          <div class="panel-head">
            <div>
              <h2 id="updateDetailName">-</h2>
            </div>
          </div>
          <div class="panel-body" id="updateDetailContent"></div>
        </article>
      </div>
    </section>


    <section class="view" id="receiptsView">
      <div class="page-top">
        <div>
          <h1>Dekontlar</h1>
          <p>Bağış dekontu gönderen üyeleri görüntüleyin ve dekont dosyalarına erişin.</p>
        </div>
      </div>

      <div class="toolbar">
        <div class="search">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-3.2-3.2"></path>
          </svg>
          <input type="search" id="receiptSearchInput" placeholder="Firma, ad, e-posta veya üye durumu ara">
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <button class="excel-btn" id="exportReceiptsExcel">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9">
              <path d="M12 3v12"></path>
              <path d="m7 10 5 5 5-5"></path>
              <path d="M5 21h14"></path>
            </svg>
            XLS İndir
          </button>
          <button class="csv-btn" id="exportReceiptsCsv">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9">
              <path d="M12 3v12"></path>
              <path d="m7 10 5 5 5-5"></path>
              <path d="M5 21h14"></path>
            </svg>
            CSV İndir
          </button>
        </div>
      </div>

      <div class="table-card">
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Dekont Gönderme Tarihi</th>
                <th>Firma Adı</th>
                <th>Adı Soyadı</th>
                <th>E-Mail Adresi</th>
                <th>Üye Durumu</th>
                <th>Dekont</th>
              </tr>
            </thead>
            <tbody id="receiptRows"></tbody>
          </table>
        </div>
        <div class="empty" id="receiptEmptyState">Dekont kaydı bulunamadı.</div>
      </div>

    </section>

    <section class="view" id="receiptDetailView">
      <div class="detail-top">
        <button class="back-btn" id="receiptBackButton">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="m15 18-6-6 6-6"/>
          </svg>
          Dekontlar
        </button>
      </div>

      <article class="panel">
        <div class="panel-head">
          <div>
            <h2 id="receiptDetailName">-</h2>
          </div>
          <div class="receipt-actions">
            <a class="view-btn" id="receiptOpenButton" href="#" target="_blank" rel="noopener noreferrer">Yeni Sekmede Gör</a>
            <a class="view-btn" id="receiptDownloadButton" href="#" download>Dekontu İndir</a>
          </div>
        </div>
        <div class="panel-body">
          <div class="data-grid">
            <div class="data-item"><span>Dekont Gönderme Tarihi</span><strong id="receiptDetailDate">-</strong></div>
            <div class="data-item"><span>Firma Adı</span><strong id="receiptDetailCompany">-</strong></div>
            <div class="data-item"><span>Adı Soyadı</span><strong id="receiptDetailPerson">-</strong></div>
            <div class="data-item"><span>E-Mail Adresi</span><a id="receiptDetailEmail" href="#">-</a></div>
            <div class="data-item"><span>Üye Durumu</span><strong id="receiptDetailMembershipStatus">-</strong></div>
            <div class="data-item full"><span>Dosya Adı</span><strong id="receiptDetailFile">-</strong></div>
          </div>
          <div class="receipt-preview-wrap" id="receiptPreviewWrap"></div>
        </div>
      </article>
    </section>



    <section class="view" id="corporateView">
      <div class="page-top">
        <div>
          <h1>Kurumsal Üye Başvuruları</h1>
          <p>Kurumsal üyelik başvurularını inceleyin, detaylarını görüntüleyin ve başvuru durumunu yönetin.</p>
        </div>
      </div>

      <div class="toolbar">
        <div class="search">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-3.2-3.2"></path>
          </svg>
          <input type="search" id="corporateSearchInput" placeholder="Firma, başvuru sahibi, e-posta veya ünvan ara">
        </div>

        <button class="excel-btn" id="exportCorporateExcel">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9">
            <path d="M12 3v12"></path>
            <path d="m7 10 5 5 5-5"></path>
            <path d="M5 21h14"></path>
          </svg>
          Excel'e Aktar
        </button>
      </div>

      <div class="table-card">
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Başvuru Tarihi</th>
                <th>Firma Ticari Ünvanı</th>
                <th>Başvuru Sahibi</th>
                <th>Ünvanı</th>
                <th>E-posta Adresi</th>
                <th>Durum</th>
                <th>Karar Tarihi</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="corporateRows"></tbody>
          </table>
        </div>
        <div class="empty" id="corporateEmptyState">Kurumsal başvuru bulunamadı.</div>
      </div>
    </section>

    <section class="view" id="corporateDetailView">
      <div class="detail-top">
        <button class="back-btn" id="corporateBackButton">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="m15 18-6-6 6-6"/>
          </svg>
          Kurumsal Üye Başvuruları
        </button>
      </div>

      <div class="detail-grid">
        <article class="panel">
          <div class="panel-head">
            <div>
              <h2 id="corporateDetailName">-</h2>
            </div>
            <span class="status pending" id="corporateDetailStatus">Beklemede</span>
          </div>
          <div class="panel-body" id="corporateDetailContent"></div>
        </article>

        <aside class="panel action-card">
          <div class="panel-head">
            <h3>Kurumsal Başvuru Değerlendirmesi</h3>
          </div>
          <div class="panel-body">
            <div class="decision-state">
              <div class="decision-label">Başvuru Durumu</div>
              <div class="decision-value">
                <strong id="corporateDecisionText">Henüz değerlendirilmedi</strong>
              </div>

              <div class="mail-status">
                <div class="mail-row">
                  <span>Mail Durumu</span>
                  <span class="mail-pill mail-not-sent" id="corporateMailStatus">Gönderilmedi</span>
                </div>
              </div>
            </div>

            <div class="action-buttons">
              <button class="approve-btn" id="corporateApproveButton">Kurumsal Üyelik Başvurusu Onaylanmıştır</button>
              <button class="reject-btn" id="corporateRejectButton">Kurumsal Üyelik Başvurusu Onaylanmamıştır</button>
            </div>

            <div class="email-preview mail-editor" id="corporateApproveMailEditor" data-template-key="wbn_corporate_approve">
              <div class="mail-editor-head">
                <h4>Onay Sonrası Otomatik E-posta</h4>
                <div class="mail-edit-actions">
                  <button type="button" class="mail-edit-btn">Mail İçeriğini Düzenle</button>
                  <button type="button" class="mail-save-btn">Kaydet</button>
                </div>
              </div>
              <div class="mail-content">
                <p><strong id="corporateApproveEmailName" data-mail-editable="true">Merhaba,</strong></p>
                <p data-mail-editable="true">WBN Türkiye Kurumsal Üyelik Başvurunuz değerlendirilmiş ve başvurunuz onaylanmıştır.</p>
                <p data-mail-editable="true">Topluluğumuza hoş geldiniz.</p>
              </div>
            </div>

            <div class="email-preview reject-email-preview mail-editor" id="corporateRejectMailEditor" data-template-key="wbn_corporate_reject">
              <div class="mail-editor-head">
                <h4>Onaylanmama Sonrası Otomatik E-posta</h4>
                <div class="mail-edit-actions">
                  <button type="button" class="mail-edit-btn">Mail İçeriğini Düzenle</button>
                  <button type="button" class="mail-save-btn">Kaydet</button>
                </div>
              </div>
              <div class="mail-content">
                <p><strong id="corporateRejectEmailName" data-mail-editable="true">Merhaba,</strong></p>
                <p data-mail-editable="true">WBN Türkiye Kurumsal Üyelik Başvurunuz değerlendirilmiş ve başvurunuz onaylanmamıştır.</p>
                <p data-mail-editable="true">WBN Türkiye'ye göstermiş olduğunuz ilgi ve başvurunuz için teşekkür ederiz.</p>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </section>

    <section class="view" id="detailView">
      <div class="detail-top">
        <button class="back-btn" id="backButton">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="m15 18-6-6 6-6"/>
          </svg>
          Yeni Üye Başvuruları
        </button>
      </div>

      <div class="detail-grid">
        <article class="panel">
          <div class="panel-head">
            <div>
              <h2 id="detailName">-</h2>
            </div>
            <span class="status pending" id="detailStatus">Beklemede</span>
          </div>
          <div class="panel-body" id="detailContent"></div>
        </article>

        <aside class="panel action-card">
          <div class="panel-head">
            <h3>Başvuru Değerlendirmesi</h3>
          </div>
          <div class="panel-body">
            <div class="decision-state">
              <div class="decision-label">Başvuru Durumu</div>
              <div class="decision-value">
                <strong id="decisionText">Henüz değerlendirilmedi</strong>
              </div>

              <div class="mail-status">
                <div class="mail-row">
                  <span>Mail Durumu</span>
                  <span class="mail-pill mail-not-sent" id="mailStatus">Gönderilmedi</span>
                </div>
              </div>
            </div>

            <div class="action-buttons">
              <button class="approve-btn" id="approveButton">Yeni Üye Başvurusu Onaylanmıştır</button>
              <button class="reject-btn" id="rejectButton">Yeni Üye Başvurusu Onaylanmamıştır</button>
            </div>

            <div class="email-preview mail-editor" id="individualApproveMailEditor" data-template-key="wbn_individual_approve">
              <div class="mail-editor-head">
                <h4>Onay Sonrası Otomatik E-posta</h4>
                <div class="mail-edit-actions">
                  <button type="button" class="mail-edit-btn">Mail İçeriğini Düzenle</button>
                  <button type="button" class="mail-save-btn">Kaydet</button>
                </div>
              </div>
              <div class="mail-content">
                <p><strong id="emailPreviewName" data-mail-editable="true">Merhaba,</strong></p>
                <p data-mail-editable="true">WBN Türkiye Yeni Üye Başvurunuz değerlendirilmiş ve üyeliğiniz onaylanmıştır.</p>
                <p data-mail-editable="true">Topluluğumuza hoş geldiniz.</p>
              </div>
            </div>

            <div class="email-preview reject-email-preview mail-editor" id="individualRejectMailEditor" data-template-key="wbn_individual_reject">
              <div class="mail-editor-head">
                <h4>Onaylanmama Sonrası Otomatik E-posta</h4>
                <div class="mail-edit-actions">
                  <button type="button" class="mail-edit-btn">Mail İçeriğini Düzenle</button>
                  <button type="button" class="mail-save-btn">Kaydet</button>
                </div>
              </div>
              <div class="mail-content">
                <p><strong id="rejectEmailPreviewName" data-mail-editable="true">Merhaba,</strong></p>
                <p data-mail-editable="true">WBN Türkiye Yeni Üye Başvurunuz değerlendirilmiş ve üyeliğiniz onaylanmamıştır.</p>
                <p data-mail-editable="true">Topluluğumuza göstermiş olduğunuz ilgi ve başvurunuz için teşekkür ederiz.</p>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </section>
    <section class="view" id="nodyoView">
      <div class="page-top">
        <div>
          <h1>Nodyo Üyeleri</h1>
          <p>wbnturkiye.com üzerinden Nodyo için kayıt olan kullanıcılar. E-posta doğrulaması yapılanlar Nodyo'ya giriş yapabilmiştir.</p>
        </div>
      </div>
      <div class="toolbar">
        <div class="search">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.2-3.2"></path></svg>
          <input type="search" id="nodyoSearchInput" placeholder="Ad veya e-posta ara">
        </div>
      </div>
      <div class="table-card">
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Kayıt Tarihi</th><th>Adı Soyadı</th><th>E-posta Adresi</th><th>E-posta Doğrulama</th>
                <th>Nodyo Hesabı</th><th>Son Nodyo Girişi</th><th>Giriş Sayısı</th><th></th>
              </tr>
            </thead>
            <tbody id="nodyoRows"></tbody>
          </table>
        </div>
        <div class="empty" id="nodyoEmptyState">Kayıt bulunamadı.</div>
      </div>
    </section>

    <section class="view" id="nodyoMeetView">
      <div class="page-top">
        <div>
          <h1>Nodyo Görüşmeleri</h1>
          <p>WBN Türkiye üyelerinin hangi Nody (uzman) ile görüştüğü burada listelenecek.</p>
        </div>
      </div>
      <div class="table-card">
        <div class="empty" style="display:block">Görüşme verisi henüz alınamıyor: Nodyo tarafından görüşme (kim kiminle) API'si sağlandığında bu bölüm doldurulacaktır.</div>
      </div>
    </section>

  </main>
</div>

<div class="toast" id="toast"></div>

<script src="/yonetim/assets/admin.js?v=<?= filemtime(__DIR__ . '/assets/admin.js') ?>"></script>
<script src="/yonetim/assets/nodyo-admin.js?v=<?= filemtime(__DIR__ . '/assets/nodyo-admin.js') ?>"></script>
</body>
</html>
