-- Migration: Adicionar status 'blocked' à tabela affiliates
-- Data: 2026-08-10

ALTER TABLE affiliates MODIFY COLUMN status ENUM('pending', 'active', 'rejected', 'suspended', 'blocked') NOT NULL DEFAULT 'pending';

-- Converter 'suspended' para 'blocked' para consistência
UPDATE affiliates SET status = 'blocked' WHERE status = 'suspended';
