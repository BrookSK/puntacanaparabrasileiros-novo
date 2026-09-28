-- Migration 034: coluna para registrar quando o LRV mudou o status da demanda
-- (recebido via callback ticket.status_changed). Necessária para a integração
-- de retorno de status (helpdeskON → Punta Cana).
--
-- Rode este ALTER se a tabela support_tickets já foi criada pela migration 033
-- na sua versão anterior (sem esta coluna).

ALTER TABLE `support_tickets`
    ADD COLUMN `lrv_status_changed_at` DATETIME NULL AFTER `lrv_status`;
