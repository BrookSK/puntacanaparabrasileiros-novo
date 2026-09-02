-- Migração: Adicionar valores 'pix' e 'simulate' ao ENUM gateway da tabela payments
-- Data: 2026-08-13
-- Descrição: Corrige erro "Data truncated for column gateway" ao usar PIX ou Simulação de pagamento

ALTER TABLE payments MODIFY COLUMN gateway ENUM('paypal','stripe','pix','pagbank','manual','free','simulate') NOT NULL;
