-- Migration: Migrar transfers completos do site antigo (locais + rotas + tarifas)
-- Data: 2026-08-07
-- Fonte: wp_cad131_posts (transfer_location) + _vehicle_routes serializado
-- Veículo: Transfer Compartilhado (208 rotas, 121 locais)

SET NAMES utf8mb4;

-- ============================================================
-- PARTE 1: INSERIR TODOS OS LOCAIS
-- ============================================================

INSERT IGNORE INTO `transfer_locations` (`title`, `slug`, `address`, `location_type`, `sort_order`, `status`) VALUES
('Aquamarina', 'aquamarina', 'Aquamarina Beach Hotel, Punta Cana, RD', 'hotel', 20, 1),
('Bahia Principe Bavaro', 'bahia-principe-bavaro', 'Bahia Principe Grand Bavaro, RD', 'hotel', 21, 1),
('Bahia Principe Fantasia', 'bahia-principe-fantasia', 'Bahia Principe Fantasia, Bavaro, RD', 'hotel', 22, 1),
('Bahia Principe Grand Aquamarine', 'bahia-principe-grand-aquamarine', 'Bahia Principe Grand Aquamarine, Bavaro, RD', 'hotel', 23, 1),
('Bahia Principe La Romana', 'bahia-principe-la-romana', 'Bahia Principe Grand La Romana, RD', 'hotel', 24, 1),
('Bahia Principe Punta Cana', 'bahia-principe-punta-cana', 'Bahia Principe Grand Punta Cana, RD', 'hotel', 25, 1),
('Barcelo Bavaro Beach', 'barcelo-bavaro-beach', 'Barcelo Bavaro Beach, Punta Cana, RD', 'hotel', 26, 1),
('Barcelo Palace', 'barcelo-palace', 'Barcelo Bavaro Palace, Punta Cana, RD', 'hotel', 27, 1),
('Be Live Hamaca', 'be-live-hamaca', 'Be Live Experience Hamaca, Boca Chica, RD', 'hotel', 28, 1),
('BelleVue Dominican Bay', 'bellevue-dominican-bay', 'BelleVue Dominican Bay, Boca Chica, RD', 'hotel', 29, 1),
('Breathless', 'breathless', 'Breathless Punta Cana Resort, Uvero Alto, RD', 'hotel', 30, 1),
('Cadaques', 'cadaques', 'Cadaques Caribe, Bayahibe, RD', 'hotel', 31, 1),
('Caribe Club Princess', 'caribe-club-princess', 'Caribe Club Princess, Bavaro, RD', 'hotel', 32, 1),
('Casa de Campo', 'casa-de-campo', 'Casa de Campo Resort, La Romana, RD', 'resort', 33, 1),
('Catalonia Bavaro', 'catalonia-bavaro', 'Catalonia Bavaro Beach Resort, RD', 'hotel', 34, 1),
('Catalonia Dominicus', 'catalonia-dominicus', 'Catalonia Gran Dominicus, Bayahibe, RD', 'hotel', 35, 1),
('Catalonia Royal', 'catalonia-royal', 'Catalonia Royal Bavaro, RD', 'hotel', 36, 1),
('Club Med Miches Playa Esmeralda', 'club-med-miches', 'Club Med Miches Playa Esmeralda, RD', 'hotel', 37, 1),
('Club Med Punta Cana', 'club-med-punta-cana', 'Club Med Punta Cana, RD', 'hotel', 38, 1),
('Coral Cana Bay', 'coral-cana-bay', 'Coral Cana Bay, Punta Cana, RD', 'hotel', 39, 1),
('Coral Costa Caribe', 'coral-costa-caribe', 'Coral Costa Caribe, Juan Dolio, RD', 'hotel', 40, 1),
('Cortecito Inn', 'cortecito-inn', 'Cortecito Inn, Bavaro, RD', 'hotel', 41, 1),
('Dreams Dominicus', 'dreams-dominicus', 'Dreams Dominicus La Romana, Bayahibe, RD', 'hotel', 42, 1),
('Dreams Flora', 'dreams-flora', 'Dreams Flora Resort, Bavaro, RD', 'hotel', 43, 1),
('Dreams Macao', 'dreams-macao', 'Dreams Macao Beach, Punta Cana, RD', 'hotel', 44, 1),
('Dreams Onyx', 'dreams-onyx', 'Dreams Onyx Resort, Punta Cana, RD', 'hotel', 45, 1),
('Dreams Royal Beach', 'dreams-royal-beach', 'Dreams Royal Beach Punta Cana, RD', 'hotel', 46, 1),
('Eden Roc', 'eden-roc', 'Eden Roc Cap Cana, RD', 'hotel', 47, 1),
('Emotions by Hodelpa Juan Dolio', 'emotions-hodelpa-juan-dolio', 'Emotions by Hodelpa, Juan Dolio, RD', 'hotel', 48, 1),
('Emotions by Hodelpa', 'emotions-hodelpa', 'Emotions by Hodelpa, Puerto Plata, RD', 'hotel', 49, 1),
('Excellence del Carmen', 'excellence-del-carmen', 'Excellence El Carmen, Punta Cana, RD', 'hotel', 50, 1);

INSERT IGNORE INTO `transfer_locations` (`title`, `slug`, `address`, `location_type`, `sort_order`, `status`) VALUES
('Excellence Punta Cana', 'excellence-punta-cana', 'Excellence Punta Cana, Uvero Alto, RD', 'hotel', 51, 1),
('Finest Punta Cana', 'finest-punta-cana', 'Finest Punta Cana, Uvero Alto, RD', 'hotel', 52, 1),
('Flamboyan', 'flamboyan', 'Flamboyan, Bavaro, RD', 'hotel', 53, 1),
('Four Points by Sheraton', 'four-points-sheraton', 'Four Points by Sheraton, Punta Cana, RD', 'hotel', 54, 1),
('Garden Suites / Melia', 'garden-suites-melia', 'Garden Suites by Melia, Bavaro, RD', 'hotel', 55, 1),
('Gran Bavaro Princess', 'gran-bavaro-princess', 'Gran Bavaro Princess, Bavaro, RD', 'hotel', 56, 1),
('Green Coast Beach', 'green-coast-beach', 'Green Coast Beach, Bavaro, RD', 'hotel', 57, 1),
('Green Coast Hotel', 'green-coast-hotel', 'Green Coast Hotel, Bavaro, RD', 'hotel', 58, 1),
('Hard Rock', 'hard-rock-punta-cana', 'Hard Rock Hotel & Casino Punta Cana, RD', 'hotel', 59, 1),
('Hilton La Romana', 'hilton-la-romana', 'Hilton La Romana, La Romana, RD', 'hotel', 60, 1),
('HM Alma Bavaro', 'hm-alma-bavaro', 'HM Alma Bavaro, Bavaro, RD', 'hotel', 61, 1),
('HM Alma Bayahibe', 'hm-alma-bayahibe', 'HM Alma Bayahibe, Bayahibe, RD', 'hotel', 62, 1),
('Hotel MT', 'hotel-mt', 'Hotel MT, Punta Cana, RD', 'hotel', 63, 1),
('Hyatt Zilara Cap Cana', 'hyatt-zilara-cap-cana', 'Hyatt Zilara Cap Cana, RD', 'hotel', 64, 1),
('Hyatt Ziva Cap Cana', 'hyatt-ziva-cap-cana', 'Hyatt Ziva Cap Cana, RD', 'hotel', 65, 1),
('Iberostar Bavaro', 'iberostar-bavaro', 'Iberostar Bavaro Suites, RD', 'hotel', 66, 1),
('Iberostar Dominicana', 'iberostar-dominicana', 'Iberostar Dominicana, Bavaro, RD', 'hotel', 67, 1),
('Iberostar Grand', 'iberostar-grand', 'Iberostar Grand Bavaro, RD', 'hotel', 68, 1),
('Iberostar Grand Bavaro', 'iberostar-grand-bavaro', 'Iberostar Grand Bavaro, RD', 'hotel', 69, 1),
('Iberostar Hacienda', 'iberostar-hacienda', 'Iberostar Hacienda Dominicus, Bayahibe, RD', 'hotel', 70, 1),
('Iberostar Punta Cana', 'iberostar-punta-cana', 'Iberostar Punta Cana, RD', 'hotel', 71, 1),
('Impressive Premium Punta Cana', 'impressive-premium', 'Impressive Premium Punta Cana, RD', 'hotel', 72, 1),
('Impressive Punta Cana', 'impressive-punta-cana', 'Impressive Punta Cana, Bavaro, RD', 'hotel', 73, 1),
('Jewel Palm Beach', 'jewel-palm-beach', 'Jewel Palm Beach, Punta Cana, RD', 'hotel', 74, 1),
('Jewel Punta Cana', 'jewel-punta-cana', 'Jewel Punta Cana, RD', 'hotel', 75, 1),
('Karibo', 'karibo', 'Karibo Punta Cana, RD', 'hotel', 76, 1),
('Live Aqua', 'live-aqua', 'Live Aqua Beach Resort, Punta Cana, RD', 'hotel', 77, 1),
('Lopesan Costa Bavaro', 'lopesan-costa-bavaro', 'Lopesan Costa Bavaro, RD', 'hotel', 78, 1),
('Los Corales / Residencial Citrus', 'los-corales-citrus', 'Los Corales, Bavaro, Punta Cana, RD', 'hotel', 79, 1),
('Majestic Colonial', 'majestic-colonial', 'Majestic Colonial Punta Cana, RD', 'hotel', 80, 1),
('Majestic Elegance', 'majestic-elegance', 'Majestic Elegance Punta Cana, RD', 'hotel', 81, 1),
('Majestic Mirage', 'majestic-mirage', 'Majestic Mirage Punta Cana, RD', 'hotel', 82, 1),
('Margaritaville', 'margaritaville', 'Margaritaville Cap Cana, RD', 'hotel', 83, 1),
('Melia Caribe', 'melia-caribe', 'Melia Caribe Beach, Bavaro, RD', 'hotel', 84, 1),
('Melia Punta Cana', 'melia-punta-cana', 'Melia Punta Cana Beach Resort, RD', 'hotel', 85, 1),
('Occidental Caribe', 'occidental-caribe', 'Occidental Caribe, Punta Cana, RD', 'hotel', 86, 1),
('Occidental Punta Cana', 'occidental-punta-cana', 'Occidental Punta Cana, RD', 'hotel', 87, 1),
('Ocean Blue', 'ocean-blue', 'Ocean Blue & Sand, Bavaro, RD', 'hotel', 88, 1),
('Ocean El Faro', 'ocean-el-faro', 'Ocean El Faro, Uvero Alto, RD', 'hotel', 89, 1),
('Palladium Bavaro', 'palladium-bavaro', 'Grand Palladium Bavaro Suites, RD', 'hotel', 90, 1),
('Palladium Palace', 'palladium-palace', 'Grand Palladium Palace Resort, RD', 'hotel', 91, 1),
('Palladium Punta Cana', 'palladium-punta-cana', 'Grand Palladium Punta Cana, RD', 'hotel', 92, 1),
('Palladium TRS Turquesa / Royal Suites', 'palladium-trs-turquesa', 'TRS Turquesa / Royal Suites, Bavaro, RD', 'hotel', 93, 1),
('Paradisus Grand Cana', 'paradisus-grand-cana', 'Paradisus Grand Cana, Bavaro, RD', 'hotel', 94, 1),
('Paradisus Palma Real', 'paradisus-palma-real', 'Paradisus Palma Real, Bavaro, RD', 'hotel', 95, 1),
('Paradisus Punta Cana', 'paradisus-punta-cana', 'Paradisus Punta Cana Resort, RD', 'hotel', 96, 1),
('Paradisus Punta Cana The Reserve', 'paradisus-the-reserve', 'Paradisus Punta Cana The Reserve, RD', 'hotel', 97, 1),
('Playa Palmera', 'playa-palmera', 'Playa Palmera Beach Resort, Bavaro, RD', 'hotel', 98, 1),
('Presidential Suites', 'presidential-suites', 'Presidential Suites, Punta Cana, RD', 'hotel', 99, 1),
('Punta Cana Princess', 'punta-cana-princess', 'Punta Cana Princess All Suites, RD', 'hotel', 100, 1);

INSERT IGNORE INTO `transfer_locations` (`title`, `slug`, `address`, `location_type`, `sort_order`, `status`) VALUES
('Punta Palmera', 'punta-palmera', 'Punta Palmera Cap Cana, RD', 'hotel', 101, 1),
('Radisson Blu Punta Cana', 'radisson-blu-punta-cana', 'Radisson Blu Resort, Punta Cana, RD', 'hotel', 102, 1),
('Riu Bambu', 'riu-bambu', 'Riu Bambu, Bavaro, RD', 'hotel', 103, 1),
('Riu Bavaro', 'riu-bavaro', 'Riu Bavaro, Punta Cana, RD', 'hotel', 104, 1),
('Riu Naiboa', 'riu-naiboa', 'Riu Naiboa, Punta Cana, RD', 'hotel', 105, 1),
('Riu Palace Macao', 'riu-palace-macao', 'Riu Palace Macao, RD', 'hotel', 106, 1),
('Riu Palace Punta Cana', 'riu-palace-punta-cana', 'Riu Palace Punta Cana, RD', 'hotel', 107, 1),
('Riu Republica', 'riu-republica', 'Riu Republica, Punta Cana, RD', 'hotel', 108, 1),
('Royalton Bavaro', 'royalton-bavaro', 'Royalton Bavaro Resort, RD', 'hotel', 109, 1),
('Royalton Chic', 'royalton-chic', 'Royalton CHIC Punta Cana, RD', 'hotel', 110, 1),
('Royalton Punta Cana', 'royalton-punta-cana', 'Royalton Punta Cana Resort, RD', 'hotel', 111, 1),
('Royalton Splash', 'royalton-splash', 'Royalton Splash Punta Cana, RD', 'hotel', 112, 1),
('Sanctuary', 'sanctuary-cap-cana', 'Sanctuary Cap Cana, RD', 'hotel', 113, 1),
('Secrets Cap Cana', 'secrets-cap-cana', 'Secrets Cap Cana Resort, RD', 'hotel', 114, 1),
('Secrets Royal Beach', 'secrets-royal-beach', 'Secrets Royal Beach Punta Cana, RD', 'hotel', 115, 1),
('Sensatori / Nickelodeon', 'sensatori-nickelodeon', 'Nickelodeon Hotels, Uvero Alto, RD', 'hotel', 116, 1),
('Sensatori Cap Cana', 'sensatori-cap-cana', 'Sensatori Cap Cana, RD', 'hotel', 117, 1),
('Serenade Punta Cana', 'serenade-punta-cana', 'Serenade Punta Cana Beach Resort, RD', 'hotel', 118, 1),
('Sirenis', 'sirenis', 'Grand Sirenis Punta Cana, Uvero Alto, RD', 'hotel', 119, 1),
('Sivory', 'sivory', 'Sivory Punta Cana, Uvero Alto, RD', 'hotel', 120, 1),
('Sports Illustrated', 'sports-illustrated', 'Sports Illustrated Resorts, Cap Cana, RD', 'hotel', 121, 1),
('Sunscape Canoa', 'sunscape-canoa', 'Sunscape Canoa, Bayahibe, RD', 'hotel', 122, 1),
('Sunscape Coco Punta Cana', 'sunscape-coco-punta-cana', 'Sunscape Coco Punta Cana, RD', 'hotel', 123, 1),
('Tortuga Bay', 'tortuga-bay', 'Tortuga Bay Hotel, Punta Cana, RD', 'hotel', 124, 1),
('Tropical Deluxe Princess', 'tropical-deluxe-princess', 'Tropical Deluxe Princess, Bavaro, RD', 'hotel', 125, 1),
('TRS Cap Cana', 'trs-cap-cana', 'TRS Cap Cana Waterfront & Marina, RD', 'hotel', 126, 1),
('VIK Arena Blanca', 'vik-arena-blanca', 'VIK Hotel Arena Blanca, Bavaro, RD', 'hotel', 127, 1),
('Vista Sol', 'vista-sol', 'Vista Sol Punta Cana, RD', 'hotel', 128, 1),
('Viva Wyndham Beach', 'viva-wyndham-beach', 'Viva Wyndham Dominicus Beach, Bayahibe, RD', 'hotel', 129, 1),
('Viva Wyndham Palace', 'viva-wyndham-palace', 'Viva Wyndham Dominicus Palace, Bayahibe, RD', 'hotel', 130, 1),
('Westin', 'westin-punta-cana', 'The Westin Puntacana Resort & Club, RD', 'hotel', 131, 1),
('Whala Bavaro', 'whala-bavaro', 'Whala Bavaro, Bavaro, RD', 'hotel', 132, 1),
('Whala Bayahibe', 'whala-bayahibe', 'Whala Bayahibe, Bayahibe, RD', 'hotel', 133, 1),
('Whala Boca Chica', 'whala-boca-chica', 'Whala Boca Chica, Boca Chica, RD', 'hotel', 134, 1),
('Whala Urban', 'whala-urban', 'Whala Urban, Punta Cana, RD', 'hotel', 135, 1),
('Zoetry', 'zoetry', 'Zoetry Agua Punta Cana, Uvero Alto, RD', 'hotel', 136, 1);


-- ============================================================
-- PARTE 2: CRIAR ROTAS COM PRECOS
-- O site antigo tinha 1 veículo (Transfer Compartilhado) com 208 rotas.
-- No site novo temos 3 veículos. Vamos criar rotas para TODOS os veículos
-- usando o preço base do veículo compartilhado.
-- Os preços e tarifas podem ser ajustados depois no admin.
-- ============================================================

-- Precisamos do ID do aeroporto no site novo
SET @airport_id = (SELECT id FROM transfer_locations WHERE slug = 'aeroporto-punta-cana' LIMIT 1);

-- Criar rotas IDA e VOLTA para cada veículo e cada local
-- Usando os preços do site antigo (base_price do veículo compartilhado)
-- Rotas com preço $35 = Bavaro/Punta Cana (25-40 min)
-- Rotas com preço $40 = Cap Cana / areas próximas (40-45 min)  
-- Rotas com preço $45 = Hard Rock / Uvero (44 min)
-- Rotas com preço $76 = Macao / Dreams Macao (50-55 min)
-- Rotas com preço $80 = Bayahibe / Excellence / Zoetry (52-65 min)
-- Rotas com preço $113 = La Romana / Hilton / Iberostar Hacienda (60-73 min)
-- Rotas com preço $145 = Boca Chica / Whala Boca Chica (100-115 min)
-- Rotas com preço $149 = Juan Dolio (90 min)
-- Rotas com preço $154 = Club Med Miches (70 min)

-- Criar rotas para TODOS os veículos (ida e volta) com preço base
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 35.00, 30, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'barcelo-palace','barcelo-bavaro-beach','caribe-club-princess',
    'catalonia-bavaro','catalonia-royal','cortecito-inn','dreams-flora',
    'dreams-onyx','dreams-royal-beach','flamboyan','garden-suites-melia',
    'gran-bavaro-princess','green-coast-beach','green-coast-hotel',
    'hm-alma-bavaro','hotel-mt','iberostar-bavaro','iberostar-dominicana',
    'iberostar-grand','iberostar-grand-bavaro','iberostar-punta-cana',
    'impressive-premium','impressive-punta-cana','jewel-palm-beach',
    'jewel-punta-cana','karibo','lopesan-costa-bavaro',
    'los-corales-citrus','majestic-colonial','majestic-elegance',
    'majestic-mirage','melia-caribe','melia-punta-cana',
    'occidental-caribe','occidental-punta-cana','ocean-blue',
    'palladium-bavaro','palladium-palace','palladium-punta-cana',
    'palladium-trs-turquesa','paradisus-grand-cana','paradisus-palma-real',
    'paradisus-punta-cana','paradisus-the-reserve','presidential-suites',
    'punta-cana-princess','radisson-blu-punta-cana','riu-bambu',
    'riu-bavaro','riu-naiboa','riu-palace-macao','riu-palace-punta-cana',
    'riu-republica','royalton-bavaro','royalton-punta-cana',
    'royalton-splash','secrets-royal-beach','serenade-punta-cana',
    'vik-arena-blanca','vista-sol','whala-bavaro','whala-urban',
    'club-med-punta-cana','tropical-deluxe-princess'
)
AND v.status = 'active';

-- Rotas VOLTA (local → aeroporto) $35
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 35.00, 30, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'barcelo-palace','barcelo-bavaro-beach','caribe-club-princess',
    'catalonia-bavaro','catalonia-royal','cortecito-inn','dreams-flora',
    'dreams-onyx','dreams-royal-beach','flamboyan','garden-suites-melia',
    'gran-bavaro-princess','green-coast-beach','green-coast-hotel',
    'hm-alma-bavaro','hotel-mt','iberostar-bavaro','iberostar-dominicana',
    'iberostar-grand','iberostar-grand-bavaro','iberostar-punta-cana',
    'impressive-premium','impressive-punta-cana','jewel-palm-beach',
    'jewel-punta-cana','karibo','lopesan-costa-bavaro',
    'los-corales-citrus','majestic-colonial','majestic-elegance',
    'majestic-mirage','melia-caribe','melia-punta-cana',
    'occidental-caribe','occidental-punta-cana','ocean-blue',
    'palladium-bavaro','palladium-palace','palladium-punta-cana',
    'palladium-trs-turquesa','paradisus-grand-cana','paradisus-palma-real',
    'paradisus-punta-cana','paradisus-the-reserve','presidential-suites',
    'punta-cana-princess','radisson-blu-punta-cana','riu-bambu',
    'riu-bavaro','riu-naiboa','riu-palace-macao','riu-palace-punta-cana',
    'riu-republica','royalton-bavaro','royalton-punta-cana',
    'royalton-splash','secrets-royal-beach','serenade-punta-cana',
    'vik-arena-blanca','vista-sol','whala-bavaro','whala-urban',
    'club-med-punta-cana','tropical-deluxe-princess'
)
AND v.status = 'active';

-- Rotas $40 (Cap Cana e arredores) - IDA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 40.00, 45, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'bahia-principe-bavaro','bahia-principe-fantasia',
    'bahia-principe-grand-aquamarine','bahia-principe-punta-cana',
    'punta-palmera','royalton-chic','eden-roc',
    'hyatt-zilara-cap-cana','hyatt-ziva-cap-cana','secrets-cap-cana',
    'sanctuary-cap-cana','sensatori-cap-cana','four-points-sheraton',
    'margaritaville','sports-illustrated','trs-cap-cana',
    'aquamarina','coral-cana-bay','tortuga-bay'
)
AND v.status = 'active';

-- Rotas $40 VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 40.00, 45, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'bahia-principe-bavaro','bahia-principe-fantasia',
    'bahia-principe-grand-aquamarine','bahia-principe-punta-cana',
    'punta-palmera','royalton-chic','eden-roc',
    'hyatt-zilara-cap-cana','hyatt-ziva-cap-cana','secrets-cap-cana',
    'sanctuary-cap-cana','sensatori-cap-cana','four-points-sheraton',
    'margaritaville','sports-illustrated','trs-cap-cana',
    'aquamarina','coral-cana-bay','tortuga-bay'
)
AND v.status = 'active';

-- Rotas $45 (Hard Rock) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 45.00, 44, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('hard-rock-punta-cana') AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 45.00, 44, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('hard-rock-punta-cana') AND v.status = 'active';

-- Rotas $76 (Dreams Macao e area) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 76.00, 50, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('dreams-macao','live-aqua') AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 76.00, 50, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('dreams-macao','live-aqua') AND v.status = 'active';

-- Rotas $80 (Bayahibe, Uvero Alto, Zoetry, Excellence, etc) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 80.00, 60, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'excellence-punta-cana','excellence-del-carmen','finest-punta-cana',
    'breathless','ocean-el-faro','sensatori-nickelodeon','sivory',
    'sirenis','zoetry','sunscape-coco-punta-cana','playa-palmera',
    'viva-wyndham-beach','viva-wyndham-palace','dreams-dominicus',
    'cadaques','hm-alma-bayahibe'
)
AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 80.00, 60, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'excellence-punta-cana','excellence-del-carmen','finest-punta-cana',
    'breathless','ocean-el-faro','sensatori-nickelodeon','sivory',
    'sirenis','zoetry','sunscape-coco-punta-cana','playa-palmera',
    'viva-wyndham-beach','viva-wyndham-palace','dreams-dominicus',
    'cadaques','hm-alma-bayahibe'
)
AND v.status = 'active';

-- Rotas $113 (La Romana, Hilton, Iberostar Hacienda, Sunscape Canoa, etc) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 113.00, 70, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'hilton-la-romana','iberostar-hacienda','catalonia-dominicus',
    'bahia-principe-la-romana','casa-de-campo','sunscape-canoa',
    'whala-bayahibe'
)
AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 113.00, 70, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN (
    'hilton-la-romana','iberostar-hacienda','catalonia-dominicus',
    'bahia-principe-la-romana','casa-de-campo','sunscape-canoa',
    'whala-bayahibe'
)
AND v.status = 'active';

-- Rotas $145 (Boca Chica) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 145.00, 100, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('whala-boca-chica','be-live-hamaca','bellevue-dominican-bay')
AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 145.00, 100, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('whala-boca-chica','be-live-hamaca','bellevue-dominican-bay')
AND v.status = 'active';

-- Rotas $149 (Juan Dolio) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 149.00, 90, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('emotions-hodelpa-juan-dolio','coral-costa-caribe')
AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 149.00, 90, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('emotions-hodelpa-juan-dolio','coral-costa-caribe')
AND v.status = 'active';

-- Rotas $154 (Club Med Miches) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 154.00, 70, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('club-med-miches') AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 154.00, 70, 1
FROM transfer_vehicles v CROSS JOIN transfer_locations l
WHERE l.slug IN ('club-med-miches') AND v.status = 'active';

-- Rotas restantes ($35 default para locais não categorizados) - IDA e VOLTA
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 35.00, 35, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.id != @airport_id
AND v.status = 'active';

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 35.00, 35, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.id != @airport_id
AND v.status = 'active';


-- ============================================================
-- PARTE 3: CRIAR TARIFAS POR FAIXA DE PASSAGEIROS
-- No site antigo, o transfer compartilhado cobrava $10/pessoa para Bavaro
-- e $15/pessoa para Cap Cana. Vamos criar tarifas para todas as rotas.
-- ============================================================

-- Tarifas para rotas de $35 (Bavaro) - $10 por pessoa (1-13 pax)
INSERT IGNORE INTO transfer_tariffs (route_id, service_type, min_pax, max_pax, price)
SELECT tr.id, 'shared', n.pax, n.pax, n.pax * 10
FROM transfer_routes tr
CROSS JOIN (
    SELECT 1 as pax UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13
) n
WHERE tr.base_price = 35.00;

-- Tarifas para rotas de $40 (Cap Cana) - $15 por pessoa (1-7 pax)
INSERT IGNORE INTO transfer_tariffs (route_id, service_type, min_pax, max_pax, price)
SELECT tr.id, 'shared', n.pax, n.pax, n.pax * 15
FROM transfer_routes tr
CROSS JOIN (
    SELECT 1 as pax UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
    UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
) n
WHERE tr.base_price = 40.00;

-- Tarifas privativas (usa o base_price como preço fixo, não por pessoa)
INSERT IGNORE INTO transfer_tariffs (route_id, service_type, min_pax, max_pax, price)
SELECT tr.id, 'private', 1, 6, tr.base_price
FROM transfer_routes tr;
