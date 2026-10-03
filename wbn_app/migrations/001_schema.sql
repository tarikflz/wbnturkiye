-- WBN Türkiye veritabanı şeması
-- cPanel > phpMyAdmin > veritabanını seçin > "İçe Aktar" (Import) ile bir kez çalıştırın.

SET NAMES utf8mb4;

-- Yeni Üye Başvuru Formu -> Admin: Yeni Üye Başvuruları
CREATE TABLE IF NOT EXISTS applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(32) NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NOT NULL,
  reference VARCHAR(150) NOT NULL,
  kvkk TINYINT(1) NOT NULL DEFAULT 0,
  company VARCHAR(190) NOT NULL,
  title VARCHAR(150) NOT NULL,
  sector VARCHAR(100) NOT NULL,
  other_sector VARCHAR(150) NOT NULL DEFAULT '',
  linkedin VARCHAR(300) NOT NULL,
  expertise JSON NOT NULL,
  other_expertise VARCHAR(190) NOT NULL DEFAULT '',
  active_areas JSON NOT NULL,
  other_active_area VARCHAR(190) NOT NULL DEFAULT '',
  database_update_consent VARCHAR(10) NOT NULL DEFAULT '',
  extra_info TEXT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  email_status ENUM('not_sent','sent','error') NOT NULL DEFAULT 'not_sent',
  decided_at DATETIME NULL,
  decided_by VARCHAR(60) NULL,
  ip VARCHAR(45) NULL,
  INDEX idx_applications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Üye Bilgi Güncelleme Formu -> Admin: Üye Bilgi Güncellemeleri
CREATE TABLE IF NOT EXISTS member_updates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(32) NULL UNIQUE,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NOT NULL,
  kvkk TINYINT(1) NOT NULL DEFAULT 0,
  company VARCHAR(190) NOT NULL,
  title VARCHAR(150) NOT NULL,
  sector VARCHAR(100) NOT NULL,
  other_sector VARCHAR(150) NOT NULL DEFAULT '',
  linkedin VARCHAR(300) NOT NULL,
  expertise JSON NOT NULL,
  other_expertise VARCHAR(190) NOT NULL DEFAULT '',
  attended_events VARCHAR(10) NOT NULL DEFAULT '',
  active_areas JSON NOT NULL,
  other_active_area VARCHAR(190) NOT NULL DEFAULT '',
  database_update_consent VARCHAR(10) NOT NULL DEFAULT '',
  extra_info TEXT NULL,
  ip VARCHAR(45) NULL,
  INDEX idx_member_updates_updated (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kurumsal Üyelik Başvuru Formu -> Admin: Kurumsal Üye Başvuruları
CREATE TABLE IF NOT EXISTS corporate_applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(32) NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  company_title VARCHAR(190) NOT NULL,
  registry_no VARCHAR(60) NOT NULL,
  website VARCHAR(300) NOT NULL,
  applicant_name VARCHAR(150) NOT NULL,
  applicant_title VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  email_status ENUM('not_sent','sent','error') NOT NULL DEFAULT 'not_sent',
  decided_at DATETIME NULL,
  decided_by VARCHAR(60) NULL,
  ip VARCHAR(45) NULL,
  INDEX idx_corporate_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dekont Gönderme Formu (bireysel + kurumsal) -> Admin: Dekontlar
CREATE TABLE IF NOT EXISTS receipts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(32) NULL UNIQUE,
  submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  full_name VARCHAR(150) NOT NULL,
  company_name VARCHAR(190) NOT NULL,
  email VARCHAR(190) NOT NULL,
  membership_status VARCHAR(40) NOT NULL,
  membership_type VARCHAR(20) NOT NULL DEFAULT '',
  receipt_name VARCHAR(255) NOT NULL,
  receipt_mime VARCHAR(100) NOT NULL,
  receipt_size INT UNSIGNED NOT NULL,
  stored_name VARCHAR(100) NOT NULL,
  ip VARCHAR(45) NULL,
  INDEX idx_receipts_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ana sayfa iletişim formu
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  company VARCHAR(190) NOT NULL DEFAULT '',
  title VARCHAR(150) NOT NULL DEFAULT '',
  message TEXT NOT NULL,
  ip VARCHAR(45) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin kullanıcıları (ilk kullanıcıyı oluşturma adımları README'de)
CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Form gönderimi ve giriş denemesi sınırlama
CREATE TABLE IF NOT EXISTS rate_limits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bucket VARCHAR(60) NOT NULL,
  ip VARCHAR(45) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_rate_limits_lookup (bucket, ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
