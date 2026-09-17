-- =============================================================
-- CORRIGE IMAGENS DOS TRANSFERS
-- Os arquivos /uploads/vehicle-*.png foram perdidos (não existem
-- no disco). Em vez de re-subir, apontamos cada transfer para as
-- imagens fixas que JÁ EXISTEM e funcionam em /assets/images/
-- (as mesmas usadas na página "Serviços de transporte").
--
-- FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- =============================================================

-- [A] CONFERÊNCIA (não altera nada)
SELECT id, title, image FROM transfer_vehicles ORDER BY id;

-- [B] CORREÇÃO
UPDATE transfer_vehicles SET image = '/assets/images/onibus.png'
WHERE id = 1;  -- Transfer em Ônibus Compartilhado

UPDATE transfer_vehicles SET image = '/assets/images/van.png'
WHERE id = 2;  -- Transfer Privativo em Van

UPDATE transfer_vehicles SET image = '/assets/images/van_adap.png'
WHERE id = 3;  -- Transfer Acessível com Van Adaptada

-- [C] CONFERÊNCIA FINAL (opcional)
SELECT id, title, image FROM transfer_vehicles ORDER BY id;
