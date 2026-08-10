-- Migration: Vincular categorias padrão (Adulto, Criança, Infantil) a pacotes sem categorias
-- Data: 2026-08-10
-- Corrige pacotes que foram criados sem categorias de viajante vinculadas

-- Inserir Adulto (id=1) para pacotes que não têm nenhuma categoria
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 1, 0.00, NULL
FROM trip_packages tp
WHERE NOT EXISTS (
    SELECT 1 FROM trip_package_categories tpc WHERE tpc.package_id = tp.id
);

-- Inserir Criança (id=2) para pacotes que agora só têm Adulto
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 2, 0.00, NULL
FROM trip_packages tp
WHERE EXISTS (
    SELECT 1 FROM trip_package_categories tpc WHERE tpc.package_id = tp.id AND tpc.traveler_category_id = 1
)
AND NOT EXISTS (
    SELECT 1 FROM trip_package_categories tpc WHERE tpc.package_id = tp.id AND tpc.traveler_category_id = 2
);

-- Inserir Infantil (id=6) para pacotes que não têm Infantil
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 6, 0.00, NULL
FROM trip_packages tp
WHERE EXISTS (
    SELECT 1 FROM trip_package_categories tpc WHERE tpc.package_id = tp.id AND tpc.traveler_category_id = 1
)
AND NOT EXISTS (
    SELECT 1 FROM trip_package_categories tpc WHERE tpc.package_id = tp.id AND tpc.traveler_category_id = 6
);
