-- Migração: Adicionar campos youtube_url e faqs na tabela trips
-- Data: 2026-08-29

ALTER TABLE trips ADD COLUMN youtube_url VARCHAR(500) DEFAULT NULL AFTER important_notes;
ALTER TABLE trips ADD COLUMN faqs TEXT DEFAULT NULL AFTER youtube_url;
