-- ─────────────────────────────────────────────
-- Integração com o Google Meet (Google Calendar API)
--
-- Persistimos o ID do evento no Google Calendar e o calendário usado, para
-- podermos cancelar/atualizar a reunião depois (ex.: quando o admin cancela
-- ou exclui o agendamento no painel).
-- ─────────────────────────────────────────────

-- Coluna do ID do evento no Google Calendar.
-- (Execute cada ALTER apenas se a coluna ainda não existir no seu MySQL.)
ALTER TABLE `videocall_bookings`
    ADD COLUMN `google_event_id` VARCHAR(255) DEFAULT NULL AFTER `meeting_link`,
    ADD COLUMN `google_calendar_id` VARCHAR(190) DEFAULT NULL AFTER `google_event_id`;

-- ─────────────────────────────────────────────
-- Settings do Google Meet (chave/valor)
-- google_meet_enabled       : liga/desliga a integração real com o Google Meet (0/1)
--                             quando 0, o sistema usa o link Jitsi como antes.
-- google_meet_client_id     : Client ID do OAuth 2.0 (Google Cloud Console)
-- google_meet_client_secret : Client Secret do OAuth 2.0
-- google_meet_refresh_token : refresh_token obtido no fluxo de autorização (painel)
-- google_meet_calendar_id   : ID do calendário onde os eventos são criados (default: primary)
-- google_meet_timezone      : timezone IANA usado nos eventos (ex: America/Santo_Domingo)
-- ─────────────────────────────────────────────
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`)
SELECT * FROM (
    SELECT 'google_meet_enabled' AS k, '0' AS v, 'videocall' AS g, 'boolean' AS t UNION ALL
    SELECT 'google_meet_client_id', '', 'videocall', 'text' UNION ALL
    SELECT 'google_meet_client_secret', '', 'videocall', 'text' UNION ALL
    SELECT 'google_meet_refresh_token', '', 'videocall', 'text' UNION ALL
    SELECT 'google_meet_calendar_id', 'primary', 'videocall', 'text' UNION ALL
    SELECT 'google_meet_timezone', 'America/Santo_Domingo', 'videocall', 'text'
) AS novo
WHERE NOT EXISTS (
    SELECT 1 FROM `settings` s WHERE s.`setting_key` = novo.k
);
