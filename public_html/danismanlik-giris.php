<?php
require __DIR__ . '/_app.php';

// E-postadaki bağlantı buraya gelir. Token yalnızca butona basılınca tüketilir
// (e-posta tarayıcıları bağlantıyı önceden açsa da bağlantı yanmaz).
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');

layout_start([
    'footer_cta' => false,
    'title' => 'WBN Türkiye | Giriş',
    'description' => 'Giriş bağlantısı.',
    'css' => ['pages/member-form'],
    'noindex' => true,
    'active' => 'danismanlik',
]);
?>
<main id="main" tabindex="-1" class="form-section" style="padding-top:80px;padding-bottom:80px">
  <div class="container form-shell" style="grid-template-columns:minmax(0,560px);justify-content:center">
    <section class="form-card" style="text-align:center">
      <div class="form-heading">
        <h2>Giriş</h2>
        <p id="consultingMsg">E-postanız doğrulandı. Devam ettiğinizde danışmanlık platformuna yönlendirileceksiniz.</p>
      </div>
      <button class="submit-button" id="consultingGo" type="button">Giriş Yap</button>
    </section>
  </div>
</main>
<script>
(function () {
  const token = new URLSearchParams(location.search).get('token') || '';
  const btn = document.getElementById('consultingGo');
  const msg = document.getElementById('consultingMsg');
  if (!token) { msg.textContent = 'Bağlantı geçersiz. Lütfen yeni bir giriş bağlantısı isteyin.'; btn.textContent = 'Yeni Bağlantı İste'; btn.onclick = () => location.href = '/danismanlik'; return; }
  history.replaceState(null, '', location.pathname); // token adres çubuğunda kalmasın
  btn.addEventListener('click', async () => {
    btn.disabled = true; btn.textContent = 'Yönlendiriliyor...';
    try {
      const r = await fetch('/api/danismanlik/continue', { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify({ token }) });
      const d = await r.json().catch(() => ({}));
      if (r.ok && d.ok && /^https:\/\//.test(d.redirect)) { location.href = d.redirect; return; }
      msg.textContent = d.message || 'İşlem tamamlanamadı.';
    } catch (e) { msg.textContent = 'Bağlantı hatası oluştu. Lütfen tekrar deneyin.'; }
    btn.disabled = false; btn.textContent = 'Tekrar Dene';
    if (btn.dataset.retry !== '1') { btn.dataset.retry = '1'; }
  });
})();
</script>
<?php layout_end(); ?>
