-- Migração: Módulo de Agências Parceiras
-- Data: 2026-08-13
-- Descrição: Cria o módulo de gestão de agências/empresas parceiras (com CNPJ)
--            que vendem passeios e recebem comissão. Separado do módulo de afiliados.
--
-- Como aplicar: execute este arquivo no phpMyAdmin no banco do site.

-- ─────────────────────────────────────────────
-- Tabela de agências parceiras (empresas)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `agencies` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_name` VARCHAR(200) NOT NULL COMMENT 'Razão social',
    `trade_name` VARCHAR(200) DEFAULT NULL COMMENT 'Nome fantasia',
    `cnpj` VARCHAR(20) DEFAULT NULL,
    `contact_name` VARCHAR(150) DEFAULT NULL COMMENT 'Responsável / contato',
    `email` VARCHAR(190) DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(120) DEFAULT NULL,
    `country` VARCHAR(120) DEFAULT NULL,
    `bank_info` TEXT DEFAULT NULL COMMENT 'Dados bancários / PIX (texto livre)',
    `ref_code` VARCHAR(40) NOT NULL COMMENT 'Código de indicação usado no link ?ag=',
    `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `total_sales` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_commission` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_paid` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_agency_ref` (`ref_code`),
    KEY `idx_agency_status` (`status`),
    KEY `idx_agency_cnpj` (`cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- Comissões geradas para as agências
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `agency_commissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `agency_id` INT UNSIGNED NOT NULL,
    `booking_id` INT UNSIGNED DEFAULT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `rate` DECIMAL(5,2) NOT NULL,
    `base_amount` DECIMAL(12,2) NOT NULL COMMENT 'Valor base da venda',
    `status` ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
    `paid_at` DATETIME DEFAULT NULL,
    `payout_reference` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_agcomm_agency` (`agency_id`),
    KEY `idx_agcomm_booking` (`booking_id`),
    KEY `idx_agcomm_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Obs.: sem FOREIGN KEY para evitar falhas de criação em hosts com engines/tipos divergentes.
--       Os vínculos são validados na aplicação.

-- ─────────────────────────────────────────────
-- Rastreamento da agência que originou cada reserva
-- ─────────────────────────────────────────────
ALTER TABLE `bookings` ADD COLUMN `agency_id` INT UNSIGNED DEFAULT NULL AFTER `affiliate_id`;
ALTER TABLE `bookings` ADD COLUMN `agency_ref_code` VARCHAR(40) DEFAULT NULL AFTER `agency_id`;
