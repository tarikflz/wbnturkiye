<?php
require dirname(__DIR__) . '/_app.php';

header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

if (current_admin()) {
    header('Location: /yonetim/');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $error = 'Oturum süresi doldu. Lütfen tekrar deneyin.';
    } elseif (login_locked()) {
        $error = 'Çok fazla hatalı deneme yapıldı. Lütfen 15 dakika sonra tekrar deneyin.';
    } elseif ($username === '' || $password === '' || !attempt_login($username, $password)) {
        $error = 'Kullanıcı adı veya şifre hatalı.';
    } else {
        header('Location: /yonetim/');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>WBN Türkiye | Yönetim Paneli Girişi</title>
  <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">
  <link rel="icon" type="image/png" sizes="512x512" href="<?= e(asset('img/favicon.png')) ?>">
  <link rel="apple-touch-icon" href="<?= e(asset('img/favicon.png')) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset('css/base.css')) ?>">
  <style>
    body {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
      background: radial-gradient(circle at 20% 10%, var(--purple-800), var(--purple-950) 60%);
    }
    .login-card {
      width: min(100%, 420px);
      padding: 40px 36px;
      border-radius: 28px;
      background: #fff;
      box-shadow: 0 30px 80px rgba(0, 0, 0, .35);
    }
    .login-logo {
      width: 120px;
      margin: 0 auto 22px;
      padding: 12px 16px;
      border-radius: 16px;
      background: var(--purple-950);
    }
    .login-card h1 {
      margin: 0 0 6px;
      text-align: center;
      color: var(--purple-950);
      font-family: "Playfair Display", serif;
      font-size: 28px;
    }
    .login-card p { margin: 0 0 26px; text-align: center; color: var(--muted); font-size: 14px; }
    .login-card label { display: block; margin: 0 0 7px; font-size: 13px; font-weight: 700; color: var(--purple-950); }
    .login-card input {
      width: 100%;
      margin-bottom: 18px;
      padding: 13px 15px;
      border: 1px solid var(--line);
      border-radius: 14px;
      font: inherit;
      background: var(--off-white);
    }
    .login-card input:focus { outline: 2px solid var(--purple-500); border-color: transparent; }
    .login-card button {
      width: 100%;
      padding: 14px;
      border: 0;
      border-radius: 999px;
      color: #fff;
      background: linear-gradient(135deg, var(--purple-800), var(--purple-600));
      font: inherit;
      font-weight: 700;
      cursor: pointer;
    }
    .login-card .form-error { margin: 0 0 18px; }
  </style>
</head>
<body>
  <main class="login-card">
    <img class="login-logo" src="<?= e(asset('img/wbn-logo.png')) ?>" alt="WBN Türkiye">
    <h1>Yönetim Paneli</h1>
    <p>Devam etmek için giriş yapın.</p>

<?php if ($error): ?>
    <div class="form-error" role="alert"><?= e($error) ?></div>
<?php endif; ?>

    <form method="post" action="/yonetim/login.php">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <label for="username">Kullanıcı Adı</label>
      <input id="username" name="username" type="text" autocomplete="username" value="<?= e($username) ?>" required autofocus>
      <label for="password">Şifre</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>
      <button type="submit">Giriş Yap</button>
    </form>
  </main>
</body>
</html>
