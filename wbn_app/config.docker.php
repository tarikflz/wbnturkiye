<?php
// Yalnızca docker compose ile yerel test içindir (WBN_CONFIG ile seçilir). Gizli bilgi içermez.

return [
    'app' => [
        'env' => 'local',
        'base_url' => 'http://localhost:8080',
        'timezone' => 'Europe/Istanbul',
    ],
    'db' => [
        'host' => 'db',
        'name' => 'wbn_local',
        'user' => 'wbn',
        'pass' => 'wbn_local_pw',
    ],
    // E-postalar gönderilmez, wbn_app/storage/logs/mail.log dosyasına yazılır.
    'mail' => [
        'transport' => 'log',
        'host' => 'localhost',
        'port' => 25,
        'secure' => '',
        'username' => '',
        'password' => '',
        'from_email' => 'wbnturkiye@gmail.com',
        'from_name' => 'WBN Türkiye',
        'reply_to' => 'wbnturkiye@gmail.com',
    ],
    'notify' => [
        'default' => ['test@example.com'],
        'receipts' => ['test@example.com'],
    ],
    // Nodyo Partner API. api_key'i yalnızca sunucudaki config.php'ye yazın, asla git'e eklemeyin.
    // Staging: https://partner.nodyo.co/v1 (Cloudflare Zero Trust arkasında: gerekirse access_client_* servis token'ı).
    'nodyo' => [
        'base_url' => 'https://partner.nodyo.co/v1',
        'api_key' => '',
        'access_client_id' => '',
        'access_client_secret' => '',
        // true: header'da "Giriş Yap" butonu görünür ve /danismanlik arama motorlarına açılır. Canlıya alırken true yapın.
        'show_login' => false,
    ],

    'links' => [
        'tev_donation' => 'https://bagis.tev.org.tr/burs-fonu-bagisi?q=67532&k=46004',
    ],
    'uploads' => [
        'max_bytes' => 10 * 1024 * 1024,
    ],
];
