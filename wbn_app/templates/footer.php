<footer class="site-footer">
  <div class="container footer-community">
    <p class="kicker">Topluluğa Katılın</p>
    <p class="lead">
      Güçlü bağlar kurmak, deneyimlerinizi paylaşmak ve geleceğin kadın liderlerine birlikte değer katmak için sizi de WBN Türkiye topluluğuna bekliyoruz.
    </p>
    <img class="footer-logo" src="<?= e(asset('img/wbn-logo.png')) ?>" alt="WBN Türkiye logosu" width="164" height="76" loading="lazy">
  </div>

  <div class="container footer-social-only">
    <p class="footer-social-description">
      Türkiye'nin finans ve teknoloji başta olmak üzere tüm sektörlerden kadın liderleri buluşturan etkileşim platformu.
    </p>

    <div class="social-links" aria-label="Sosyal medya hesapları">
      <a class="social-link" href="https://www.linkedin.com/company/wbn-turkiye/posts/?feedView=all" target="_blank" rel="noopener noreferrer" aria-label="WBN Türkiye LinkedIn hesabı">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M4.98 3.5C4.98 4.88 3.87 6 2.49 6A2.5 2.5 0 1 1 4.98 3.5ZM.36 8.04h4.26V21H.36V8.04ZM7.34 8.04h4.08v1.77h.06c.57-1.08 1.96-2.22 4.03-2.22 4.31 0 5.11 2.84 5.11 6.53V21h-4.25v-6.1c0-1.46-.03-3.33-2.03-3.33-2.03 0-2.34 1.58-2.34 3.22V21H7.34V8.04Z"/>
        </svg>
      </a>
      <a class="social-link" href="https://www.instagram.com/wbuzznetwork/" target="_blank" rel="noopener noreferrer" aria-label="WBN Türkiye Instagram hesabı">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2Zm-.2 2A3.6 3.6 0 0 0 4 7.6v8.8A3.6 3.6 0 0 0 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6A3.6 3.6 0 0 0 16.4 4H7.6Zm9.65 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
        </svg>
      </a>
    </div>

    <nav class="footer-links" aria-label="Yasal bağlantılar">
      <a href="/kvkk">KVKK ve Gizlilik</a>
    </nav>
  </div>

  <div class="footer-bottom">
    <div class="container">Telif Hakkı @<?= date('Y') ?>. Tüm Hakları Saklıdır © WBN Türkiye</div>
  </div>

  <div class="footer-support">
    <div class="container footer-support-inner">
      <span>WBN Türkiye, Inspira7 tarafından desteklenmektedir.</span>
      <a href="https://inspira7.com/" target="_blank" rel="noopener noreferrer" aria-label="Inspira7">
        <img class="footer-support-logo" src="<?= e(asset('img/inspira7.png')) ?>" alt="Inspira7 logosu" width="92" height="15" loading="lazy">
      </a>
    </div>
  </div>
</footer>

<script src="<?= e(asset('js/nav.js')) ?>" defer></script>
<?php foreach ($page['js'] ?? [] as $js): ?>
<script src="<?= e(asset("js/$js.js")) ?>"></script>
<?php endforeach; ?>
</body>

</html>
