-- Migração: Controle de Caução/Sinal
-- Data: 2026-08-13
-- Descrição: Amplia a tabela payments para registrar as movimentações do sinal:
--   - pagamento do restante presencial (mantendo o sinal)
--   - devolução do sinal (estorno da caução)
--   - pagamento total presencial (após devolver o sinal)
-- Reutiliza a tabela payments existente (não cria tabela nova).
--
-- Como aplicar: execute no phpMyAdmin no banco do site.

-- Amplia o ENUM de tipo de pagamento com os novos tipos de movimentação.
ALTER TABLE `payments`
    MODIFY COLUMN `type` ENUM('full','partial','remaining','deposit_refund','manual_full') NOT NULL DEFAULT 'full';

-- Método do pagamento manual/presencial (dinheiro, pix, cartão, transferência).
ALTER TABLE `payments` ADD COLUMN `method` VARCHAR(30) DEFAULT NULL AFTER `type`;

-- Observações do registro (ex.: "pago presencial em dinheiro", "sinal devolvido via PIX").
ALTER TABLE `payments` ADD COLUMN `notes` TEXT DEFAULT NULL AFTER `method`;
