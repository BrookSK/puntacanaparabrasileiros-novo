-- Migration: Adicionar campo de documentos extras ao trips
-- Data: 2026-08-12

ALTER TABLE trips
ADD COLUMN documents JSON NULL AFTER gallery;
