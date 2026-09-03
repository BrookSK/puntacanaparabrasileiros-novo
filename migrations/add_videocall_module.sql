-- Migração: Módulo de Agendamento de Chamadas de Vídeo
-- Data: 2026-08-13
-- Descrição: Cria a tabela videocall_bookings (agendamentos de chamadas de vídeo feitos
--            pelos clientes na página do passeio) e insere as settings do módulo.
--
-- Como aplicar: execute este arquivo no phpMyAdmin (ou via CLI mysql) no banco do site.

-- ─────────────────────────────────────────────
-- Tabela de agendamentos
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `videocall_bookings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `trip_id` INT UNSIGNED DEFAULT NULL,
    `customer_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `scheduled_at` DATETIME NOT NULL,
    `duration_minutes` INT UNSIGNED NOT NULL DEFAULT 30,
    `meeting_link` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    `reminder_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `notes` TEXT DEFAULT NULL,
    `admin_notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_videocall_trip` (`trip_id`),
    KEY `idx_videocall_scheduled` (`scheduled_at`),
    KEY `idx_videocall_status` (`status`),
    CONSTRAINT `fk_videocall_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- Settings do módulo (chave/valor)
-- videocall_enabled       : liga/desliga o módulo (0/1)
-- videocall_days          : dias da semana disponíveis, CSV de 0=domingo ... 6=sábado (ex: "1,2,3,4,5")
-- videocall_hour_start    : hora inicial de atendimento (ex: "09:00")
-- videocall_hour_end      : hora final de atendimento (ex: "18:00")
-- videocall_duration      : duração de cada chamada em minutos (ex: "30")
-- videocall_reminder_token: token secreto usado pelo cron externo de lembretes
-- ─────────────────────────────────────────────
-- Inserimos apenas se a chave ainda não existir (independe de índice UNIQUE).
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`)
SELECT * FROM (
    SELECT 'videocall_enabled' AS k, '0' AS v, 'videocall' AS g, 'boolean' AS t UNION ALL
    SELECT 'videocall_days', '1,2,3,4,5', 'videocall', 'text' UNION ALL
    SELECT 'videocall_hour_start', '09:00', 'videocall', 'text' UNION ALL
    SELECT 'videocall_hour_end', '18:00', 'videocall', 'text' UNION ALL
    SELECT 'videocall_duration', '30', 'videocall', 'text' UNION ALL
    SELECT 'videocall_reminder_token', '', 'videocall', 'text'
) AS novo
WHERE NOT EXISTS (
    SELECT 1 FROM `settings` s WHERE s.`setting_key` = novo.k
);
