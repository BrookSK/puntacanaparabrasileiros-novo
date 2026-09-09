-- Migração: coluna raw_payload em whatsapp_messages
-- Data: 2026-09-09
-- Descrição: Guarda o payload bruto (JSON) recebido no webhook para mensagens de mídia.
--            Necessário para (re)baixar o áudio da Evolution com a estrutura exata da
--            mensagem (a Aurora precisa disso para transcrever áudios de forma confiável).
--
-- Como aplicar: execute este arquivo no phpMyAdmin (ou via CLI mysql) no banco do site.

ALTER TABLE `whatsapp_messages`
    ADD COLUMN `raw_payload` LONGTEXT DEFAULT NULL COMMENT 'Payload bruto (JSON) do webhook, usado para baixar mídia' AFTER `transcription`;
