// Mobil menü aç/kapat
(function () {
  const toggle = document.querySelector('.site-nav__toggle');
  const nav = document.getElementById('siteNav');
  if (!toggle || !nav) return;

  function setOpen(open) {
    nav.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç');
  }

  toggle.addEventListener('click', function () {
    setOpen(!nav.classList.contains('is-open'));
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') setOpen(false);
  });

  window.matchMedia('(min-width: 1101px)').addEventListener('change', function (mq) {
    if (mq.matches) setOpen(false);
  });
})();

// Alt menü: dokunmatik cihazlarda üst başlığa tıklayınca açılır
(function () {
  const parents = document.querySelectorAll('.site-nav__parent');
  parents.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const item = btn.closest('.site-nav__item');
      const open = !item.classList.contains('is-open');
      document.querySelectorAll('.site-nav__item.is-open').forEach(function (el) { el.classList.remove('is-open'); });
      item.classList.toggle('is-open', open);
      btn.setAttribute('aria-expanded', String(open));
    });
  });
  document.addEventListener('click', function (event) {
    if (event.target.closest('.site-nav__item')) return;
    document.querySelectorAll('.site-nav__item.is-open').forEach(function (el) { el.classList.remove('is-open'); });
  });
})();
