-- Migration: Corrigir valores de status inválidos nas tabelas de afiliados
-- Data: 2026-08-11
-- Descrição: Altera coluna status de ENUM para VARCHAR para evitar rejeição silenciosa de valores.
--            Corrige registros que ficaram com status vazio devido a updates inválidos anteriores.

-- 1. Alterar affiliate_requests: ENUM -> VARCHAR para flexibilidade
ALTER TABLE `affiliate_requests` MODIFY COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pending';

-- 2. Alterar affiliates: ENUM -> VARCHAR para flexibilidade
ALTER TABLE `affiliates` MODIFY COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pending';

-- 3. Corrigir registros com status vazio ou NULL (resultado de ENUM rejeitando valor inválido)
UPDATE `affiliate_requests` SET `status` = 'rejected', `rejected_at` = NOW() WHERE `status` = '' OR `status` IS NULL;
UPDATE `affiliates` SET `status` = 'inactive' WHERE `status` = '' OR `status` IS NULL;
