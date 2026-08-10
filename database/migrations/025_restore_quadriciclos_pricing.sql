-- Migration: Restaurar preços do passeio Quadriciclos + Cenote
-- Data: 2026-08-10

INSERT IGNORE INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 1, 45.00, NULL
FROM trip_packages tp
INNER JOIN trips t ON tp.trip_id = t.id
WHERE t.slug LIKE '%quadriciclo%cenote%'
LIMIT 1;

INSERT IGNORE INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 2, 30.00, NULL
FROM trip_packages tp
INNER JOIN trips t ON tp.trip_id = t.id
WHERE t.slug LIKE '%quadriciclo%cenote%'
LIMIT 1;

INSERT IGNORE INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, 6, 0.00, NULL
FROM trip_packages tp
INNER JOIN trips t ON tp.trip_id = t.id
WHERE t.slug LIKE '%quadriciclo%cenote%'
LIMIT 1;
