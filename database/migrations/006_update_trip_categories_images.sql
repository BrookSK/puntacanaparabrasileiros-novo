-- Migration: Atualizar categorias de passeios com imagens
-- As imagens são placeholders das fotos já existentes no projeto
-- Substituir posteriormente com imagens específicas de cada categoria via admin

UPDATE `trip_categories` SET `image` = '/assets/images/layout/praia.jpeg' WHERE `slug` = 'passeios-de-barco';
UPDATE `trip_categories` SET `image` = '/assets/images/layout/praia-pessoas.jpeg' WHERE `slug` = 'aventura';
UPDATE `trip_categories` SET `image` = '/assets/images/layout/PUNTA-CANA-1.png' WHERE `slug` = 'cultural';
UPDATE `trip_categories` SET `image` = '/assets/images/layout/praia-com-arvore.jpeg' WHERE `slug` = 'natureza';
UPDATE `trip_categories` SET `image` = '/assets/images/layout/casal.jpg' WHERE `slug` = 'noturno';
UPDATE `trip_categories` SET `image` = '/assets/images/layout/mulher.jpg' WHERE `slug` = 'familia';
UPDATE `trip_categories` SET `image` = '/assets/images/layout/praia.jpeg' WHERE `slug` = 'romantico';
UPDATE `trip_categories` SET `image` = '/assets/images/layout/praia-pessoas.jpeg' WHERE `slug` = 'esportes-aquaticos';
