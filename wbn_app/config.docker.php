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
    'links' => [
        'tev_donation' => 'https://bagis.tev.org.tr/burs-fonu-bagisi?q=67532&k=46004',
    ],
    'uploads' => [
        'max_bytes' => 10 * 1024 * 1024,
    ],
];
