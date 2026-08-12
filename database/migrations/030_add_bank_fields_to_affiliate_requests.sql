-- Migration: Adicionar campos bancários à tabela affiliate_requests
-- Data: 2026-08-12

ALTER TABLE affiliate_requests
ADD COLUMN bank_name VARCHAR(100) NULL AFTER pix,
ADD COLUMN bank_agency VARCHAR(20) NULL AFTER bank_name,
ADD COLUMN bank_account VARCHAR(30) NULL AFTER bank_agency,
ADD COLUMN bank_account_type VARCHAR(20) NULL DEFAULT 'corrente' AFTER bank_account;
