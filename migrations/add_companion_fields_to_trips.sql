-- Migração: Adicionar campos de acompanhantes na tabela trips
-- Data: 2026-08-13
-- Descrição: Permite configurar acompanhantes por passeio com regras, limites e preço próprio

ALTER TABLE trips ADD COLUMN companion_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER composition_pricing_enabled;
ALTER TABLE trips ADD COLUMN companion_label VARCHAR(100) DEFAULT 'Acompanhante' AFTER companion_enabled;
ALTER TABLE trips ADD COLUMN companion_price DECIMAL(10,2) DEFAULT NULL AFTER companion_label;
ALTER TABLE trips ADD COLUMN companion_max_per_participant INT DEFAULT NULL AFTER companion_price;
ALTER TABLE trips ADD COLUMN companion_max_total INT DEFAULT NULL AFTER companion_max_per_participant;
ALTER TABLE trips ADD COLUMN companion_description VARCHAR(500) DEFAULT NULL AFTER companion_max_total;
