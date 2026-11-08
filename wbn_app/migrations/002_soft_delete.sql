-- Yönetim panelinden yapılan silmeler fiziksel değil, işaretlemedir (soft delete).
-- Mevcut veritabanında bir kez çalıştırın (phpMyAdmin > İçe Aktar). Yeni kurulumda 001'den sonra çalıştırın.

ALTER TABLE applications
  ADD COLUMN deleted_at DATETIME NULL,
  ADD COLUMN deleted_by VARCHAR(60) NULL;

ALTER TABLE member_updates
  ADD COLUMN deleted_at DATETIME NULL,
  ADD COLUMN deleted_by VARCHAR(60) NULL;

ALTER TABLE corporate_applications
  ADD COLUMN deleted_at DATETIME NULL,
  ADD COLUMN deleted_by VARCHAR(60) NULL;

ALTER TABLE receipts
  ADD COLUMN deleted_at DATETIME NULL,
  ADD COLUMN deleted_by VARCHAR(60) NULL;
