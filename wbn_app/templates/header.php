<?php $active = $GLOBALS['page']['active'] ?? ''; ?>
<a class="skip-link" href="#main">İçeriğe geç</a>
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
<?php foreach (NAV_ITEMS as $key => [$href, $label, $children]): ?>
<?php
    $childActive = isset($children[$active]);
    $isActive = $key === $active || $childActive;
?>
        <li class="site-nav__item<?= $children ? ' has-sub' : '' ?>">
<?php if ($href !== null): ?>
          <a href="<?= e($href) ?>"<?= $isActive ? ' class="is-active"' : '' ?><?= $key === $active ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
<?php else: ?>
          <button type="button" class="site-nav__parent<?= $isActive ? ' is-active' : '' ?>" aria-expanded="false"><?= e($label) ?></button>
<?php endif; ?>
<?php if ($children): ?>
          <ul class="site-nav__sub">
<?php foreach ($children as $childKey => [$childHref, $childLabel]): ?>
<?php if ($childHref !== null): ?>
            <li><a href="<?= e($childHref) ?>"<?= $childKey === $active ? ' class="is-active" aria-current="page"' : '' ?>><?= e($childLabel) ?></a></li>
<?php else: ?>
            <li><span class="is-disabled" aria-disabled="true"><?= e($childLabel) ?></span></li>
<?php endif; ?>
<?php endforeach; ?>
          </ul>
<?php endif; ?>
        </li>
<?php endforeach; ?>
<?php if (login_enabled()): ?>
        <li class="site-nav__item site-nav__login">
          <a class="site-nav__login-link<?= $active === 'danismanlik' ? ' is-active' : '' ?>" href="/danismanlik"<?= $active === 'danismanlik' ? ' aria-current="page"' : '' ?>>Giriş Yap</a>
        </li>
<?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
