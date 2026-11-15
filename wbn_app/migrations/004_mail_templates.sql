-- Yönetim panelinde düzenlenen onay/red e-posta şablonları (tüm yöneticiler için ortak, sunucuda saklanır).
-- Mevcut veritabanında bir kez çalıştırın (phpMyAdmin > İçe Aktar).

CREATE TABLE IF NOT EXISTS mail_templates (
  template_key VARCHAR(60) NOT NULL PRIMARY KEY,
  content MEDIUMTEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by VARCHAR(60) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
