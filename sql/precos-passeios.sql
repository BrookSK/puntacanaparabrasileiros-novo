-- =============================================================
-- ATUALIZAÇÃO DE PREÇOS (baseado no PDF Outubro 2026)
-- Regra: Adulto = maior valor | Criança 4-11 = menor valor |
--        Infantil/menor de 3 = 0 (cortesia).
--
-- Só faz UPDATE (os registros já existem em trip_package_categories).
-- NÃO cria pacote nem categoria nova.
--
-- Categorias: 1=Adulto  2=Criança  6=Infantil
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. (Opcional) Rode o SELECT [A] para conferir os valores atuais.
-- 3. Rode os UPDATE. Cada bloco mira um package_id específico.
-- 4. Rode o SELECT [A] de novo para conferir se ficou certo.
-- =============================================================

-- [A] CONFERÊNCIA (não altera nada) — antes e depois
SELECT tp.id AS package_id, tp.trip_id, tpc.traveler_category_id,
       tc.name AS categoria, tpc.price
FROM trip_packages tp
JOIN trip_package_categories tpc ON tpc.package_id = tp.id
JOIN traveler_categories tc ON tc.id = tpc.traveler_category_id
WHERE tp.trip_id IN (51, 71, 72, 65, 58, 57)
ORDER BY tp.trip_id, tpc.traveler_category_id;


-- ---- Saona VIP Mano Juan (trip 51, package 34): adulto 129 / criança 69 ----
UPDATE trip_package_categories SET price = 129.00 WHERE package_id = 34 AND traveler_category_id = 1;
UPDATE trip_package_categories SET price = 69.00  WHERE package_id = 34 AND traveler_category_id = 2;
UPDATE trip_package_categories SET price = 0.00   WHERE package_id = 34 AND traveler_category_id = 6;

-- ---- Saona Premium Brasil (trip 71, package 54): adulto 129 / criança 79 ----
UPDATE trip_package_categories SET price = 129.00 WHERE package_id = 54 AND traveler_category_id = 1;
UPDATE trip_package_categories SET price = 79.00  WHERE package_id = 54 AND traveler_category_id = 2;
UPDATE trip_package_categories SET price = 0.00   WHERE package_id = 54 AND traveler_category_id = 6;

-- ---- Saona Clássica (trip 72, package 55): adulto 85 / criança 49 ----
UPDATE trip_package_categories SET price = 85.00 WHERE package_id = 55 AND traveler_category_id = 1;
UPDATE trip_package_categories SET price = 49.00 WHERE package_id = 55 AND traveler_category_id = 2;
UPDATE trip_package_categories SET price = 0.00  WHERE package_id = 55 AND traveler_category_id = 6;

-- ---- Golfinho: Interação com 1 golfinho (trip 65, package 48): adulto 120 / criança 120 ----
UPDATE trip_package_categories SET price = 120.00 WHERE package_id = 48 AND traveler_category_id = 1;
UPDATE trip_package_categories SET price = 120.00 WHERE package_id = 48 AND traveler_category_id = 2;
UPDATE trip_package_categories SET price = 0.00   WHERE package_id = 48 AND traveler_category_id = 6;

-- ---- Golfinho: Nado e interação com 1 golfinho (trip 58, package 41): adulto 155 / criança 120 ----
UPDATE trip_package_categories SET price = 155.00 WHERE package_id = 41 AND traveler_category_id = 1;
UPDATE trip_package_categories SET price = 120.00 WHERE package_id = 41 AND traveler_category_id = 2;
UPDATE trip_package_categories SET price = 0.00   WHERE package_id = 41 AND traveler_category_id = 6;

-- ---- Golfinho: Nado e interação com 2 golfinhos (trip 57, package 40): adulto 199 / criança 120 ----
UPDATE trip_package_categories SET price = 199.00 WHERE package_id = 40 AND traveler_category_id = 1;
UPDATE trip_package_categories SET price = 120.00 WHERE package_id = 40 AND traveler_category_id = 2;
UPDATE trip_package_categories SET price = 0.00   WHERE package_id = 40 AND traveler_category_id = 6;
