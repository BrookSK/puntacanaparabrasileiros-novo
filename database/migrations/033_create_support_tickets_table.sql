-- Migration 033: Tabela de demandas de Suporte (integração com LRV / helpdeskON)
--
-- Cada linha é uma demanda criada na área "Suporte" do painel. A demanda é
-- gravada localmente SEMPRE; o envio ao LRV é feito em seguida via
-- POST /api/v1/tickets. Se o envio falhar, a linha fica com sync_status='pending'
-- para reenvio posterior (o external_ref garante idempotência no LRV).

CREATE TABLE IF NOT EXISTS `support_tickets` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- Dados da demanda (espelham os campos aceitos pela API do LRV)
    `title`             VARCHAR(255) NOT NULL,
    `description`       TEXT NOT NULL,
    `priority`          ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    `category`          VARCHAR(100) NULL,
    `requester_name`    VARCHAR(191) NULL,
    `requester_company` VARCHAR(191) NULL,

    -- Referência única usada como external_ref no LRV (idempotência)
    `external_ref`      VARCHAR(191) NOT NULL,

    -- Retorno do LRV
    `lrv_id`                  BIGINT UNSIGNED NULL,
    `lrv_client_ticket_number` INT UNSIGNED NULL,
    `lrv_status`              VARCHAR(50) NULL,

    -- Estado da sincronização com o LRV
    -- pending  = gravado local, ainda não confirmado no LRV (reenviar)
    -- synced   = confirmado no LRV
    -- failed   = última tentativa falhou (mantém pendente de reenvio)
    `sync_status`       ENUM('pending','synced','failed') NOT NULL DEFAULT 'pending',
    `sync_error`        TEXT NULL,
    `synced_at`         DATETIME NULL,

    -- Autoria
    `created_by`        BIGINT UNSIGNED NULL,

    `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_support_tickets_external_ref` (`external_ref`),
    KEY `idx_support_tickets_sync_status` (`sync_status`),
    KEY `idx_support_tickets_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
