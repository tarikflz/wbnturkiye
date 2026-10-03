<?php
declare(strict_types=1);

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_WINDOW_SECONDS = 900;

function session_start_secure(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('wbn_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    session_start();

    // 2 saat işlem yapılmazsa oturumu kapat
    if (isset($_SESSION['last_seen']) && time() - $_SESSION['last_seen'] > 7200) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['last_seen'] = time();
}

function current_admin(): ?string
{
    session_start_secure();
    return $_SESSION['admin_username'] ?? null;
}

/** Başarılıysa true; hatalı denemeleri IP bazında sayar. */
function attempt_login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Kullanıcı yoksa da hash doğrulaması yaparak zamanlama farkını gizle
    $hash = $user ? $user['password_hash'] : password_hash(random_bytes(16), PASSWORD_DEFAULT);
    if (!password_verify($password, $hash) || !$user) {
        rate_limit_record('login');
        return false;
    }

    rate_limit_reset('login');
    session_start_secure();
    session_regenerate_id(true);
    $_SESSION['admin_username'] = $user['username'];
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    db()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
    return true;
}

function login_locked(): bool
{
    return rate_limit_hit('login', LOGIN_MAX_ATTEMPTS, LOGIN_WINDOW_SECONDS);
}

function logout(): void
{
    session_start_secure();
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    session_destroy();
}

function csrf_token(): string
{
    session_start_secure();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    session_start_secure();
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
