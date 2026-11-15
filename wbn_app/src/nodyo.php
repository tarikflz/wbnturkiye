<?php
declare(strict_types=1);

// Nodyo Partner API entegrasyonu. Akış: form → e-posta doğrulama bağlantısı → Nodyo'da hesap aç/bul → SSO yönlendirmesi.
// Not: POST /users aynı e-posta için mevcut kullanıcıyı döndürür; bu yüzden SSO'ya yalnızca e-postası doğrulanmış kişiler geçirilir.

const NODYO_TOKEN_TTL = 1800;      // doğrulama bağlantısı geçerlilik süresi (sn)
const NODYO_MAIL_LIMIT = 3;        // aynı e-postaya saatte en fazla bağlantı

/** Nodyo API çağrısı. [HTTP durumu, çözümlenmiş JSON] döner; ağ hatasında 502 HttpError fırlatır. */
function nodyo_request(string $method, string $path, ?array $body = null): array
{
    $key = (string) config('nodyo.api_key');
    if ($key === '') {
        throw new HttpError(503, 'Hizmet henüz yapılandırılmadı.');
    }

    $headers = ['Accept: application/json', 'Content-Type: application/json', 'X-API-Key: ' . $key];
    // Staging ortamı Cloudflare Zero Trust arkasındadır: servis token'ı tanımlıysa eklenir.
    if (config('nodyo.access_client_id') && config('nodyo.access_client_secret')) {
        $headers[] = 'CF-Access-Client-Id: ' . config('nodyo.access_client_id');
        $headers[] = 'CF-Access-Client-Secret: ' . config('nodyo.access_client_secret');
    }

    $ch = curl_init(rtrim((string) config('nodyo.base_url'), '/') . $path);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POSTFIELDS => $body === null ? null : json_encode($body, JSON_UNESCAPED_UNICODE),
    ]);
    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        error_log("Nodyo $method $path ağ hatası: $err");
        throw new HttpError(502, 'Hizmete bağlanılamadı. Lütfen daha sonra tekrar deneyin.');
    }
    $data = json_decode((string) $raw, true);
    if ($status >= 500 || ($status >= 400 && !in_array($status, [404, 409, 422], true))) {
        error_log("Nodyo $method $path -> $status: " . substr((string) $raw, 0, 300));
    }
    return [$status, is_array($data) ? $data : []];
}

/** Addan Nodyo kullanıcı adı üretir: küçük harf, rakam, nokta; 4-32 karakter (ör. "Tarık Filiz" → tarik.filiz). */
function nodyo_username(string $fullName, string $suffix = ''): string
{
    $ascii = strtr($fullName, ['ı' => 'i', 'İ' => 'i', 'ş' => 's', 'Ş' => 's', 'ğ' => 'g', 'Ğ' => 'g',
        'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c']);
    $ascii = strtolower((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $ascii));
    $words = preg_split('/[^a-z0-9]+/', $ascii, -1, PREG_SPLIT_NO_EMPTY);
    $base = implode('.', $words) ?: 'wbn.uye';
    $base = substr($base, 0, 32 - strlen($suffix));
    $name = rtrim($base, '.') . $suffix;
    return strlen($name) >= 4 ? $name : $name . 'wbn';
}

/**
 * Yeni hesabı onboarding'den çıkarır (kullanıcı adı, ülke, saat dilimi, dil), böylece kullanıcı Nodyo'da profil formu görmez.
 * Başarısız olursa girişi engellemez: kullanıcı formu Nodyo'da kendisi doldurur. Başarılıysa true döner.
 */
function nodyo_onboard(string $userId, string $fullName): bool
{
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $suffix = $attempt === 0 ? '' : (string) random_int(10, 9999);
        [$status] = nodyo_request('POST', '/users/' . rawurlencode($userId) . '/onboarding', [
            'username' => nodyo_username($fullName, $suffix),
            'country' => 'TR',
            'timezone' => 'Europe/Istanbul',
            'languages' => ['tr'],
        ]);
        if ($status === 200) {
            return true;
        }
        if ($status !== 409 && $status !== 400) {
            break; // kullanıcı adı çakışması dışında tekrar denemenin anlamı yok
        }
    }
    error_log('Nodyo onboarding tamamlanamadı: ' . $userId);
    return false;
}

/** Üyelik formu: kayıt/giriş için doğrulama bağlantısı gönderir. Yanıt, e-postanın kayıtlı olup olmadığını belli etmez. */
function handle_nodyo_signup(array $input): array
{
    guard_public_form($input);
    $d = validate($input, [
        'full_name' => ['required', 'max:64'],
        'email' => ['required', 'email', 'max:190'],
        'kvkk' => ['accepted'],
    ]);
    if (mb_strlen($d['full_name']) < 2) {
        throw new HttpError(422, 'Lütfen işaretli alanları kontrol edin.', ['full_name' => 'En az 2 karakter olmalıdır.']);
    }
    $email = mb_strtolower($d['email']);

    $stmt = db()->prepare('SELECT * FROM nodyo_members WHERE email = ?');
    $stmt->execute([$email]);
    $member = $stmt->fetch();

    if (!$member) {
        [$id] = db_insert('nodyo_members', [
            'full_name' => $d['full_name'], 'email' => $email, 'kvkk' => 1, 'ip' => client_ip(),
        ], 'NDY');
        $isNew = true;
    } else {
        $id = (int) $member['id'];
        $isNew = false;
        if ($member['deleted_at'] !== null) {
            db()->prepare('UPDATE nodyo_members SET deleted_at = NULL, deleted_by = NULL WHERE id = ?')->execute([$id]);
        }
    }

    $recent = db()->prepare('SELECT COUNT(*) FROM nodyo_login_tokens WHERE member_id = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
    $recent->execute([$id]);
    if ((int) $recent->fetchColumn() < NODYO_MAIL_LIMIT) {
        $token = bin2hex(random_bytes(32));
        db()->prepare('INSERT INTO nodyo_login_tokens (member_id, token_hash, expires_at) VALUES (?, ?, NOW() + INTERVAL ? SECOND)')
            ->execute([$id, hash('sha256', $token), NODYO_TOKEN_TTL]);

        $name = $member['full_name'] ?? $d['full_name'];
        $link = url('/danismanlik-giris?token=' . $token);
        send_mail([$email], 'WBN Türkiye | Giriş Bağlantınız', mail_layout(
            '<p>Merhaba ' . e($name) . ',</p>'
            . '<p>Uzman danışmanlığı hizmetine erişmek için aşağıdaki bağlantıyı kullanın. Bağlantı tek kullanımlıktır ve 30 dakika geçerlidir.</p>'
            . '<p style="margin:24px 0"><a href="' . e($link) . '" style="background:#3A1044;color:#fff;padding:12px 22px;border-radius:999px;text-decoration:none;font-weight:bold">Giriş Yap</a></p>'
            . '<p style="font-size:13px;color:#6F5D76">Bu talebi siz yapmadıysanız bu e-postayı yok sayabilirsiniz.</p>'
        ));
        if ($isNew) {
            send_mail(config('notify.default'), 'WBN Türkiye | Yeni Nodyo Kaydı',
                mail_table('Yeni Nodyo Kaydı', ['Ad Soyad' => $d['full_name'], 'E-posta' => $email]));
        }
    }

    return ['ok' => true, 'message' => 'Doğrulama bağlantısı ' . $email . ' adresine gönderildi. Lütfen e-postanızı kontrol edin (gelmezse spam klasörüne bakın).'];
}

/** Doğrulama bağlantısındaki "Devam" butonu: tokenı tüketir, Nodyo hesabını hazırlar ve SSO adresini döner. */
function handle_nodyo_continue(array $input): array
{
    if (rate_limit_hit('nodyo-continue', 20, 3600)) {
        throw new HttpError(429, 'Çok fazla deneme yaptınız. Lütfen bir süre sonra tekrar deneyin.');
    }
    rate_limit_record('nodyo-continue');

    $token = (string) ($input['token'] ?? '');
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        throw new HttpError(400, 'Bağlantı geçersiz.');
    }
    $hash = hash('sha256', $token);

    // Tokenı atomik olarak tüket; Nodyo çağrısı başarısız olursa geri bırakılır.
    $claim = db()->prepare('UPDATE nodyo_login_tokens SET used_at = NOW() WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()');
    $claim->execute([$hash]);
    if ($claim->rowCount() === 0) {
        throw new HttpError(410, 'Bağlantının süresi dolmuş veya daha önce kullanılmış. Lütfen yeniden giriş bağlantısı isteyin.');
    }

    try {
        $stmt = db()->prepare('SELECT m.* FROM nodyo_members m JOIN nodyo_login_tokens t ON t.member_id = m.id WHERE t.token_hash = ?');
        $stmt->execute([$hash]);
        $member = $stmt->fetch();
        if (!$member) {
            throw new HttpError(404, 'Kayıt bulunamadı.');
        }

        $userId = $member['nodyo_user_id'];
        if (!$userId) {
            [$status, $user] = nodyo_request('POST', '/users', ['email' => $member['email'], 'full_name' => $member['full_name']]);
            if ($status === 409) {
                throw new HttpError(409, 'Bu e-posta adresiyle zaten bir hesap bulunuyor. Lütfen bizimle iletişime geçin.');
            }
            if (!in_array($status, [200, 201], true) || empty($user['id'])) {
                throw new HttpError(502, 'Hesabınız hazırlanamadı. Lütfen daha sonra tekrar deneyin.');
            }
            $userId = (string) $user['id'];
            $accountStatus = $user['account_status'] ?? null;
            db()->prepare('UPDATE nodyo_members SET nodyo_user_id = ?, account_status = ? WHERE id = ?')
                ->execute([$userId, $accountStatus, $member['id']]);
            if ($accountStatus === 'onboarding' && nodyo_onboard($userId, $member['full_name'])) {
                db()->prepare('UPDATE nodyo_members SET account_status = ? WHERE id = ?')->execute(['active', $member['id']]);
            }
        }

        [$status, $sso] = nodyo_request('POST', '/users/' . rawurlencode((string) $userId) . '/sso');
        $redirect = (string) ($sso['redirect_url'] ?? '');
        if ($status !== 200 || !str_starts_with($redirect, 'https://')) {
            throw new HttpError(502, 'Giriş başlatılamadı. Lütfen daha sonra tekrar deneyin.');
        }
    } catch (Throwable $e) {
        db()->prepare('UPDATE nodyo_login_tokens SET used_at = NULL WHERE token_hash = ?')->execute([$hash]);
        throw $e;
    }

    db()->prepare('UPDATE nodyo_members SET email_verified_at = COALESCE(email_verified_at, NOW()), last_sso_at = NOW(), sso_count = sso_count + 1 WHERE id = ?')
        ->execute([$member['id']]);

    return ['ok' => true, 'redirect' => $redirect];
}
