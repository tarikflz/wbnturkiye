<?php
// Bu dosyayı config.php adıyla kopyalayıp gerçek değerleri girin.
// config.php git'e eklenmez.

return [
    'app' => [
        // 'production' ortamında hatalar ekrana basılmaz, storage/logs altına yazılır.
        'env' => 'production',
        'base_url' => 'https://wbnturkiye.com',
        'timezone' => 'Europe/Istanbul',
    ],

    'db' => [
        'host' => 'localhost',
        'name' => 'cpaneluser_wbn',
        'user' => 'cpaneluser_wbn',
        'pass' => '',
    ],

    // Gmail SMTP: wbnturkiye@gmail.com hesabında 2 adımlı doğrulama açık olmalı,
    // şifre olarak Google "Uygulama Şifresi" (App Password) kullanılmalı.
    'mail' => [
        // 'smtp' gerçek gönderim yapar; 'log' e-postaları storage/logs/mail.log dosyasına yazar (yerel test için).
        'transport' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls',
        'username' => 'wbnturkiye@gmail.com',
        'password' => '',
        'from_email' => 'wbnturkiye@gmail.com',
        'from_name' => 'WBN Türkiye',
        'reply_to' => 'wbnturkiye@gmail.com',
    ],

    // Form bildirimlerinin gideceği adresler.
    'notify' => [
        'default' => ['wbnturkiye@gmail.com', 'wbuzznetwork@gmail.com'],
        'receipts' => ['wbnturkiye@gmail.com', 'wbuzznetwork@gmail.com', 'tulin.cakmak@novaformdan.com'],
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
