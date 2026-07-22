-- Migration: Insert trips from old WordPress site
-- Date: 2025-01-01
-- Description: Migrates all 24 trips from the old puntacanaparabrasileiros.com WordPress site

START TRANSACTION;

-- =====================================================
-- INSERT TRIPS
-- =====================================================

INSERT IGNORE INTO trips (
    title, slug, description, short_description, featured_image,
    duration, duration_unit, min_pax, max_pax,
    sort_order, featured, status, created_at, updated_at
) VALUES
-- 1. Buggies + Cenote Domitai
(
    'Buggies + Cenote Domitai',
    'buggies-cenote-domitai',
    'Explore as estradas de Macao em Punta Cana dirigindo buggies com muita diversão. Percorra trilhas, lama, descubra cenotes de águas azuis e experimente produtos típicos dominicanos.',
    'Prepare-se para explorar as estradas de Macao, em Punta Cana, dirigindo com facilidade e muita diversão. Aproveite as paisagens deslumbrantes, percorra trilhas e lama, descubra e nade em um cenote de águas azuis escondida no caminho e experimente produtos típicos dominicanos.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0101.jpg',
    '4', 'hours', 1, 20,
    1, 1, 'published', NOW(), NOW()
),
-- 2. Quadriciclos + Cenote
(
    'Quadriciclos + Cenote',
    'quadriciclos-cenote',
    'Explore as estradas de Macao em Punta Cana pilotando quadriciclos. Percorra trilhas, lama, descubra cenotes de águas azuis e experimente produtos típicos dominicanos.',
    'Prepare-se para explorar as estradas de Macao, em Punta Cana, dirigindo com facilidade e muita diversão. Aproveite as paisagens deslumbrantes, percorra trilhas e lama, descubra e nade em um cenote de águas azuis escondida no caminho e experimente produtos típicos dominicanos.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/IMG_6370-1.jpeg',
    '4', 'hours', 1, 20,
    2, 1, 'published', NOW(), NOW()
),
-- 3. Saona VIP Mano Juan - Lancha
(
    'Saona VIP Mano Juan - Lancha',
    'saona-vip-mano-juan-lancha',
    'Um dia no mar do Caribe com águas azuis infinitas, brisa salgada e paradas que parecem cenas de cartão-postal. Passeio de lancha até Saona com parada em Mano Juan.',
    'Imagine um dia em que o mar do Caribe te abraça com águas azuis infinitas, onde a brisa salgada acaricia o rosto e cada parada parece uma cena de cartão-postal.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/21f72a17-03d9-43a8-99f7-39c03d664ff2.jpeg',
    '10', 'hours', 1, 30,
    3, 0, 'published', NOW(), NOW()
),
-- 4. La Hacienda Park (Fazenda)
(
    'La Hacienda Park (Fazenda)',
    'la-hacienda-park',
    'No coração da natureza dominicana, cada trilha revela aventura e encanto. Passeio com buggies, cavalos e rios cristalinos em uma fazenda típica.',
    'No coração da natureza, cada trilha revela aventura e encanto. Entre buggies, cavalos e rios cristalinos, a alma se enche de liberdade.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/IMG_9039.jpeg',
    '8', 'hours', 1, 30,
    4, 0, 'published', NOW(), NOW()
),
-- 5. Chic Cabaret & Restaurant
(
    'Chic Cabaret & Restaurant',
    'chic-cabaret-restaurant',
    'Experiência que mistura música, dança e alta gastronomia em um show inesquecível. Luzes, câmera, sabores e emoção em um só lugar.',
    'Luzes, câmera, sabores e emoção em um só lugar! O Chic Cabaret & Restaurant é uma experiência que mistura música, dança e alta gastronomia em um show inesquecível.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/b4a7ee99-71aa-4181-a0f9-9e457cd218da.jpeg',
    '6', 'hours', 1, 50,
    5, 0, 'published', NOW(), NOW()
),
-- 6. Coco Bongo - Front Row
(
    'Coco Bongo - Front Row',
    'coco-bongo-front-row',
    'A casa noturna mais famosa de Punta Cana e do Caribe. Ingresso Front Row com lugar privilegiado para os espetáculos, shows e performances circenses.',
    'A Coco Bongo é a casa noturna mais famosa de Punta Cana e do Caribe. É referência em diversão, espetáculos, shows, performances circenses, balada, boa música e gente bonita.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg',
    '5', 'hours', 1, 50,
    6, 0, 'published', NOW(), NOW()
),
-- 7. Coco Bongo - Gold Member
(
    'Coco Bongo - Gold Member',
    'coco-bongo-gold-member',
    'A casa noturna mais famosa de Punta Cana e do Caribe. Ingresso Gold Member com área VIP, open bar premium e experiência exclusiva.',
    'A Coco Bongo é a casa noturna mais famosa de Punta Cana e do Caribe. É referência em diversão, espetáculos, shows, performances circenses, balada, boa música e gente bonita.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg',
    '5', 'hours', 1, 50,
    7, 0, 'published', NOW(), NOW()
),
-- 8. Coco Bongo - Open Bar
(
    'Coco Bongo - Open Bar',
    'coco-bongo-open-bar',
    'A casa noturna mais famosa de Punta Cana e do Caribe. Ingresso Open Bar com bebidas incluídas, espetáculos e muita diversão.',
    'A Coco Bongo é a casa noturna mais famosa de Punta Cana e do Caribe. É referência em diversão, espetáculos, shows, performances circenses, balada, boa música e gente bonita.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg',
    '5', 'hours', 1, 50,
    8, 0, 'published', NOW(), NOW()
),
-- 9. Nado e interação com 2 Golfinhos
(
    'Nado e interação com 2 Golfinhos',
    'nado-e-interacao-com-2-golfinhos',
    'Interaja e nade com 2 golfinhos em uma plataforma onde todos ficam de pé enquanto eles se aproximam. Experiência única no Caribe.',
    'Você irá interagir e nadar com 2 golfinhos em uma plataforma onde todos ficam de pé enquanto eles se aproximam.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0135.jpg',
    '4', 'hours', 1, 10,
    9, 0, 'published', NOW(), NOW()
),
-- 10. Nado e interação com 1 Golfinho
(
    'Nado e interação com 1 Golfinho',
    'nado-e-interacao-com-golfinho',
    'Interaja e nade com 1 golfinho em uma plataforma onde todos ficam de pé enquanto eles se aproximam. Experiência única no Caribe.',
    'Você irá interagir e nadar com 1 golfinho em uma plataforma onde todos ficam de pé enquanto eles se aproximam.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0138.jpg',
    '4', 'hours', 1, 10,
    10, 1, 'published', NOW(), NOW()
),
-- 11. Supreme Safari
(
    'Supreme Safari',
    'supreme-safari',
    'Safari dominicano combinando cultura, natureza e diversão em um dia inesquecível a bordo de caminhões turísticos confortáveis.',
    'Experimente a emoção de um Safari dominicano, combinando cultura, natureza e diversão em um dia inesquecível a bordo de nossos caminhões turísticos confortáveis.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/08/IMG_5872-scaled-1.jpeg',
    '8', 'hours', 1, 30,
    11, 0, 'published', NOW(), NOW()
),
-- 12. Samaná
(
    'Samaná (Playa Rincon + City Tour Panorâmico + Cayo Levantado)',
    'samana-playa-rincon-city-tour-panoramico-cayo-levantado',
    'Jornada inesquecível por Samaná com rios cristalinos, manguezais encantados e a famosa Praia Rincón, eleita uma das mais bonitas do mundo.',
    'Viva uma jornada inesquecível por Samaná: rios cristalinos, manguezais encantados e a famosa Praia Rincón, eleita uma das mais bonitas do mundo.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0086.jpg',
    '10', 'hours', 1, 30,
    12, 0, 'published', NOW(), NOW()
),
-- 13. Scuba Doo
(
    'Scuba Doo – Aventura Submarina 3 em 1',
    'scuba-doo-aventura-submarina',
    'Experiência única explorando o fundo do mar de Punta Cana de forma divertida, segura e inesquecível com scooters submarinos.',
    'Viva uma experiência única explorando o fundo do mar de Punta Cana de uma forma divertida, segura e inesquecível.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0031.jpg',
    '4', 'hours', 1, 15,
    13, 0, 'published', NOW(), NOW()
),
-- 14. Pesca em Alto Mar
(
    'Pesca em Alto Mar',
    'pesca-em-alto-mar',
    'Pesca esportiva no azul do Caribe. Desafie os gigantes do mar como Mahi-Mahi, Marlin e Wahoo em uma experiência emocionante.',
    'Pesque no azul do Caribe e desafie os gigantes do mar. Entre Mahi-Mahi, Marlin e Wahoo, cada lance é pura emoção.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0088.jpg',
    '4', 'hours', 1, 10,
    14, 0, 'published', NOW(), NOW()
),
-- 15. Festa no Catamarã (Party Boat)
(
    'Festa no Catamarã (Party Boat)',
    'festa-no-catamara-party-boat',
    'A festa mais animada do Caribe em um catamarã. Música, drinks e paisagens de tirar o fôlego em alto mar.',
    'Embarque no nosso catamarã e viva a festa mais animada do Caribe! A Party Boat é pura diversão: música, drinks e paisagens de tirar o fôlego.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0081.jpg',
    '4', 'hours', 1, 40,
    15, 0, 'published', NOW(), NOW()
),
-- 16. Seaquarium
(
    'Seaquarium',
    'seaquarium',
    'Caminhe no fundo do mar e descubra um mundo encantado entre peixes, corais e as águas cristalinas do Caribe.',
    'Caminhe no fundo do mar e descubra um mundo encantado, entre peixes, corais e sorrisos que dançam nas águas do Caribe.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250524-WA0191.jpg',
    '5', 'hours', 1, 20,
    16, 0, 'published', NOW(), NOW()
),
-- 17. Interação com Golfinho
(
    'Interação com Golfinho',
    'interacao-com-golfinho',
    'Interaja com o golfinho por 40 minutos tocando, abraçando e beijando em uma plataforma onde todos ficam de pé.',
    'Você irá interagir com o golfinho em uma plataforma onde todos ficam de pé enquanto eles se aproximam. Irá interagir com o golfinho por 40 minutos tocando, abraçando, beijando.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0148.jpg',
    '4', 'hours', 1, 10,
    17, 0, 'published', NOW(), NOW()
),
-- 18. Parasailing
(
    'Parasailing',
    'parasailing',
    'Voe sobre o azul do Caribe preso a um paraquedas puxado por lancha. O vento te guia e o mar te abraça lá de cima.',
    'Sinta a liberdade de voar sobre o azul do Caribe! No Parasailing, o vento te guia e o mar te abraça lá de cima.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0035.jpg',
    '2', 'hours', 1, 6,
    18, 0, 'published', NOW(), NOW()
),
-- 19. Santo Domingo
(
    'Santo Domingo',
    'santo-domingo',
    'Explore a primeira cidade do Novo Mundo, onde a história de Colombo ainda vive entre pedras antigas. City tour completo pela capital dominicana.',
    'Explore a alma da primeira cidade do Novo Mundo, onde a história de Colombo ainda sussurra entre pedras antigas.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0052.jpg',
    '10', 'hours', 1, 30,
    19, 0, 'published', NOW(), NOW()
),
-- 20. Scape Park + Cenote
(
    'Scape Park + Cenote',
    'scape-park-cenote',
    'Parque de aventuras onde a adrenalina encontra a magia da natureza. Tirolesas, cenotes, trilhas e muito mais entre céu e terra.',
    'Um mundo de sensações te espera entre céu e terra, onde a adrenalina encontra a magia da natureza.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_9698-scaled.jpeg',
    '7', 'hours', 1, 20,
    20, 0, 'published', NOW(), NOW()
),
-- 21. Isla Catalina com Snorkel
(
    'Isla Catalina com Snorkel',
    'isla-catalina-snorkel',
    'Mergulhe nas águas cristalinas da Isla Catalina com snorkel. Sol, mar e vida marinha dançando entre corais coloridos.',
    'Mergulhe nas águas cristalinas da Isla Catalina, onde o sol beija o mar e a vida dança entre corais.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/06/IMG-20250530-WA0079.jpg',
    '10', 'hours', 1, 30,
    21, 0, 'published', NOW(), NOW()
),
-- 22. Isla Catalina com Snorkel + Altos de Chavón
(
    'Isla Catalina com Snorkel + Altos de Chavón',
    'isla-catalina-altos-de-chavon',
    'Combinação perfeita: mergulho com snorkel na Isla Catalina e visita ao vilarejo artístico de Altos de Chavón com sua arquitetura mediterrânea.',
    'Entre pedras e memórias, Altos de Chavón revela sua magia, Isla Catalina convida ao mergulho em um mar de cores e vida.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0091-1.jpg',
    '10', 'hours', 1, 30,
    22, 0, 'published', NOW(), NOW()
),
-- 23. Saona Premium Brasil - Lancha
(
    'Saona Premium Brasil - Lancha',
    'saona-premium-brasil-lancha-ida-e-volta',
    'Exclusividade da Punta Cana para Brasileiros. Lancha ida e volta. A Saona Premium Brasil nasceu para o viajante brasileiro que busca mais do que um simples roteiro VIP.',
    'Exclusividade da Punta Cana para Brasileiros. Lancha ida e volta. A Saona Premium Brasil nasceu para o viajante brasileiro que busca mais do que um simples roteiro VIP.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/70edfaca-8405-44a3-be02-2ae5c68249d6-990x490.jpeg',
    '9', 'hours', 1, 30,
    23, 1, 'published', NOW(), NOW()
),
-- 24. Saona Clássica – Catamarã
(
    'Saona Clássica – Catamarã',
    'saona-classica',
    'A Saona Clássica é um convite para viver um dia no paraíso: águas azul-turquesa, areia branca e paisagens de tirar o fôlego em catamarã.',
    'A Saona Clássica é um convite para viver um dia no paraíso: águas azul-turquesa, areia branca e paisagens de tirar o fôlego.',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_0948.jpeg',
    '1', 'days', 1, 40,
    24, 0, 'published', NOW(), NOW()
);

-- =====================================================
-- INSERT TRIP PACKAGES (one "Padrão" package per trip)
-- =====================================================

INSERT IGNORE INTO trip_packages (trip_id, name, description, min_pax, max_pax, sort_order, created_at, updated_at)
SELECT t.id, 'Padrão', 'Pacote padrão', t.min_pax, t.max_pax, 1, NOW(), NOW()
FROM trips t
WHERE t.slug IN (
    'buggies-cenote-domitai',
    'quadriciclos-cenote',
    'saona-vip-mano-juan-lancha',
    'la-hacienda-park',
    'chic-cabaret-restaurant',
    'coco-bongo-front-row',
    'coco-bongo-gold-member',
    'coco-bongo-open-bar',
    'nado-e-interacao-com-2-golfinhos',
    'nado-e-interacao-com-golfinho',
    'supreme-safari',
    'samana-playa-rincon-city-tour-panoramico-cayo-levantado',
    'scuba-doo-aventura-submarina',
    'pesca-em-alto-mar',
    'festa-no-catamara-party-boat',
    'seaquarium',
    'interacao-com-golfinho',
    'parasailing',
    'santo-domingo',
    'scape-park-cenote',
    'isla-catalina-snorkel',
    'isla-catalina-altos-de-chavon',
    'saona-premium-brasil-lancha-ida-e-volta',
    'saona-classica'
)
AND NOT EXISTS (
    SELECT 1 FROM trip_packages tp WHERE tp.trip_id = t.id AND tp.name = 'Padrão'
);

-- =====================================================
-- INSERT TRIP PACKAGE PRICING (per_person, Adulto)
-- =====================================================

-- We use a subquery to get the package_id from trip_packages joined with trips by slug
INSERT IGNORE INTO trip_package_pricing (package_id, traveler_category_id, price_type, price, created_at, updated_at)
SELECT tp.id, 1, 'per_person', prices.price, NOW(), NOW()
FROM trip_packages tp
INNER JOIN trips t ON t.id = tp.trip_id
INNER JOIN (
    SELECT 'buggies-cenote-domitai' AS slug, 55.00 AS price
    UNION ALL SELECT 'quadriciclos-cenote', 65.00
    UNION ALL SELECT 'saona-vip-mano-juan-lancha', 69.00
    UNION ALL SELECT 'la-hacienda-park', 99.00
    UNION ALL SELECT 'chic-cabaret-restaurant', 180.00
    UNION ALL SELECT 'coco-bongo-front-row', 190.00
    UNION ALL SELECT 'coco-bongo-gold-member', 170.00
    UNION ALL SELECT 'coco-bongo-open-bar', 90.00
    UNION ALL SELECT 'nado-e-interacao-com-2-golfinhos', 199.00
    UNION ALL SELECT 'nado-e-interacao-com-golfinho', 155.00
    UNION ALL SELECT 'supreme-safari', 45.00
    UNION ALL SELECT 'samana-playa-rincon-city-tour-panoramico-cayo-levantado', 89.00
    UNION ALL SELECT 'scuba-doo-aventura-submarina', 70.00
    UNION ALL SELECT 'pesca-em-alto-mar', 70.00
    UNION ALL SELECT 'festa-no-catamara-party-boat', 59.00
    UNION ALL SELECT 'seaquarium', 89.00
    UNION ALL SELECT 'interacao-com-golfinho', 120.00
    UNION ALL SELECT 'parasailing', 60.00
    UNION ALL SELECT 'santo-domingo', 49.00
    UNION ALL SELECT 'scape-park-cenote', 69.00
    UNION ALL SELECT 'isla-catalina-snorkel', 55.00
    UNION ALL SELECT 'isla-catalina-altos-de-chavon', 59.00
    UNION ALL SELECT 'saona-premium-brasil-lancha-ida-e-volta', 79.00
    UNION ALL SELECT 'saona-classica', 49.00
) AS prices ON prices.slug = t.slug
WHERE tp.name = 'Padrão'
AND NOT EXISTS (
    SELECT 1 FROM trip_package_pricing tpp
    WHERE tpp.package_id = tp.id AND tpp.traveler_category_id = 1
);

COMMIT;
