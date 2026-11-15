// Nodyo bölümleri (Nodyo Üyeleri, Nodyo Görüşmeleri). admin.js'ten bağımsızdır;
// diğer sekmeler tıklanınca kendi görünümlerini kapatır.
(function(){
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  const esc = v => String(v ?? "").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
  const fmt = v => v ? new Date(v).toLocaleString("tr-TR",{day:"2-digit",month:"2-digit",year:"numeric",hour:"2-digit",minute:"2-digit"}) : "-";
  const initials = n => String(n||"?").trim().split(/\s+/).slice(0,2).map(w=>w[0]).join("").toLocaleUpperCase("tr-TR");

  const views = ["nodyoView","nodyoMeetView"].map(id => document.getElementById(id));
  const tabs = {nodyoTab:"nodyoView", nodyoMeetTab:"nodyoMeetView"};
  let members = [];

  async function call(path, options = {}){
    const r = await fetch("/api" + path, {credentials:"same-origin", ...options,
      headers:{"Accept":"application/json","X-CSRF-Token":CSRF}});
    if(r.status === 401){ location.href = "/yonetim/login.php"; throw new Error("Oturum süresi doldu."); }
    const d = await r.json().catch(() => ({}));
    if(!r.ok) throw new Error(d.message || "İşlem tamamlanamadı.");
    return d;
  }

  function render(){
    const q = document.getElementById("nodyoSearchInput").value.trim().toLocaleLowerCase("tr-TR");
    const list = members.filter(m => !q || (m.full_name + " " + m.email).toLocaleLowerCase("tr-TR").includes(q));
    document.getElementById("nodyoCount").textContent = members.length;
    document.getElementById("nodyoRows").innerHTML = list.map(m => `
      <tr>
        <td>${fmt(m.created_at)}</td>
        <td><div class="person"><div class="avatar">${esc(initials(m.full_name))}</div><div><strong>${esc(m.full_name)}</strong><small>${esc(m.id)}</small></div></div></td>
        <td>${esc(m.email)}</td>
        <td>${m.email_verified_at ? "Doğrulandı · " + fmt(m.email_verified_at) : "Bekliyor"}</td>
        <td>${m.nodyo_user_id ? esc(m.account_status || "Açıldı") : "Henüz açılmadı"}</td>
        <td>${fmt(m.last_sso_at)}</td>
        <td>${m.sso_count}</td>
        <td><div class="row-actions"><button class="delete-btn" data-nodyo-delete="${esc(m.id)}">Sil</button></div></td>
      </tr>`).join("");
    document.getElementById("nodyoEmptyState").style.display = list.length ? "none" : "block";
  }

  async function load(){
    try{ members = await call("/nodyo-members"); render(); }
    catch(err){ const t = document.getElementById("toast"); t.textContent = err.message; t.classList.add("show"); setTimeout(() => t.classList.remove("show"), 4500); }
  }

  function showOnly(viewId){
    document.querySelectorAll("#listView,.view").forEach(v => v.classList.remove("active"));
    if(viewId) document.getElementById(viewId).classList.add("active");
  }

  document.querySelectorAll(".sidebar .nav-item, .nav-item").forEach(tab => {
    tab.addEventListener("click", () => {
      const mine = tabs[tab.id];
      if(mine){
        showOnly(mine);
        document.querySelectorAll(".nav-item").forEach(t => { t.classList.toggle("is-active", t === tab); t.toggleAttribute("aria-current", t === tab); });
        if(tab.id === "nodyoTab") load();
      }else{
        views.forEach(v => v.classList.remove("active")); // diğer sekmenin kendi handler'ı kendi görünümünü açar
      }
    });
  });

  document.getElementById("nodyoSearchInput").addEventListener("input", render);
  document.addEventListener("click", async e => {
    const btn = e.target.closest("[data-nodyo-delete]");
    if(!btn) return;
    const id = btn.dataset.nodyoDelete;
    if(!confirm(`${id} numaralı Nodyo kaydını silmek istediğinize emin misiniz?`)) return;
    try{ await call("/nodyo-members/" + encodeURIComponent(id), {method:"DELETE"}); members = members.filter(m => m.id !== id); render(); }
    catch(err){ alert(err.message); }
  });

  load(); // rozet sayısı için
})();
