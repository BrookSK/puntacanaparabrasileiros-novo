-- =============================================================
-- FLUXO PÚBLICO DE AGÊNCIAS — estrutura de banco
-- Cria a tabela de solicitações e liga a agência a um usuário.
--
-- FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- Rode este arquivo UMA vez.
-- =============================================================

-- 1) Tabela de solicitações de cadastro de agência (pendentes de aprovação)
CREATE TABLE IF NOT EXISTS `agency_requests` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_name` VARCHAR(255) NOT NULL,
    `trade_name` VARCHAR(255) DEFAULT NULL,
    `cnpj` VARCHAR(30) DEFAULT NULL,
    `contact_name` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `city` VARCHAR(120) DEFAULT NULL,
    `country` VARCHAR(120) DEFAULT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `admin_notes` TEXT DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `rejected_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_agency_requests_status` (`status`),
    KEY `idx_agency_requests_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Liga a agência a um usuário (login). Só adiciona se ainda não existir.
--    (Se a coluna já existir, o phpMyAdmin vai acusar erro — pode ignorar nesse caso.)
ALTER TABLE `agencies` ADD COLUMN `user_id` INT UNSIGNED DEFAULT NULL AFTER `id`;
