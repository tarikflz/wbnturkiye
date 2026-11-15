-- Nodyo entegrasyonu: sitede Nodyo için kayıt olan üyeler ve e-posta doğrulama bağlantıları.
-- Mevcut veritabanında bir kez çalıştırın (phpMyAdmin > İçe Aktar).

CREATE TABLE IF NOT EXISTS nodyo_members (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(32) NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  full_name VARCHAR(64) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  kvkk TINYINT(1) NOT NULL DEFAULT 0,
  nodyo_user_id VARCHAR(64) NULL,
  account_status VARCHAR(40) NULL,
  email_verified_at DATETIME NULL,
  last_sso_at DATETIME NULL,
  sso_count INT UNSIGNED NOT NULL DEFAULT 0,
  ip VARCHAR(45) NULL,
  deleted_at DATETIME NULL,
  deleted_by VARCHAR(60) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tek kullanımlık giriş bağlantıları (yalnızca SHA-256 özeti saklanır).
CREATE TABLE IF NOT EXISTS nodyo_login_tokens (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  INDEX idx_nodyo_tokens_member (member_id, created_at),
  FOREIGN KEY (member_id) REFERENCES nodyo_members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
