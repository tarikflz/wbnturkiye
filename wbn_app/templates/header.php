<?php $active = $GLOBALS['page']['active'] ?? ''; ?>
<header class="site-header">
  <div class="container site-header__inner">
    <a class="site-header__logo" href="/" aria-label="WBN Türkiye ana sayfa">
      <img src="<?= e(asset('img/wbn-logo.png')) ?>" alt="WBN Türkiye" width="120" height="55">
    </a>

    <button class="site-nav__toggle" type="button" aria-expanded="false" aria-controls="siteNav" aria-label="Menüyü aç">
      <span></span><span></span><span></span>
    </button>

    <nav class="site-nav" id="siteNav" aria-label="Ana menü">
      <ul>
<?php foreach (NAV_ITEMS as $key => [$href, $label]): ?>
        <li><a href="<?= e($href) ?>"<?= $key === $active ? ' class="is-active" aria-current="page"' : '' ?>><?= e($label) ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>
  </div>
</header>
