-- Migração: Sistema de Cupons de Desconto
-- Data: 2026-08-13
-- Descrição: Cria a tabela de cupons promocionais (gerais ou vinculados a um afiliado)
--            e adiciona colunas de rastreamento de cupom na tabela bookings.
--
-- Como aplicar: execute este arquivo no phpMyAdmin no banco do site.

-- ─────────────────────────────────────────────
-- Tabela de cupons
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `coupons` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `type` ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
    `value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `affiliate_id` INT UNSIGNED DEFAULT NULL,
    `min_order` DECIMAL(10,2) DEFAULT NULL,
    `max_uses` INT UNSIGNED DEFAULT NULL,
    `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `starts_at` DATETIME DEFAULT NULL,
    `expires_at` DATETIME DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_coupon_code` (`code`),
    KEY `idx_coupon_affiliate` (`affiliate_id`),
    KEY `idx_coupon_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Obs.: o vínculo com afiliados é validado na aplicação (sem FOREIGN KEY,
-- para evitar falhas de criação em hosts com engines/tipos divergentes).

-- ─────────────────────────────────────────────
-- Rastreamento do cupom usado em cada reserva
-- (a coluna discount_amount já existe em bookings)
-- ─────────────────────────────────────────────
ALTER TABLE `bookings` ADD COLUMN `coupon_id` INT UNSIGNED DEFAULT NULL AFTER `discount_amount`;
ALTER TABLE `bookings` ADD COLUMN `coupon_code` VARCHAR(50) DEFAULT NULL AFTER `coupon_id`;
