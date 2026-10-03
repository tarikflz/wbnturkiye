<?php
declare(strict_types=1);

// Form seçenekleri: sayfalar bu listelerden üretilir, sunucu da aynı listeyle doğrular.
const SECTOR_OPTIONS = [
    'Finans', 'Sigortacılık', 'Teknoloji', 'Finansal Hizmetler', 'Bilgi Teknolojileri',
    'Danışmanlık', 'Girişimcilik / Startup', 'Diğer',
];
const EXPERTISE_OPTIONS = [
    'Yapay Zekâ', 'Fintek', 'Ödeme Sistemleri', 'Bankacılık', 'Sigortacılık', 'Teknoloji',
    'Dijital Dönüşüm', 'Girişimcilik', 'Liderlik', 'Pazarlama & İletişim', 'İnsan & Kültür',
    'Sürdürülebilirlik', 'Yatırım / Finans', 'Hukuk', 'Diğer',
];
const ACTIVE_AREA_OPTIONS = [
    'Etkinlik / Networking', 'Eğitim', 'Konuşmacılık', 'Mentorluk', 'Uzmanlık paylaşımı',
    'İş birliği / İş geliştirme', 'Sosyal etki projeleri', 'WBN çalışma grupları',
];
const MEMBERSHIP_STATUS_OPTIONS = ['Yeni Üye', 'Mevcut Üyelik Yenileme'];
const MEMBERSHIP_TYPE_OPTIONS = ['Kurumsal', 'Bireysel'];

const FORM_RATE_LIMIT = 10;        // IP başına
const FORM_RATE_WINDOW = 3600;     // saniye

function in_rule(array $options): string
{
    return 'in:' . implode('|', $options);
}

/** Bot tuzağı ve hız sınırı: tüm public formlar için. Bot ise sessizce başarılı döner. */
function guard_public_form(array $input): void
{
    if (!empty($input['_hp'])) {
        json_response(['ok' => true, 'message' => 'Teşekkür ederiz.']);
    }
    if (rate_limit_hit('form', FORM_RATE_LIMIT, FORM_RATE_WINDOW)) {
        throw new HttpError(429, 'Çok fazla gönderim yaptınız. Lütfen bir süre sonra tekrar deneyin.');
    }
    rate_limit_record('form');
}

/** Yeni üye ve bilgi güncelleme formlarının ortak alanları. */
function member_rules(): array
{
    return [
        'full_name' => ['required', 'max:150'],
        'phone' => ['required', 'max:40'],
        'email' => ['required', 'email', 'max:190'],
        'kvkk' => ['accepted'],
        'company' => ['required', 'max:190'],
        'title' => ['required', 'max:150'],
        'sector' => ['required', in_rule(SECTOR_OPTIONS)],
        'other_sector' => ['max:150'],
        'linkedin' => ['required', 'url', 'max:300'],
        'expertise' => ['array', in_rule(EXPERTISE_OPTIONS)],
        'other_expertise' => ['max:190'],
        'active_areas' => ['array', in_rule(ACTIVE_AREA_OPTIONS)],
        'other_active_area' => ['max:190'],
        'database_update_consent' => ['in:Evet'],
        'extra_info' => ['max:3000'],
    ];
}

function member_row(array $d): array
{
    return [
        'full_name' => $d['full_name'],
        'phone' => $d['phone'],
        'email' => $d['email'],
        'kvkk' => 1,
        'company' => $d['company'],
        'title' => $d['title'],
        'sector' => $d['sector'],
        'other_sector' => $d['sector'] === 'Diğer' ? $d['other_sector'] : '',
        'linkedin' => $d['linkedin'],
        'expertise' => json_encode($d['expertise'], JSON_UNESCAPED_UNICODE),
        'other_expertise' => in_array('Diğer', $d['expertise'], true) ? $d['other_expertise'] : '',
        'active_areas' => json_encode($d['active_areas'], JSON_UNESCAPED_UNICODE),
        'other_active_area' => $d['other_active_area'],
        'database_update_consent' => $d['database_update_consent'],
        'extra_info' => $d['extra_info'],
        'ip' => client_ip(),
    ];
}

function member_mail_rows(array $d): array
{
    return [
        'Ad Soyad' => $d['full_name'],
        'Cep Telefonu' => $d['phone'],
        'E-posta' => $d['email'],
        'KVKK ve Gizlilik Onayı' => 'Onaylandı',
        'Kurum / Şirket' => $d['company'],
        'Ünvan' => $d['title'],
        'Sektör' => $d['sector'] . ($d['sector'] === 'Diğer' && $d['other_sector'] ? ': ' . $d['other_sector'] : ''),
        'LinkedIn' => $d['linkedin'],
        'Uzmanlık / İlgi Alanları' => implode(', ', $d['expertise'])
            . (in_array('Diğer', $d['expertise'], true) && $d['other_expertise'] ? ' — ' . $d['other_expertise'] : ''),
        'Aktif Yer Almak İstediği Alanlar' => implode(', ', $d['active_areas'])
            . ($d['other_active_area'] ? ($d['active_areas'] ? ', ' : '') . 'Diğer: ' . $d['other_active_area'] : ''),
        'Veri Tabanı Güncelleme Onayı' => $d['database_update_consent'] ?: 'Hayır',
        'Ek Bilgi' => $d['extra_info'],
    ];
}

function send_auto_reply(string $to, string $fullName, string $formName): void
{
    $html = mail_layout(
        '<p>Merhaba ' . e($fullName) . ',</p>'
        . '<p>WBN Türkiye ' . e($formName) . ' başvurunuz alınmıştır. Teşekkür ederiz.</p>'
        . '<p>Sevgilerimizle,<br>WBN Türkiye</p>'
    );
    send_mail([$to], 'WBN Türkiye | ' . $formName, $html);
}

function handle_application(array $input): array
{
    guard_public_form($input);
    $d = validate($input, member_rules() + ['reference' => ['required', 'max:150']]);

    [, $ref] = db_insert('applications', member_row($d) + ['reference' => $d['reference']], 'WBN');

    $rows = ['Başvuru No' => $ref] + member_mail_rows($d);
    $rows = array_slice($rows, 0, 4, true) + ['Referans' => $d['reference']] + array_slice($rows, 4, null, true);
    send_mail(config('notify.default'), 'WBN Türkiye | Yeni Üye Başvuru Formu', mail_table('Yeni Üye Başvurusu', $rows), $d['email']);
    send_auto_reply($d['email'], $d['full_name'], 'Yeni Üye Başvuru Formu');

    return ['ok' => true, 'message' => 'Başvurunuz alındı. Teşekkür ederiz.', 'ref' => $ref];
}

function handle_member_update(array $input): array
{
    guard_public_form($input);
    $d = validate($input, member_rules() + ['attended_events' => [in_rule(['Evet', 'Hayır'])]]);

    [, $ref] = db_insert('member_updates', member_row($d) + ['attended_events' => $d['attended_events']], 'WBN-UPD');

    $rows = ['Kayıt No' => $ref] + member_mail_rows($d) + ['WBN Etkinlik Katılımı' => $d['attended_events']];
    send_mail(config('notify.default'), 'WBN Türkiye | Üye Bilgi Güncelleme Formu', mail_table('Üye Bilgi Güncellemesi', $rows), $d['email']);
    send_auto_reply($d['email'], $d['full_name'], 'Üye Bilgi Güncelleme Formu');

    return ['ok' => true, 'message' => 'Bilgileriniz güncellendi. Teşekkür ederiz.', 'ref' => $ref];
}

function handle_corporate_application(array $input): array
{
    guard_public_form($input);
    $d = validate($input, [
        'company_title' => ['required', 'max:190'],
        'registry_no' => ['required', 'max:60'],
        'website' => ['required', 'url', 'max:300'],
        'applicant_name' => ['required', 'max:150'],
        'applicant_title' => ['required', 'max:150'],
        'phone' => ['required', 'max:40'],
        'email' => ['required', 'email', 'max:190'],
    ]);

    [, $ref] = db_insert('corporate_applications', $d + ['ip' => client_ip()], 'WBN-CORP');

    send_mail(config('notify.default'), 'WBN Türkiye | Kurumsal Üyelik Başvurusu', mail_table('Kurumsal Üyelik Başvurusu', [
        'Başvuru No' => $ref,
        'Firma Ticari Ünvanı' => $d['company_title'],
        'Ticaret Odası Sicil Numarası' => $d['registry_no'],
        'Web Sitesi' => $d['website'],
        'Başvuru Sahibi Adı Soyadı' => $d['applicant_name'],
        'Başvuru Sahibi Ünvanı' => $d['applicant_title'],
        'Başvuru Sahibi Telefonu' => $d['phone'],
        'Başvuru Sahibi E-Mail Adresi' => $d['email'],
    ]), $d['email']);

    return ['ok' => true, 'message' => 'Başvurunuz alındı. Teşekkür ederiz.', 'ref' => $ref];
}

function handle_receipt(array $input, ?array $file): array
{
    guard_public_form($input);
    $d = validate($input, [
        'full_name' => ['required', 'max:150'],
        'company_name' => ['required', 'max:190'],
        'email' => ['required', 'email', 'max:190'],
        'membership_status' => ['required', in_rule(MEMBERSHIP_STATUS_OPTIONS)],
        'membership_type' => [in_rule(MEMBERSHIP_TYPE_OPTIONS)],
    ]);
    $upload = store_receipt_upload($file);

    [, $ref] = db_insert('receipts', $d + [
        'receipt_name' => $upload['original'],
        'receipt_mime' => $upload['mime'],
        'receipt_size' => $upload['size'],
        'stored_name' => $upload['stored'],
        'ip' => client_ip(),
    ], 'WBN-RCP');

    send_mail(
        config('notify.receipts'),
        'WBN Türkiye | Bağış Dekontu Gönderimi',
        mail_table('Bağış Dekontu', [
            'Dekont No' => $ref,
            'Gönderim Tarihi' => date('d.m.Y H:i'),
            'Adı Soyadı' => $d['full_name'],
            'Firma Adı' => $d['company_name'],
            'E-Mail Adresi' => $d['email'],
            'Üye Durumu' => $d['membership_status'],
            'Üyelik Tipi' => $d['membership_type'],
            'Dosya' => $upload['original'],
        ]),
        $d['email'],
        [['path' => STORAGE_DIR . '/receipts/' . $upload['stored'], 'name' => $upload['original']]]
    );

    return ['ok' => true, 'message' => 'Dekontunuz gönderildi.', 'ref' => $ref];
}

function handle_contact(array $input): array
{
    guard_public_form($input);
    $d = validate($input, [
        'full_name' => ['required', 'max:150'],
        'email' => ['required', 'email', 'max:190'],
        'phone' => ['required', 'max:40'],
        'company' => ['max:190'],
        'title' => ['max:150'],
        'message' => ['required', 'max:5000'],
    ]);

    $stmt = db()->prepare(
        'INSERT INTO contact_messages (full_name, email, phone, company, title, message, ip) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$d['full_name'], $d['email'], $d['phone'], $d['company'], $d['title'], $d['message'], client_ip()]);

    send_mail(config('notify.default'), 'WBN Türkiye Web Sitesi İletişim Formu', mail_table('İletişim Formu', [
        'Adı Soyadı' => $d['full_name'],
        'E-Mail Adresi' => $d['email'],
        'Telefon' => $d['phone'],
        'Firma Adı' => $d['company'],
        'Ünvan' => $d['title'],
        'Mesaj' => $d['message'],
    ]), $d['email']);

    return ['ok' => true, 'message' => 'Mesajınız iletildi. Teşekkür ederiz.'];
}
