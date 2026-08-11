-- Migration: Corrigir valores de status inválidos nas tabelas de afiliados
-- Data: 2026-08-11
-- Descrição: Corrige registros que possam ter ficado com status incorreto
--            devido a tentativas de update com valores fora do ENUM.

-- 1. Garantir que a tabela affiliates tenha 'inactive' no ENUM (para bloqueio)
ALTER TABLE `affiliates` MODIFY COLUMN `status` ENUM('pending','active','inactive','rejected') NOT NULL DEFAULT 'pending';

-- 2. Atualizar registros que possam ter ficado com status vazio (update silencioso do ENUM)
-- Se houve tentativa de setar 'blocked' ou 'suspended' e o MySQL descartou, o status pode ter ficado como string vazia
UPDATE `affiliates` SET `status` = 'inactive' WHERE `status` = '' OR `status` IS NULL;

-- 3. Garantir que affiliate_requests tenha os valores corretos
-- (caso a tabela já exista mas com ENUM diferente)
ALTER TABLE `affiliate_requests` MODIFY COLUMN `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending';
