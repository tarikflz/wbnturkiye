// Yönetim Paneli
(function(){
/*
  CANLI ENTEGRASYON SÖZLEŞMESİ
  ---------------------------
  GET  /api/applications
    -> [{ id, created_at, full_name, title, email, ... }]

  POST /api/applications
    -> Yeni Üye Başvuru Formu buraya kayıt oluşturur.

  POST /api/applications/:id/approve
    -> Başvuruyu approved yapar.
    -> Başvuru sahibine onay + TEV bağış butonlu HTML e-posta gönderir.
    -> email_status = "sent" veya "error" olarak günceller.

  POST /api/applications/:id/reject
    -> Başvuruyu rejected yapar.
    -> Başvuru sahibine onaylanmama e-postası gönderir.
    -> email_status = "sent" veya "error" olarak günceller.

  GET  /api/receipts
    -> [{ id, submitted_at, full_name, email, membership_status, receipt_name, receipt_url, receipt_mime }]

  GET  /api/corporate-applications
    -> [{ id, created_at, company_title, registry_no, website, applicant_name, applicant_title, phone, email, status, email_status }]

  POST /api/corporate-applications/:id/approve
    -> Kurumsal başvuruyu approved yapar.
    -> Başvuru sahibine onay e-postası gönderir.
    -> E-postada TEV “WBN Türkiye Geleceğin Arıları” burs fonu için Bağış Yap bağlantısı bulunur.
    -> Bağış sonrasında dekont yükleme formu bağlantısı gösterilir (şimdilik ....).
    -> email_status = "sent" veya "error" olarak günceller.

  POST /api/corporate-applications/:id/reject
    -> Kurumsal başvuruyu rejected yapar.
    -> Başvuru sahibine onaylanmama e-postası gönderir.
    -> email_status = "sent" veya "error" olarak günceller.

  POST /api/receipts
    -> Bireysel Dekont formundan multipart/form-data kaydı alır.
    -> Dekont dosyasını saklar ve tulin.cakmak@novaformdan.com adresine e-posta gönderir.
    -> Gönderme tarihi, Adı Soyadı, E-Mail Adresi ve Üye Durumu e-posta içeriğinde yer alır.

  GET  /api/corporate-receipts
    -> [{ id, submitted_at, company_name, email, membership_status, receipt_name, receipt_url, receipt_mime }]

  POST /api/corporate-receipts
    -> Kurumsal Dekont Gönderme Formundan multipart/form-data kaydı alır.
    -> Dekont dosyasını saklar ve tulin.cakmak@novaformdan.com adresine e-posta gönderir.
    -> Gönderme tarihi, Firma Adı, E-Mail Adresi ve Üye Durumu e-posta içeriğinde yer alır.

  Güvenlik:
  - Admin sayfası authentication arkasında olmalı.
  - E-posta servis anahtarı frontend'e yazılmamalı.
  - Approval işlemi sunucu tarafında yetkilendirilmelidir.
*/

const API_BASE = "/api";
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// Başvuru verileri kullanıcıdan gelir: HTML'e yazmadan önce mutlaka kaçışla.
function esc(value){
  return String(value ?? "").replace(/[&<>"']/g, ch => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[ch]));
}

function safeUrl(value){
  return /^https?:\/\//i.test(String(value || "")) ? esc(value) : "#";
}

async function api(path, options = {}){
  const r = await fetch(API_BASE + path, {
    credentials: "same-origin",
    ...options,
    headers: {"Accept":"application/json", "X-CSRF-Token": CSRF_TOKEN, ...(options.headers || {})}
  });
  if(r.status === 401){
    window.location.href = "/yonetim/login.php";
    throw new Error("Oturum süresi doldu.");
  }
  const data = await r.json().catch(() => ({}));
  if(!r.ok) throw new Error(data.message || "İşlem tamamlanamadı.");
  return data;
}

async function loadAll(){
  const state = document.getElementById("backendState");
  state.lastChild.textContent = " Veriler yükleniyor...";
  try{
    [applications, memberUpdates, receipts, corporateApplications, mailTemplates] = await Promise.all([
      api("/applications"), api("/member-updates"), api("/receipts"), api("/corporate-applications"), api("/mail-templates").catch(() => ({}))
    ]);
    if(Array.isArray(mailTemplates)) mailTemplates = {};  // boş liste PHP'de [] döner
    await migrateLocalMailTemplates();
    renderRows();
    renderUpdateRows();
    renderReceiptRows();
    renderCorporateRows();
    state.lastChild.textContent = " Canlı veri · " + new Date().toLocaleTimeString("tr-TR",{hour:"2-digit",minute:"2-digit"});
  }catch(err){
    state.lastChild.textContent = " Veriler yüklenemedi";
    showToast(err.message || "Veriler yüklenemedi.");
  }
}


function getEditorCurrentName(editorId){
  if(editorId.startsWith("corporate")){
    return currentCorporateApplication?.applicant_name || "";
  }
  return currentApplication?.full_name || "";
}

function saveMailEditorTemplate(editor){
  const currentName = getEditorCurrentName(editor.id);
  const values = [...editor.querySelectorAll('[data-mail-editable="true"]')].map(el => {
    let value = el.innerHTML;
    if(currentName){
      value = value.split(esc(currentName)).join("{{name}}");
    }
    return value;
  });
  return saveMailTemplate(editor.dataset.templateKey, values);
}

// Şablonlar sunucuda saklanır; böylece tüm yöneticiler ve bilgisayarlar aynı içeriği görür.
let mailTemplates = {};

async function saveMailTemplate(key, values){
  await api("/mail-templates/" + encodeURIComponent(key), {
    method: "PUT",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({values})
  });
  mailTemplates[key] = values;
}

// Eski sürümde şablonlar tarayıcıya (localStorage) kaydediliyordu: sunucuda karşılığı yoksa bir kez sunucuya taşı.
async function migrateLocalMailTemplates(){
  for(const editor of document.querySelectorAll(".mail-editor")){
    const key = editor.dataset.templateKey;
    let stored = null;
    try{ stored = localStorage.getItem(key); }catch(e){ return; }
    if(!stored || mailTemplates[key]) continue;
    try{
      const values = JSON.parse(stored);
      if(Array.isArray(values) && values.every(v => typeof v === "string")){
        await saveMailTemplate(key, values);
        localStorage.removeItem(key);
      }
    }catch(e){ console.warn("Yerel mail şablonu sunucuya taşınamadı.", e); }
  }
}

function applyMailEditorTemplate(editorId, currentName){
  const editor = document.getElementById(editorId);
  if(!editor) return;

  const values = mailTemplates[editor.dataset.templateKey];
  if(!Array.isArray(values)) return;

  try{
    const fields = [...editor.querySelectorAll('[data-mail-editable="true"]')];
    fields.forEach((el, index) => {
      if(values[index] === undefined) return;
      el.innerHTML = String(values[index]).split("{{name}}").join(esc(currentName));
    });
  }catch(e){
    console.warn("Kaydedilmiş mail şablonu okunamadı.", e);
  }
}

function getMailEditorHtml(editorId){
  const editor = document.getElementById(editorId);
  return editor?.querySelector(".mail-content")?.innerHTML || "";
}

function initMailEditors(){
  document.querySelectorAll(".mail-editor").forEach(editor => {
    const editBtn = editor.querySelector(".mail-edit-btn");
    const saveBtn = editor.querySelector(".mail-save-btn");
    const editableFields = [...editor.querySelectorAll('[data-mail-editable="true"]')];

    editBtn.addEventListener("click", () => {
      editor.classList.add("editing");
      editableFields.forEach(el => el.setAttribute("contenteditable", "true"));
      editableFields[0]?.focus();
    });

    saveBtn.addEventListener("click", async () => {
      editableFields.forEach(el => el.removeAttribute("contenteditable"));
      editor.classList.remove("editing");
      try{
        await saveMailEditorTemplate(editor);
        showToast("Mail içeriği kaydedildi.");
      }catch(err){
        showToast(err.message || "Mail içeriği kaydedilemedi.");
      }
    });
  });
}

// Veriler sunucudan yüklenir (loadAll)
let applications = [];
let memberUpdates = [];
let receipts = [];
let corporateApplications = [];

let currentCorporateApplication = null;
let currentReceipt = null;
let decisionBusy = false;

let currentUpdate = null;

let currentApplication = null;

const rows = document.getElementById("applicationRows");
const count = document.getElementById("applicationCount");
const searchInput = document.getElementById("searchInput");
const emptyState = document.getElementById("emptyState");
const listView = document.getElementById("listView");
const detailView = document.getElementById("detailView");
const toast = document.getElementById("toast");
const updatesView = document.getElementById("updatesView");
const updateDetailView = document.getElementById("updateDetailView");
const updateRows = document.getElementById("updateRows");
const updateCount = document.getElementById("updateCount");
const updateSearchInput = document.getElementById("updateSearchInput");
const updateEmptyState = document.getElementById("updateEmptyState");
const receiptsView = document.getElementById("receiptsView");
const receiptDetailView = document.getElementById("receiptDetailView");
const receiptRows = document.getElementById("receiptRows");
const receiptCount = document.getElementById("receiptCount");
const receiptSearchInput = document.getElementById("receiptSearchInput");
const receiptEmptyState = document.getElementById("receiptEmptyState");
const corporateView = document.getElementById("corporateView");
const corporateDetailView = document.getElementById("corporateDetailView");
const corporateRows = document.getElementById("corporateRows");
const corporateCount = document.getElementById("corporateCount");
const corporateSearchInput = document.getElementById("corporateSearchInput");
const corporateEmptyState = document.getElementById("corporateEmptyState");


const statusLabel = {
  pending: "Beklemede",
  approved: "Onaylandı",
  rejected: "Onaylanmadı"
};

function initials(name){
  return name.split(" ").filter(Boolean).slice(0,2).map(x => x[0].toUpperCase()).join("");
}

function formatDate(value){
  const d = new Date(value);
  return d.toLocaleDateString("tr-TR",{day:"2-digit",month:"2-digit",year:"numeric"}) +
    " " + d.toLocaleTimeString("tr-TR",{hour:"2-digit",minute:"2-digit"});
}

function statusClass(status){
  if(status === "approved") return "approved";
  if(status === "rejected") return "rejected";
  return "pending";
}


function safeJoin(value){
  return Array.isArray(value) ? value.join(", ") : (value || "");
}

function exportApplicationsToExcel(){
  const q = searchInput.value.trim().toLocaleLowerCase("tr-TR");
  const data = applications
    .filter(a => [a.full_name,a.email,a.title].join(" ").toLocaleLowerCase("tr-TR").includes(q))
    .map(a => ({
      "Başvuru No": a.id,
      "Başvuru Tarihi": formatDate(a.created_at),
      "Ad Soyad": a.full_name,
      "Cep Telefonu": a.phone,
      "E-posta Adresi": a.email,
      "Referans": a.reference,
      "KVKK ve Gizlilik Onayı": a.kvkk,
      "Kurum / Şirket": a.company,
      "Ünvan": a.title,
      "Sektör": a.sector,
      "Diğer Sektör": a.other_sector || "",
      "Uzmanlık / İlgi Alanları": safeJoin(a.expertise),
      "Diğer Uzmanlık / İlgi Alanı": a.other_expertise || "",
      "LinkedIn": a.linkedin,
      "Aktif Yer Almak İstediği Alanlar": safeJoin(a.active_areas),
      "Diğer Aktif Alan": a.other_active_area || "",
      "Veri Tabanını Güncel Tutma Onayı": a.database_update_consent,
      "Ek Bilgi": a.extra_info || "",
      "Başvuru Durumu": statusLabel[a.status] || a.status,
      "Karar Tarihi": a.decided_at ? formatDate(a.decided_at) : "",
      "Mail Durumu": a.email_status === "sent" ? "Gönderildi" : (a.email_status === "error" ? "Gönderim Hatası" : "Gönderilmedi")
    }));

  downloadExcel(data, "WBN_Turkiye_Yeni_Uye_Basvurulari.xlsx", "Yeni Üye Başvuruları");
}

function exportUpdatesToExcel(){
  const q = updateSearchInput.value.trim().toLocaleLowerCase("tr-TR");
  const data = memberUpdates
    .filter(a => [a.full_name,a.email,a.title,a.company].join(" ").toLocaleLowerCase("tr-TR").includes(q))
    .map(a => ({
      "Kayıt No": a.id,
      "Güncelleme Tarihi": formatDate(a.updated_at),
      "Ad Soyad": a.full_name,
      "Cep Telefonu": a.phone,
      "E-posta Adresi": a.email,
      "KVKK ve Gizlilik Onayı": a.kvkk,
      "Kurum / Şirket": a.company,
      "Ünvan": a.title,
      "Sektör": a.sector,
      "Diğer Sektör": a.other_sector || "",
      "Uzmanlık / İlgi Alanları": safeJoin(a.expertise),
      "Diğer Uzmanlık / İlgi Alanı": a.other_expertise || "",
      "LinkedIn": a.linkedin,
      "WBN Etkinliklerine Daha Önce Katılım": a.attended_events,
      "Aktif Yer Almak İstediği Alanlar": safeJoin(a.active_areas),
      "Diğer Aktif Alan": a.other_active_area || "",
      "Veri Tabanını Güncel Tutma Onayı": a.database_update_consent,
      "Ek Bilgi": a.extra_info || ""
    }));

  downloadExcel(data, "WBN_Turkiye_Uye_Bilgi_Guncellemeleri.xlsx", "Üye Bilgi Güncellemeleri");
}

function downloadExcel(data, fileName, sheetName){
  if(!data.length){
    showToast("Excel'e aktarılacak kayıt bulunamadı.");
    return;
  }

  if(typeof XLSX === "undefined"){
    showToast("Excel modülü yüklenemedi. İnternet bağlantısını kontrol edip tekrar deneyin.");
    return;
  }

  const worksheet = XLSX.utils.json_to_sheet(data);

  // Kolon genişliklerini okunabilir hale getir
  const widths = Object.keys(data[0]).map(key => {
    const maxLen = Math.max(
      key.length,
      ...data.map(row => String(row[key] ?? "").length)
    );
    return { wch: Math.min(Math.max(maxLen + 2, 12), 42) };
  });
  worksheet["!cols"] = widths;

  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, sheetName.substring(0,31));
  XLSX.writeFile(workbook, fileName);
}


function getFilteredReceipts(){
  const q = receiptSearchInput.value.trim().toLocaleLowerCase("tr-TR");
  return receipts
    .filter(r =>
      [r.company_name, r.full_name, r.email, r.membership_status, r.membership_type, r.receipt_name]
        .join(" ").toLocaleLowerCase("tr-TR").includes(q)
    )
    .sort((a,b) => new Date(b.submitted_at) - new Date(a.submitted_at));
}

function exportReceiptsToExcel(){
  const data = getFilteredReceipts().map(r => ({
    "Dekont No": r.id,
    "Dekont Gönderme Tarihi": formatDate(r.submitted_at),
    "Firma Adı": r.company_name || "-",
    "Adı Soyadı": r.full_name || "-",
    "E-Mail Adresi": r.email,
    "Üye Durumu": r.membership_status || "-",
    "Üyelik Tipi": r.membership_type || "-",
    "Dosya Adı": r.receipt_name
  }));
  downloadExcel(data, "WBN_Turkiye_Dekontlar.xlsx", "Dekontlar");
}

function exportReceiptsToCsv(){
  const data = getFilteredReceipts().map(r => ({
    "Dekont No": r.id,
    "Dekont Gönderme Tarihi": formatDate(r.submitted_at),
    "Firma Adı": r.company_name || "-",
    "Adı Soyadı": r.full_name || "-",
    "E-Mail Adresi": r.email,
    "Üye Durumu": r.membership_status || "-",
    "Üyelik Tipi": r.membership_type || "-",
    "Dosya Adı": r.receipt_name
  }));

  if(!data.length){
    showToast("CSV'ye aktarılacak dekont kaydı bulunamadı.");
    return;
  }

  const headers = Object.keys(data[0]);
  const escapeCsv = value => {
    const str = String(value ?? "");
    return '"' + str.replace(/"/g, '""') + '"';
  };
  const csv = "\uFEFF" + [
    headers.map(escapeCsv).join(";"),
    ...data.map(row => headers.map(h => escapeCsv(row[h])).join(";"))
  ].join("\n");

  const blob = new Blob([csv], {type:"text/csv;charset=utf-8;"});
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "WBN_Turkiye_Dekontlar.csv";
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

function renderReceiptRows(){
  const filtered = getFilteredReceipts();
  receiptCount.textContent = receipts.length;

  receiptRows.innerHTML = filtered.map(r => `
    <tr>
      <td>${formatDate(r.submitted_at)}</td>
      <td>${esc(r.company_name || "-")}</td>
      <td>
        <div class="person">
          <div class="avatar">${esc(initials(r.full_name || r.company_name || "-"))}</div>
          <div>
            <strong>${esc(r.full_name || "-")}</strong>
            <small>${esc(r.id)}</small>
          </div>
        </div>
      </td>
      <td>${esc(r.email || "-")}</td>
      <td><span class="status ${r.membership_status === "Yeni Üye" ? "approved" : "pending"}">${esc(r.membership_status || "-")}</span></td>
      <td><div class="row-actions"><button class="view-btn receipt-view-btn" data-id="${esc(r.id)}">Detaylı Gör</button><button class="delete-btn" data-delete="receipts" data-id="${esc(r.id)}">Sil</button></div></td>
    </tr>
  `).join("");

  receiptEmptyState.style.display = filtered.length ? "none" : "block";
  document.querySelectorAll(".receipt-view-btn").forEach(btn => {
    btn.addEventListener("click", () => openReceiptDetail(btn.dataset.id));
  });
}

function openReceiptDetail(id){
  const r = receipts.find(x => x.id === id);
  if(!r) return;
  currentReceipt = r;

  const personName = r.full_name || "-";
  const companyName = r.company_name || "-";

  document.getElementById("receiptDetailName").textContent =
    companyName !== "-" ? companyName : personName;
  document.getElementById("receiptDetailDate").textContent = formatDate(r.submitted_at);
  document.getElementById("receiptDetailCompany").textContent = companyName;
  document.getElementById("receiptDetailPerson").textContent = personName;
  const emailEl = document.getElementById("receiptDetailEmail");
  emailEl.textContent = r.email || "-";
  emailEl.href = r.email ? `mailto:${esc(r.email)}` : "#";
  document.getElementById("receiptDetailMembershipStatus").textContent = r.membership_status || "-";
  document.getElementById("receiptDetailFile").textContent = r.receipt_name || "-";

  const openBtn = document.getElementById("receiptOpenButton");
  const downloadBtn = document.getElementById("receiptDownloadButton");
  openBtn.href = r.receipt_url;
  downloadBtn.href = r.receipt_url + "?download=1";
  downloadBtn.download = r.receipt_name || "dekont";

  const wrap = document.getElementById("receiptPreviewWrap");
  if((r.receipt_mime || "").startsWith("image/")){
    wrap.innerHTML = `<img src="${esc(r.receipt_url)}" alt="Dekont önizlemesi">`;
  }else{
    wrap.innerHTML = `<iframe src="${esc(r.receipt_url)}" title="Dekont önizlemesi"></iframe>`;
  }

  listView.classList.remove("active");
  detailView.classList.remove("active");
  updatesView.classList.remove("active");
  updateDetailView.classList.remove("active");
  receiptsView.classList.remove("active");
  receiptDetailView.classList.add("active");
  window.scrollTo({top:0, behavior:"smooth"});
}



function getFilteredCorporateApplications(){
  const q = corporateSearchInput.value.trim().toLocaleLowerCase("tr-TR");
  return corporateApplications.filter(a =>
    [a.company_title, a.applicant_name, a.applicant_title, a.email]
      .join(" ").toLocaleLowerCase("tr-TR").includes(q)
  );
}

function exportCorporateToExcel(){
  const data = getFilteredCorporateApplications().map(a => ({
    "Başvuru No": a.id,
    "Başvuru Tarihi": formatDate(a.created_at),
    "Firma Ticari Ünvanı": a.company_title,
    "Ticaret Odası Sicil Numarası": a.registry_no,
    "Web Sitesi": a.website,
    "Başvuru Sahibi Adı Soyadı": a.applicant_name,
    "Başvuru Sahibi Ünvanı": a.applicant_title,
    "Telefon": a.phone,
    "E-posta Adresi": a.email,
    "Başvuru Durumu": statusLabel[a.status] || a.status,
    "Karar Tarihi": a.decided_at ? formatDate(a.decided_at) : "",
    "Mail Durumu": a.email_status === "sent" ? "Gönderildi" : (a.email_status === "error" ? "Gönderim Hatası" : "Gönderilmedi")
  }));
  downloadExcel(data, "WBN_Turkiye_Kurumsal_Uye_Basvurulari.xlsx", "Kurumsal Başvurular");
}

function renderCorporateRows(){
  const filtered = getFilteredCorporateApplications();
  corporateCount.textContent = corporateApplications.length;

  corporateRows.innerHTML = filtered.map(a => `
    <tr>
      <td>${formatDate(a.created_at)}</td>
      <td>
        <div class="person">
          <div class="avatar">${esc(initials(a.company_title))}</div>
          <div>
            <strong>${esc(a.company_title)}</strong>
            <small>${esc(a.registry_no || "-")}</small>
          </div>
        </div>
      </td>
      <td>${esc(a.applicant_name || "-")}</td>
      <td>${esc(a.applicant_title || "-")}</td>
      <td>${esc(a.email || "-")}</td>
      <td><span class="status ${statusClass(a.status)}">${statusLabel[a.status] || "Beklemede"}</span></td>
      <td>${a.decided_at ? formatDate(a.decided_at) : "-"}</td>
      <td><div class="row-actions"><button class="view-btn corporate-view-btn" data-id="${esc(a.id)}">Detaylı Gör</button><button class="delete-btn" data-delete="corporate-applications" data-id="${esc(a.id)}">Sil</button></div></td>
    </tr>
  `).join("");

  corporateEmptyState.style.display = filtered.length ? "none" : "block";

  document.querySelectorAll(".corporate-view-btn").forEach(btn => {
    btn.addEventListener("click", () => openCorporateDetail(btn.dataset.id));
  });
}

function openCorporateDetail(id){
  const a = corporateApplications.find(x => x.id === id);
  if(!a) return;
  currentCorporateApplication = a;

  document.getElementById("corporateDetailName").textContent = a.company_title;
  document.getElementById("corporateApproveEmailName").textContent = `Merhaba ${a.applicant_name},`;
  document.getElementById("corporateRejectEmailName").textContent = `Merhaba ${a.applicant_name},`;
  applyMailEditorTemplate("corporateApproveMailEditor", a.applicant_name);
  applyMailEditorTemplate("corporateRejectMailEditor", a.applicant_name);

  document.getElementById("corporateDetailContent").innerHTML = `
    <div class="data-section">
      <h4>Firma Bilgileri</h4>
      <div class="data-grid">
        <div class="data-item"><span>Başvuru Tarihi</span><strong>${formatDate(a.created_at)}</strong></div>
        <div class="data-item"><span>Başvuru No</span><strong>${esc(a.id)}</strong></div>
        <div class="data-item full"><span>Firma Ticari Ünvanı</span><strong>${esc(a.company_title || "-")}</strong></div>
        <div class="data-item"><span>Ticaret Odası Sicil Numarası</span><strong>${esc(a.registry_no || "-")}</strong></div>
        <div class="data-item"><span>Web Sitesi</span><a href="${safeUrl(a.website)}" target="_blank" rel="noopener noreferrer">${esc(a.website || "-")}</a></div>
      </div>
    </div>

    <div class="data-section">
      <h4>Başvuru Sahibi Bilgileri</h4>
      <div class="data-grid">
        <div class="data-item"><span>Adı Soyadı</span><strong>${esc(a.applicant_name || "-")}</strong></div>
        <div class="data-item"><span>Ünvanı</span><strong>${esc(a.applicant_title || "-")}</strong></div>
        <div class="data-item"><span>Telefonu</span><strong>${esc(a.phone || "-")}</strong></div>
        <div class="data-item"><span>E-posta Adresi</span><a href="mailto:${esc(a.email)}">${esc(a.email || "-")}</a></div>
      </div>
    </div>
  `;

  refreshCorporateDecisionPanel();

  listView.classList.remove("active");
  detailView.classList.remove("active");
  updatesView.classList.remove("active");
  updateDetailView.classList.remove("active");
  receiptsView.classList.remove("active");
  receiptDetailView.classList.remove("active");
  corporateView.classList.remove("active");
  corporateDetailView.classList.add("active");
  window.scrollTo({top:0, behavior:"smooth"});
}

function refreshCorporateDecisionPanel(){
  if(!currentCorporateApplication) return;
  const a = currentCorporateApplication;

  const detailStatus = document.getElementById("corporateDetailStatus");
  const decisionText = document.getElementById("corporateDecisionText");
  const mailStatus = document.getElementById("corporateMailStatus");
  const approveButton = document.getElementById("corporateApproveButton");
  const rejectButton = document.getElementById("corporateRejectButton");

  detailStatus.textContent = statusLabel[a.status] || "Beklemede";
  detailStatus.className = `status ${statusClass(a.status)}`;

  if(a.status === "approved"){
    decisionText.textContent = "Kurumsal başvuru onaylandı";
  } else if(a.status === "rejected"){
    decisionText.textContent = "Kurumsal başvuru onaylanmadı";
  } else {
    decisionText.textContent = "Henüz değerlendirilmedi";
  }
  if(a.decided_at) decisionText.textContent += " · " + formatDate(a.decided_at);

  if(a.email_status === "sent"){
    mailStatus.textContent = "Gönderildi";
    mailStatus.className = "mail-pill mail-sent";
  } else if(a.email_status === "error"){
    mailStatus.textContent = "Gönderim Hatası";
    mailStatus.className = "mail-pill mail-error";
  } else {
    mailStatus.textContent = "Gönderilmedi";
    mailStatus.className = "mail-pill mail-not-sent";
  }

  approveButton.disabled = a.status === "rejected" || (a.status === "approved" && a.email_status !== "error");
  rejectButton.disabled = a.status === "approved" || (a.status === "rejected" && a.email_status !== "error");
}

async function approveCorporateApplication(){
  if(!currentCorporateApplication || decisionBusy) return;
  decisionBusy = true;

  try{
    const updated = await api(`/corporate-applications/${encodeURIComponent(currentCorporateApplication.id)}/approve`,{
      method:"POST",
      headers:{"Content-Type":"application/json"},
      body:JSON.stringify({email_html:getMailEditorHtml("corporateApproveMailEditor")})
    });
    Object.assign(currentCorporateApplication, updated);
    refreshCorporateDecisionPanel();
    renderCorporateRows();
    showToast(updated.email_status === "sent"
      ? "Kurumsal üyelik başvurusu onaylandı; bağış ve dekont yükleme bilgilerini içeren e-posta gönderildi."
      : "Durum güncellendi ancak e-posta gönderilemedi. Tekrar deneyebilirsiniz.");
  }catch(err){
    showToast(err.message || "İşlem sırasında hata oluştu.");
  }finally{
    decisionBusy = false;
    refreshCorporateDecisionPanel();
  }
}

async function rejectCorporateApplication(){
  if(!currentCorporateApplication || decisionBusy) return;
  decisionBusy = true;

  try{
    const updated = await api(`/corporate-applications/${encodeURIComponent(currentCorporateApplication.id)}/reject`,{
      method:"POST",
      headers:{"Content-Type":"application/json"},
      body:JSON.stringify({email_html:getMailEditorHtml("corporateRejectMailEditor")})
    });
    Object.assign(currentCorporateApplication, updated);
    refreshCorporateDecisionPanel();
    renderCorporateRows();
    showToast(updated.email_status === "sent"
      ? "Kurumsal üyelik başvurusu onaylanmadı ve bilgilendirme e-postası gönderildi."
      : "Durum güncellendi ancak e-posta gönderilemedi. Tekrar deneyebilirsiniz.");
  }catch(err){
    showToast(err.message || "İşlem sırasında hata oluştu.");
  }finally{
    decisionBusy = false;
    refreshCorporateDecisionPanel();
  }
}

function renderRows(){
  const q = searchInput.value.trim().toLocaleLowerCase("tr-TR");
  const filtered = applications.filter(a =>
    [a.full_name,a.email,a.title].join(" ").toLocaleLowerCase("tr-TR").includes(q)
  );

  count.textContent = applications.length;
  rows.innerHTML = filtered.map(a => `
    <tr>
      <td>${formatDate(a.created_at)}</td>
      <td>
        <div class="person">
          <div class="avatar">${esc(initials(a.full_name))}</div>
          <div>
            <strong>${esc(a.full_name)}</strong>
            <small>${esc(a.company || "-")}</small>
          </div>
        </div>
      </td>
      <td>${esc(a.title || "-")}</td>
      <td>${esc(a.email || "-")}</td>
      <td><span class="status ${statusClass(a.status)}">${statusLabel[a.status] || "Beklemede"}</span></td>
      <td>${a.decided_at ? formatDate(a.decided_at) : "-"}</td>
      <td><div class="row-actions"><button class="view-btn" data-id="${esc(a.id)}">Detaylı Gör</button><button class="delete-btn" data-delete="applications" data-id="${esc(a.id)}">Sil</button></div></td>
    </tr>
  `).join("");

  emptyState.style.display = filtered.length ? "none" : "block";

  document.querySelectorAll(".view-btn").forEach(btn => {
    btn.addEventListener("click", () => openDetail(btn.dataset.id));
  });
}

function tags(items){
  if(!items || !items.length) return "<strong>-</strong>";
  return `<div class="tag-list">${items.map(x => `<span class="tag">${esc(x)}</span>`).join("")}</div>`;
}


function renderUpdateRows(){
  const q = updateSearchInput.value.trim().toLocaleLowerCase("tr-TR");
  const filtered = memberUpdates.filter(a =>
    [a.full_name, a.email, a.title, a.company].join(" ").toLocaleLowerCase("tr-TR").includes(q)
  );

  updateCount.textContent = memberUpdates.length;

  updateRows.innerHTML = filtered.map(a => `
    <tr>
      <td>${formatDate(a.updated_at)}</td>
      <td>
        <div class="person">
          <div class="avatar">${esc(initials(a.full_name))}</div>
          <div>
            <strong>${esc(a.full_name)}</strong>
            <small>${esc(a.id)}</small>
          </div>
        </div>
      </td>
      <td>${esc(a.title || "-")}</td>
      <td>${esc(a.email || "-")}</td>
      <td>${esc(a.company || "-")}</td>
      <td><div class="row-actions"><button class="view-btn update-view-btn" data-id="${esc(a.id)}">Detaylı Gör</button><button class="delete-btn" data-delete="member-updates" data-id="${esc(a.id)}">Sil</button></div></td>
    </tr>
  `).join("");

  updateEmptyState.style.display = filtered.length ? "none" : "block";

  document.querySelectorAll(".update-view-btn").forEach(btn => {
    btn.addEventListener("click", () => openUpdateDetail(btn.dataset.id));
  });
}

function openUpdateDetail(id){
  const a = memberUpdates.find(x => x.id === id);
  if(!a) return;
  currentUpdate = a;

  document.getElementById("updateDetailName").textContent = a.full_name;

  document.getElementById("updateDetailContent").innerHTML = `
    <div class="data-section">
      <h4>İletişim Bilgileri</h4>
      <div class="data-grid">
        <div class="data-item"><span>Güncelleme Tarihi</span><strong>${formatDate(a.updated_at)}</strong></div>
        <div class="data-item"><span>Kayıt No</span><strong>${esc(a.id)}</strong></div>
        <div class="data-item"><span>Ad Soyad</span><strong>${esc(a.full_name || "-")}</strong></div>
        <div class="data-item"><span>Cep Telefonu</span><strong>${esc(a.phone || "-")}</strong></div>
        <div class="data-item"><span>E-posta Adresi</span><a href="mailto:${esc(a.email)}">${esc(a.email || "-")}</a></div>
        <div class="data-item"><span>KVKK ve Gizlilik Onayı</span><strong>${esc(a.kvkk || "-")}</strong></div>
      </div>
    </div>

    <div class="data-section">
      <h4>Profesyonel Bilgiler</h4>
      <div class="data-grid">
        <div class="data-item"><span>Kurum / Şirket</span><strong>${esc(a.company || "-")}</strong></div>
        <div class="data-item"><span>Ünvan</span><strong>${esc(a.title || "-")}</strong></div>
        <div class="data-item"><span>Sektör</span><strong>${esc(a.sector || "-")}${a.other_sector ? " — " + esc(a.other_sector) : ""}</strong></div>
        <div class="data-item"><span>LinkedIn</span><a href="${safeUrl(a.linkedin)}" target="_blank" rel="noopener noreferrer">${esc(a.linkedin || "-")}</a></div>
        <div class="data-item full"><span>Uzmanlık / İlgi Alanları</span>${tags(a.expertise)}${a.other_expertise ? `<p style="margin-top:8px">${esc(a.other_expertise)}</p>` : ""}</div>
      </div>
    </div>

    <div class="data-section">
      <h4>WBN Katılım Bilgileri</h4>
      <div class="data-grid">
        <div class="data-item"><span>WBN Etkinliklerine Daha Önce Katılım</span><strong>${esc(a.attended_events || "-")}</strong></div>
        <div class="data-item"><span>Veri Tabanını Güncel Tutma Onayı</span><strong>${esc(a.database_update_consent || "-")}</strong></div>
        <div class="data-item full"><span>Aktif Yer Almak İstediği Alanlar</span>${tags(a.active_areas)}${a.other_active_area ? `<p style="margin-top:8px">${esc(a.other_active_area)}</p>` : ""}</div>
        <div class="data-item full"><span>Ek Bilgi</span><p>${esc(a.extra_info || "-")}</p></div>
      </div>
    </div>
  `;

  listView.classList.remove("active");
  detailView.classList.remove("active");
  updatesView.classList.remove("active");
  updateDetailView.classList.add("active");
  window.scrollTo({top:0, behavior:"smooth"});
}

function openDetail(id){
  const a = applications.find(x => x.id === id);
  if(!a) return;
  currentApplication = a;

  document.getElementById("detailName").textContent = a.full_name;
  const detailStatus = document.getElementById("detailStatus");
  detailStatus.textContent = statusLabel[a.status];
  detailStatus.className = `status ${statusClass(a.status)}`;

  document.getElementById("emailPreviewName").textContent = `Merhaba ${a.full_name},`;
  document.getElementById("rejectEmailPreviewName").textContent = `Merhaba ${a.full_name},`;
  applyMailEditorTemplate("individualApproveMailEditor", a.full_name);
  applyMailEditorTemplate("individualRejectMailEditor", a.full_name);

  document.getElementById("detailContent").innerHTML = `
    <div class="data-section">
      <h4>Kişisel & İletişim Bilgileri</h4>
      <div class="data-grid">
        <div class="data-item"><span>Başvuru Tarihi</span><strong>${formatDate(a.created_at)}</strong></div>
        <div class="data-item"><span>Başvuru No</span><strong>${esc(a.id)}</strong></div>
        <div class="data-item"><span>Ad Soyad</span><strong>${esc(a.full_name || "-")}</strong></div>
        <div class="data-item"><span>Cep Telefonu</span><strong>${esc(a.phone || "-")}</strong></div>
        <div class="data-item"><span>E-posta Adresi</span><a href="mailto:${esc(a.email)}">${esc(a.email || "-")}</a></div>
        <div class="data-item"><span>Referans</span><strong>${esc(a.reference || "-")}</strong></div>
        <div class="data-item full"><span>KVKK ve Gizlilik Onayı</span><strong>${esc(a.kvkk || "-")}</strong></div>
      </div>
    </div>

    <div class="data-section">
      <h4>Profesyonel Bilgiler</h4>
      <div class="data-grid">
        <div class="data-item"><span>Kurum / Şirket</span><strong>${esc(a.company || "-")}</strong></div>
        <div class="data-item"><span>Ünvan</span><strong>${esc(a.title || "-")}</strong></div>
        <div class="data-item"><span>Sektör</span><strong>${esc(a.sector || "-")}${a.other_sector ? " — " + esc(a.other_sector) : ""}</strong></div>
        <div class="data-item"><span>LinkedIn</span><a href="${safeUrl(a.linkedin)}" target="_blank" rel="noopener noreferrer">${esc(a.linkedin || "-")}</a></div>
        <div class="data-item full"><span>Uzmanlık / İlgi Alanları</span>${tags(a.expertise)}${a.other_expertise ? `<p style="margin-top:8px">${esc(a.other_expertise)}</p>` : ""}</div>
      </div>
    </div>

    <div class="data-section">
      <h4>WBN Katılım Bilgileri</h4>
      <div class="data-grid">
        <div class="data-item full"><span>Aktif Yer Almak İstediği Alanlar</span>${tags(a.active_areas)}${a.other_active_area ? `<p style="margin-top:8px">${esc(a.other_active_area)}</p>` : ""}</div>
        <div class="data-item full"><span>WBN Veri Tabanını Güncel Tutma Onayı</span><strong>${esc(a.database_update_consent || "-")}</strong></div>
        <div class="data-item full"><span>Ek Bilgi</span><p>${esc(a.extra_info || "-")}</p></div>
      </div>
    </div>
  `;

  refreshDecisionPanel();
  listView.classList.remove("active");
  detailView.classList.add("active");
  window.scrollTo({top:0,behavior:"smooth"});
}

function refreshDecisionPanel(){
  if(!currentApplication) return;
  const a = currentApplication;

  const decisionText = document.getElementById("decisionText");
  const mailStatus = document.getElementById("mailStatus");
  const approveButton = document.getElementById("approveButton");
  const rejectButton = document.getElementById("rejectButton");
  const detailStatus = document.getElementById("detailStatus");

  if(a.status === "approved"){
    decisionText.textContent = "Başvuru onaylandı";
  }else if(a.status === "rejected"){
    decisionText.textContent = "Başvuru onaylanmadı";
  }else{
    decisionText.textContent = "Henüz değerlendirilmedi";
  }
  if(a.decided_at) decisionText.textContent += " · " + formatDate(a.decided_at);

  detailStatus.textContent = statusLabel[a.status];
  detailStatus.className = `status ${statusClass(a.status)}`;

  if(a.email_status === "sent"){
    mailStatus.textContent = "Gönderildi";
    mailStatus.className = "mail-pill mail-sent";
  }else if(a.email_status === "error"){
    mailStatus.textContent = "Gönderim Hatası";
    mailStatus.className = "mail-pill mail-error";
  }else{
    mailStatus.textContent = "Gönderilmedi";
    mailStatus.className = "mail-pill mail-not-sent";
  }

  approveButton.disabled = a.status === "rejected" || (a.status === "approved" && a.email_status !== "error");
  rejectButton.disabled = a.status === "approved" || (a.status === "rejected" && a.email_status !== "error");
}

async function approveApplication(){
  if(!currentApplication || decisionBusy) return;
  decisionBusy = true;

  try{
    const updated = await api(`/applications/${encodeURIComponent(currentApplication.id)}/approve`,{
      method:"POST",
      headers:{"Content-Type":"application/json"},
      body:JSON.stringify({email_html:getMailEditorHtml("individualApproveMailEditor")})
    });
    Object.assign(currentApplication, updated);
    refreshDecisionPanel();
    renderRows();
    showToast(updated.email_status === "sent"
      ? "Başvuru onaylandı. Onay ve bağış e-postası gönderildi."
      : "Durum güncellendi ancak e-posta gönderilemedi. Tekrar deneyebilirsiniz.");
  }catch(err){
    showToast(err.message || "İşlem sırasında hata oluştu.");
  }finally{
    decisionBusy = false;
    refreshDecisionPanel();
  }
}

async function rejectApplication(){
  if(!currentApplication || decisionBusy) return;
  decisionBusy = true;

  try{
    const updated = await api(`/applications/${encodeURIComponent(currentApplication.id)}/reject`,{
      method:"POST",
      headers:{"Content-Type":"application/json"},
      body:JSON.stringify({email_html:getMailEditorHtml("individualRejectMailEditor")})
    });
    Object.assign(currentApplication, updated);
    refreshDecisionPanel();
    renderRows();
    showToast(updated.email_status === "sent"
      ? "Başvuru durumu “Onaylanmadı” olarak güncellendi ve onaylanmama e-postası gönderildi."
      : "Durum güncellendi ancak e-posta gönderilemedi. Tekrar deneyebilirsiniz.");
  }catch(err){
    showToast(err.message || "İşlem sırasında hata oluştu.");
  }finally{
    decisionBusy = false;
    refreshDecisionPanel();
  }
}

// Silme (soft delete): sunucuda kayıt gizlenir, listeden de kaldırılır.
const DELETE_TARGETS = {
  "applications": {label: "başvuruyu", remove: id => { applications = applications.filter(x => x.id !== id); renderRows(); }},
  "member-updates": {label: "kaydı", remove: id => { memberUpdates = memberUpdates.filter(x => x.id !== id); renderUpdateRows(); }},
  "corporate-applications": {label: "kurumsal başvuruyu", remove: id => { corporateApplications = corporateApplications.filter(x => x.id !== id); renderCorporateRows(); }},
  "receipts": {label: "dekontu", remove: id => { receipts = receipts.filter(x => x.id !== id); renderReceiptRows(); }}
};

async function deleteRecord(kind, id){
  const target = DELETE_TARGETS[kind];
  if(!target || decisionBusy) return;
  if(!window.confirm(`${id} numaralı ${target.label} silmek istediğinize emin misiniz?`)) return;
  decisionBusy = true;
  try{
    await api(`/${kind}/${encodeURIComponent(id)}`, {method:"DELETE"});
    target.remove(id);
    showToast("Kayıt silindi.");
  }catch(err){
    showToast(err.message || "Silme işlemi başarısız.");
  }finally{
    decisionBusy = false;
  }
}

document.addEventListener("click", e => {
  const btn = e.target.closest("[data-delete]");
  if(btn) deleteRecord(btn.dataset.delete, btn.dataset.id);
});

function showToast(message){
  toast.textContent = message;
  toast.classList.add("show");
  clearTimeout(showToast.t);
  showToast.t = setTimeout(() => toast.classList.remove("show"),4500);
}


document.getElementById("updatesTab").addEventListener("click",()=>{
  listView.classList.remove("active");
  detailView.classList.remove("active");
  updateDetailView.classList.remove("active");
  receiptsView.classList.remove("active");
  receiptDetailView.classList.remove("active");
  corporateView.classList.remove("active");
  corporateDetailView.classList.remove("active");
  updatesView.classList.add("active");
  renderUpdateRows();
});

document.getElementById("receiptsTab").addEventListener("click",()=>{
  listView.classList.remove("active");
  detailView.classList.remove("active");
  updatesView.classList.remove("active");
  updateDetailView.classList.remove("active");
  receiptDetailView.classList.remove("active");
  corporateView.classList.remove("active");
  corporateDetailView.classList.remove("active");
  receiptsView.classList.add("active");
  renderReceiptRows();
});

document.getElementById("corporateTab").addEventListener("click",()=>{
  listView.classList.remove("active");
  detailView.classList.remove("active");
  updatesView.classList.remove("active");
  updateDetailView.classList.remove("active");
  receiptsView.classList.remove("active");
  receiptDetailView.classList.remove("active");
  corporateDetailView.classList.remove("active");
  corporateView.classList.add("active");
  renderCorporateRows();
});

document.getElementById("corporateBackButton").addEventListener("click",()=>{
  corporateDetailView.classList.remove("active");
  corporateView.classList.add("active");
});

document.getElementById("receiptBackButton").addEventListener("click",()=>{
  receiptDetailView.classList.remove("active");
  corporateView.classList.remove("active");
  corporateDetailView.classList.remove("active");
  receiptsView.classList.add("active");
});

document.getElementById("updateBackButton").addEventListener("click",()=>{
  updateDetailView.classList.remove("active");
  updatesView.classList.add("active");
});

document.getElementById("backButton").addEventListener("click",()=>{
  detailView.classList.remove("active");
  updatesView.classList.remove("active");
  updateDetailView.classList.remove("active");
  receiptsView.classList.remove("active");
  receiptDetailView.classList.remove("active");
  corporateView.classList.remove("active");
  corporateDetailView.classList.remove("active");
  listView.classList.add("active");
});
document.getElementById("applicationsTab").addEventListener("click",()=>{
  detailView.classList.remove("active");
  updatesView.classList.remove("active");
  updateDetailView.classList.remove("active");
  receiptsView.classList.remove("active");
  receiptDetailView.classList.remove("active");
  corporateView.classList.remove("active");
  corporateDetailView.classList.remove("active");
  listView.classList.add("active");
});
document.getElementById("approveButton").addEventListener("click",approveApplication);
document.getElementById("rejectButton").addEventListener("click",rejectApplication);
document.getElementById("exportApplicationsExcel").addEventListener("click", exportApplicationsToExcel);
document.getElementById("exportUpdatesExcel").addEventListener("click", exportUpdatesToExcel);
document.getElementById("exportReceiptsExcel").addEventListener("click", exportReceiptsToExcel);
document.getElementById("exportReceiptsCsv").addEventListener("click", exportReceiptsToCsv);
document.getElementById("exportCorporateExcel").addEventListener("click", exportCorporateToExcel);
document.getElementById("corporateApproveButton").addEventListener("click", approveCorporateApplication);
document.getElementById("corporateRejectButton").addEventListener("click", rejectCorporateApplication);
searchInput.addEventListener("input",renderRows);
updateSearchInput.addEventListener("input",renderUpdateRows);
receiptSearchInput.addEventListener("input",renderReceiptRows);
corporateSearchInput.addEventListener("input", renderCorporateRows);

// Menüde açık olan sekmeyi vurgula
const navTabs = ["applicationsTab", "updatesTab", "receiptsTab", "corporateTab"];
function setActiveTab(activeId){
  navTabs.forEach(id => {
    const tab = document.getElementById(id);
    tab.classList.toggle("is-active", id === activeId);
    if(id === activeId) tab.setAttribute("aria-current", "page");
    else tab.removeAttribute("aria-current");
  });
}
navTabs.forEach(id => document.getElementById(id).addEventListener("click", () => setActiveTab(id)));
setActiveTab("applicationsTab");

initMailEditors();
loadAll();
})();
