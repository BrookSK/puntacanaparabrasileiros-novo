-- Migration: Restaurar preços de passeios que perderam dados
-- Data: 2026-08-10
-- Corrige pacotes que tiveram preços apagados pelo bug do savePackages

-- Restaurar preços do Buggies + Cenote Domitai (Adulto: $55, Criança: $40, Infantil: $0)
INSERT IGNORE INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 1, 55.00, NULL
FROM trip_packages tp
INNER JOIN trips t ON tp.trip_id = t.id
WHERE t.slug LIKE '%buggies%cenote%domitai%'
LIMIT 1;

INSERT IGNORE INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 2, 40.00, NULL
FROM trip_packages tp
INNER JOIN trips t ON tp.trip_id = t.id
WHERE t.slug LIKE '%buggies%cenote%domitai%'
LIMIT 1;

INSERT IGNORE INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 6, 0.00, NULL
FROM trip_packages tp
INNER JOIN trips t ON tp.trip_id = t.id
WHERE t.slug LIKE '%buggies%cenote%domitai%'
LIMIT 1;
