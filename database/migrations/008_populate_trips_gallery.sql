-- Migration 008: Popular campo gallery dos passeios com imagens adicionais
-- Adiciona imagens do site WordPress antigo para permitir o carrossel nos cards

-- 1. Buggies + Cenote Domitai
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0101.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/IMG_6370-1.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg"]'
WHERE slug = 'buggies-cenote-domitai' AND (gallery IS NULL OR gallery = '');

-- 2. Quadriciclos + Cenote
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/IMG_6370-1.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0101.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg"]'
WHERE slug = 'quadriciclos-cenote' AND (gallery IS NULL OR gallery = '');

-- 3. Saona VIP Mano Juan - Lancha
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/21f72a17-03d9-43a8-99f7-39c03d664ff2.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/70edfaca-8405-44a3-be02-2ae5c68249d6-990x490.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png"]'
WHERE slug = 'saona-vip-mano-juan-lancha' AND (gallery IS NULL OR gallery = '');

-- 4. La Hacienda Park
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/IMG_9039.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/IMG_6370-1.jpeg"]'
WHERE slug = 'la-hacienda-park' AND (gallery IS NULL OR gallery = '');

-- 5. Chic Cabaret & Restaurant
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/b4a7ee99-71aa-4181-a0f9-9e457cd218da.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg"]'
WHERE slug = 'chic-cabaret-restaurant' AND (gallery IS NULL OR gallery = '');

-- 6. Coco Bongo - Front Row
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/b4a7ee99-71aa-4181-a0f9-9e457cd218da.jpeg"]'
WHERE slug = 'coco-bongo-front-row' AND (gallery IS NULL OR gallery = '');

-- 7. Coco Bongo - Gold Member
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/b4a7ee99-71aa-4181-a0f9-9e457cd218da.jpeg"]'
WHERE slug = 'coco-bongo-gold-member' AND (gallery IS NULL OR gallery = '');

-- 8. Coco Bongo - Open Bar
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/b4a7ee99-71aa-4181-a0f9-9e457cd218da.jpeg"]'
WHERE slug = 'coco-bongo-open-bar' AND (gallery IS NULL OR gallery = '');

-- 9. Nado e interação com 2 Golfinhos
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0135.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0138.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0148.jpg"]'
WHERE slug = 'nado-e-interacao-com-2-golfinhos' AND (gallery IS NULL OR gallery = '');

-- 10. Nado e interação com 1 Golfinho
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0138.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0135.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0148.jpg"]'
WHERE slug = 'nado-e-interacao-com-golfinho' AND (gallery IS NULL OR gallery = '');

-- 11. Supreme Safari
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/08/IMG_5872-scaled-1.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/IMG_6370-1.jpeg"]'
WHERE slug = 'supreme-safari' AND (gallery IS NULL OR gallery = '');

-- 12. Samaná
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0086.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0091-1.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png"]'
WHERE slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado' AND (gallery IS NULL OR gallery = '');

-- 13. Scuba Doo
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0031.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250524-WA0191.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0035.jpg"]'
WHERE slug = 'scuba-doo-aventura-submarina' AND (gallery IS NULL OR gallery = '');

-- 14. Pesca em Alto Mar
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0088.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0081.jpg"]'
WHERE slug = 'pesca-em-alto-mar' AND (gallery IS NULL OR gallery = '');

-- 15. Festa no Catamarã (Party Boat)
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0081.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0088.jpg"]'
WHERE slug = 'festa-no-catamara-party-boat' AND (gallery IS NULL OR gallery = '');

-- 16. Seaquarium
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250524-WA0191.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0031.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0035.jpg"]'
WHERE slug = 'seaquarium' AND (gallery IS NULL OR gallery = '');

-- 17. Interação com Golfinho
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0148.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0135.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0138.jpg"]'
WHERE slug = 'interacao-com-golfinho' AND (gallery IS NULL OR gallery = '');

-- 18. Parasailing
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0035.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png"]'
WHERE slug = 'parasailing' AND (gallery IS NULL OR gallery = '');

-- 19. Santo Domingo
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0052.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg"]'
WHERE slug = 'santo-domingo' AND (gallery IS NULL OR gallery = '');

-- 20. Scape Park + Cenote
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_9698-scaled.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/IMG_6370-1.jpeg"]'
WHERE slug = 'scape-park-cenote' AND (gallery IS NULL OR gallery = '');

-- 21. Isla Catalina com Snorkel
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0079.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0091-1.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png"]'
WHERE slug = 'isla-catalina-snorkel' AND (gallery IS NULL OR gallery = '');

-- 22. Isla Catalina com Snorkel + Altos de Chavón
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0091-1.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0079.jpg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg"]'
WHERE slug = 'isla-catalina-altos-de-chavon' AND (gallery IS NULL OR gallery = '');

-- 23. Saona Premium Brasil - Lancha
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/70edfaca-8405-44a3-be02-2ae5c68249d6-990x490.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/4.png"]'
WHERE slug = 'saona-premium-brasil-lancha-ida-e-volta' AND (gallery IS NULL OR gallery = '');

-- 24. Saona Clássica – Catamarã
UPDATE trips SET gallery = '["https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_0948.jpeg","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png","https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/4.png"]'
WHERE slug = 'saona-classica' AND (gallery IS NULL OR gallery = '');
