-- Migração: Adicionar campo ideal_for (JSON) na tabela trips
-- Guarda a lista de perfis "Ideal para" exibida como chips na página do passeio.
-- Data: 2026-09-10

ALTER TABLE trips ADD COLUMN ideal_for JSON DEFAULT NULL AFTER excludes;
