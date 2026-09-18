-- =============================================================
-- Rastreamento de visitas das AGÊNCIAS (links ?ag=)
-- Espelha a tabela affiliate_visits.
-- FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- =============================================================

CREATE TABLE IF NOT EXISTS `agency_visits` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `agency_id` INT UNSIGNED NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `referrer` TEXT DEFAULT NULL,
    `page_url` TEXT DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `converted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_agv_agency` (`agency_id`),
    KEY `idx_agv_date` (`created_at`),
    KEY `idx_agv_dedupe` (`agency_id`, `ip_address`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
