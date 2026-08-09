-- Migration: Atualizar imagem da categoria Família
-- Data: 2026-08-09
-- Usa a mesma imagem do site antigo (adequado para crianças)

UPDATE `trip_categories`
SET `image` = 'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0148.jpg'
WHERE `slug` = 'familia';
