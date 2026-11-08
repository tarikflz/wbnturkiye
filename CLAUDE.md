# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Website for **WBN Türkiye** (a women's leadership network) in pure PHP 8.1+ and MySQL, deployed to shared cPanel hosting. There is no framework and no build step; PHPMailer is the only dependency and is committed in `wbn_app/vendor/`. All site copy, UI text and code comments are in **Turkish**; keep new copy and comments in Turkish. `README.md` has the deployment steps.

`original/` holds the customer's original standalone HTML files, kept for reference only. Never edit or deploy them. `wbn-not.md` contains the customer's requirements.

## Commands

```bash
# Local server (the built-in server ignores .htaccess; dev-router.php imitates it)
php -S localhost:8000 -t public_html dev-router.php

# Alternative: docker compose up -d --build (http://localhost:8080, Apache + real .htaccess, config.docker.php via WBN_CONFIG)
# Local DB (MariaDB via Homebrew): db wbn_local, user wbn / wbn_local_pw
mariadb -u wbn -pwbn_local_pw wbn_local < wbn_app/migrations/001_schema.sql

# Create an admin user (prints SQL; --apply writes it to the configured DB)
php wbn_app/bin/create-admin.php <username> [--apply]

# Lint every PHP file
find public_html wbn_app/src wbn_app/templates wbn_app/bin -name '*.php' -exec php -l {} \;
```

There is no test suite. Verify changes with curl against `/api/*` and by loading the pages in a browser. Locally, `wbn_app/config.php` has `mail.transport = 'log'`, so emails are written to `wbn_app/storage/logs/mail.log`. PHP errors go to `wbn_app/storage/logs/php-error.log`. The form rate limit is 10 per IP per hour; clear it with `TRUNCATE rate_limits` when testing repeatedly.

## Architecture

- **Two deploy roots.** `public_html/` is the web root. `wbn_app/` sits next to it on the server, outside the web root, and holds config, `src/`, templates, migrations, uploads (`storage/receipts`) and logs. Every entry point, including `api/index.php` and `yonetim/*.php`, loads the app through `public_html/_app.php`, which is the only file that knows the path to `wbn_app`. `wbn_app/bootstrap.php` loads `config.php` (gitignored; copy it from `config.example.php`) and auto-requires every file in `wbn_app/src/`.
- **Page layout.** Each public page (`public_html/<slug>.php`) calls `layout_start([...])` with its title, description, `css`, `js`, `active`, `fonts` and `noindex`, prints its own `<main>`, then calls `layout_end()`. These come from `wbn_app/src/view.php` and render `templates/head.php`, `header.php` and `footer.php`. The menu is the `NAV_ITEMS` tree in `view.php`: four top-level entries (Hakkımızda, Üyelik, Etkinlikler, İş Birliklerimiz) with dropdown children, where a `null` href renders a non-clickable placeholder; KVKK appears only in the footer. The `PARTNERS` constant in `view.php` feeds the logos on the `/is-birliklerimiz` page via `partner_logos()` (files `assets/img/{1_inspira7,2_odarama,3_nodyo}.png`).
- **CSS.** `assets/css/base.css` holds the shared tokens, header, footer and form status styles. Page CSS in `assets/css/pages/` is the original page CSS with the footer rules stripped out, loaded after `base.css`. `content.css` is shared by Hakkımızda and Kimler Katılabilir; `member-form.css` and `js/pages/member-form.js` are shared by the two member forms. Use `asset()` for URLs; it adds a cache-busting `?v=`.
- **Clean URLs** come from `public_html/.htaccess`: `/slug` serves `slug.php`, `.php` URLs and the old Turkish-named `.html` files 301-redirect to their slugs, and `/api/*` goes to `api/index.php`. Keep `dev-router.php` in sync when the rules change.
- **Forms → API → DB → admin.** Every public form has `data-api="/api/..."` and is submitted by `assets/js/forms.js` (fetch + FormData, JSON response, inline field errors, an optional `data-success`/`data-hide` target). Page scripts can block a submission by listening for `wbn:beforesubmit` and calling `preventDefault()`. On the server, `wbn_app/src/forms.php` holds one handler per form: honeypot `_hp` → rate limit → `validate()` (`src/validate.php`) → `db_insert()` (also sets `ref`, e.g. `WBN-2026-001`) → notification email via `send_mail()`. The checkbox and select option lists are PHP constants in `forms.php`; the member form pages render their options from them, so the whitelist and the HTML can't drift apart.
- **Admin (`/yonetim/`).** Session login against `admin_users` (`src/auth.php`). `yonetim/index.php` is the customer's panel markup; `yonetim/assets/admin.js` loads data from the GET endpoints and sends `X-CSRF-Token` (from a `<meta>` tag) on POSTs. Admin list endpoints return the field names `admin.js` expects, built in `src/admin.php`, and the `id` they expose is the table's `ref`. Approve/reject (`decide_application()`) is allowed once per application; it can only be repeated with the same decision after an email error. The admin's edited email HTML goes through `sanitize_mail_html()`, which also turns the `.placeholder-link` span into the `/dekont-gonder` link and styles the `.donation-preview` button. In `admin.js`, always pass applicant data through `esc()`/`safeUrl()` before putting it into `innerHTML`.
- **Soft delete.** The panel's "Sil" button calls `DELETE /api/<applications|member-updates|corporate-applications|receipts>/<ref>`, which sets `deleted_at`/`deleted_by` (migration `002_soft_delete.sql`). Every list and lookup query filters `deleted_at IS NULL`; rows and receipt files are kept on disk. Approve/reject return and store `decided_at`/`decided_by`, shown in the panel as "Karar Tarihi".
- **Email** uses PHPMailer over Gmail SMTP (wbnturkiye@gmail.com with an App Password). Recipients are configured in `config.php` → `notify.default` and `notify.receipts`; receipts also go to tulin.cakmak@novaformdan.com, with the file attached.
- `/dekont-gonder` must stay hidden: no links to it on the site and `noindex`. Applicants reach it only from approval emails.
