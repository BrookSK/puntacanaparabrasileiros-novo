-- Migração: Adicionar campos group_pricing_enabled e group_pricing na tabela trips
-- Data: 2026-08-13
-- Descrição: Permite definir preço fixo total por número de passageiros (1 pax=$X, 2 pax=$Y, etc.)

ALTER TABLE trips ADD COLUMN group_pricing_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER group_discount_rules;
ALTER TABLE trips ADD COLUMN group_pricing TEXT DEFAULT NULL AFTER group_pricing_enabled;
