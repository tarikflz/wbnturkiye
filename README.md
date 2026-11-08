# WBN Türkiye website

Pure PHP 8.1+ and MySQL site for WBN Türkiye, built from the HTML pages the customer delivered (kept unchanged in `original/`). There is no framework; the only dependency is PHPMailer, which is committed in `wbn_app/vendor/`, so the server doesn't need Composer.

## Layout

```
public_html/   → upload to ~/public_html (web root)
wbn_app/       → upload to ~/wbn_app (next to public_html, NOT inside it)
original/      → customer's original HTML files (reference only, do not upload)
dev-router.php → local development only (do not upload)
```

`public_html/_app.php` points to `../wbn_app`, and every page, the API and the admin panel load the app through it. If the app folder lives somewhere else, change only that line.

## Local development

```bash
brew install mariadb && brew services start mariadb
mariadb -u "$(whoami)" -e "CREATE DATABASE wbn_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'wbn'@'localhost' IDENTIFIED BY 'wbn_local_pw'; GRANT ALL ON wbn_local.* TO 'wbn'@'localhost';"
mariadb -u wbn -pwbn_local_pw wbn_local < wbn_app/migrations/001_schema.sql

cp wbn_app/config.example.php wbn_app/config.php   # set env=development, base_url=http://localhost:8000,
                                                   # db credentials, mail.transport=log
php wbn_app/bin/create-admin.php admin --apply      # create an admin user
php -S localhost:8000 -t public_html dev-router.php
```

With `mail.transport = 'log'`, emails are written to `wbn_app/storage/logs/mail.log` instead of being sent.

## Deploying to cPanel

1. **PHP:** cPanel → *Select PHP Version* → 8.1 or newer, with `pdo_mysql`, `fileinfo` and `mbstring` enabled.
2. **Database:** cPanel → *MySQL Databases* → create a database and user and grant all privileges. Then phpMyAdmin → select the database → *Import* → `wbn_app/migrations/001_schema.sql`. Then import `wbn_app/migrations/002_soft_delete.sql` (adds `deleted_at`/`deleted_by` columns used by the admin panel's delete button; run once).
3. **Upload** `public_html/*` (including `.htaccess`) into `~/public_html` and `wbn_app/` to `~/wbn_app`. You can use cPanel *Git Version Control* or FTP.
4. **Config:** copy `~/wbn_app/config.example.php` to `~/wbn_app/config.php` and fill in:
   - `db.*`: the database from step 2 (cPanel prefixes the names, e.g. `cpuser_wbn`).
   - `mail.password`: a Google **App Password** for wbnturkiye@gmail.com. The account needs 2-step verification: Google Account → Security → App passwords.
   - Keep `app.env = production` and `mail.transport = smtp`.
5. **Writable folders:** `~/wbn_app/storage/receipts` and `~/wbn_app/storage/logs` must be writable by PHP (755 is usually enough on cPanel).
6. **Admin user:** run `php wbn_app/bin/create-admin.php <username>` on your own computer, enter a password (12+ characters), and run the printed `INSERT` in phpMyAdmin → *SQL*.
7. **HTTPS:** cPanel → *SSL/TLS Status* → run AutoSSL. `.htaccess` redirects HTTP to HTTPS.
8. **Remove the old files:** delete the uploaded `*.html` files from `public_html`. Their old URLs already 301-redirect to the new pages.
9. **Test email:** submit the contact form on the home page and check that both Gmail inboxes receive it. If nothing arrives, check `~/wbn_app/storage/logs/php-error.log`. Some shared hosts block outgoing SMTP on port 587; in that case, ask the host to allow it, or create a cPanel mailbox and use its SMTP settings.

## Pages and forms

| URL | Form → table → admin section |
|---|---|
| `/` | Contact form → `contact_messages` (emailed only, not shown in admin) |
| `/hakkimizda`, `/kimler-katilabilir`, `/kvkk` | Content pages |
| `/uyelik-basvurusu` | → `applications` → *Yeni Üye Başvuruları* (approve/reject sends an email) |
| `/uye-bilgi-guncelleme` | → `member_updates` → *Üye Bilgi Güncellemeleri* |
| `/kurumsal-uyelik` | → `corporate_applications` → *Kurumsal Üye Başvuruları* (approve/reject sends an email) |
| `/dekont-gonder` | → `receipts` → *Dekontlar*. Hidden page: not linked anywhere, `noindex`, reached only from approval emails |
| `/yonetim/` | Admin panel (username and password login) |

Every form notifies the addresses in `config.php` → `notify.default` (wbnturkiye@gmail.com, wbuzznetwork@gmail.com). Receipts go to `notify.receipts` (those two plus tulin.cakmak@novaformdan.com), with the file attached.
