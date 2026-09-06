-- Migração: Regras de Cancelamento/Reembolso configuráveis
-- Data: 2026-08-13
-- Descrição: Faixas de reembolso por antecedência (dias ou horas antes da viagem),
--            cadastradas manualmente pelo admin. Cada faixa define um reembolso
--            percentual OU valor fixo.
--
-- Como aplicar: execute este arquivo no phpMyAdmin no banco do site.

-- ─────────────────────────────────────────────
-- Regras (faixas) de cancelamento
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `cancellation_rules` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(200) DEFAULT NULL COMMENT 'Descrição amigável da faixa',
    `time_unit` ENUM('days','hours') NOT NULL DEFAULT 'days' COMMENT 'Unidade da antecedência',
    `time_value` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Antecedência mínima (nesta unidade) para a faixa valer',
    `refund_type` ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
    `refund_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Percentual (0-100) ou valor fixo em USD',
    `sort_order` INT NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cr_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- Rastreamento da regra aplicada em cada solicitação de cancelamento
-- (a coluna refund_amount e o ENUM refund_status 'partial_refund' já existem)
-- ─────────────────────────────────────────────
ALTER TABLE `cancellation_requests` ADD COLUMN `refund_percentage` DECIMAL(5,2) DEFAULT NULL AFTER `refund_amount`;
ALTER TABLE `cancellation_requests` ADD COLUMN `applied_rule_label` VARCHAR(200) DEFAULT NULL AFTER `refund_percentage`;

-- ─────────────────────────────────────────────
-- Exemplos de faixas iniciais (o admin edita/exclui à vontade no painel).
-- Interpretação: a faixa vale quando a antecedência do cancelamento é MAIOR OU IGUAL
-- ao time_value. O sistema aplica a faixa de maior time_value que o cliente atende.
-- Ex.: >= 2 dias → 100%; >= 24 horas → 50%; abaixo disso → sem reembolso (0%).
-- ─────────────────────────────────────────────
INSERT INTO `cancellation_rules` (`label`, `time_unit`, `time_value`, `refund_type`, `refund_value`, `sort_order`, `active`)
SELECT * FROM (
    SELECT 'A partir de 2 dias antes' AS label, 'days' AS time_unit, 2 AS time_value, 'percentage' AS refund_type, 100.00 AS refund_value, 1 AS sort_order, 1 AS active UNION ALL
    SELECT 'A partir de 24 horas antes', 'hours', 24, 'percentage', 50.00, 2, 1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM `cancellation_rules` LIMIT 1);
