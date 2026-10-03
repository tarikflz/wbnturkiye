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
