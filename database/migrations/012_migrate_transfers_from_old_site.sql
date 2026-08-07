-- Migration: Migrar todos os locais e rotas de transfer do site antigo
-- Data: 2026-08-07
-- Descrição: Adiciona todos os locais/hotéis do site WordPress antigo e cria rotas
--            para os 3 veículos já cadastrados (ida e volta) com o Aeroporto PUJ como hub.

SET NAMES utf8mb4;

-- ============================================================
-- PARTE 1: ADICIONAR TODOS OS LOCAIS QUE FALTAM
-- ============================================================

INSERT IGNORE INTO `transfer_locations` (`title`, `slug`, `address`, `location_type`, `sort_order`, `status`) VALUES
-- Hotéis/Resorts Bávaro e Punta Cana
('Iberostar Grand Bavaro', 'iberostar-grand-bavaro', 'Iberostar Grand Bávaro, Playa Bávaro, Punta Cana, RD', 'hotel', 20, 1),
('Club Med Punta Cana', 'club-med-punta-cana', 'Club Med Punta Cana, Punta Cana, RD', 'hotel', 21, 1),
('Tropical Deluxe Princess', 'tropical-deluxe-princess', 'Tropical Deluxe Princess, Playa Bávaro, Punta Cana, RD', 'hotel', 22, 1),
('Sunscape Cana', 'sunscape-cana', 'Sunscape Bávaro Beach, Punta Cana, RD', 'hotel', 23, 1),
('Dreams Dominicus', 'dreams-dominicus', 'Dreams Dominicus La Romana, Bayahibe, RD', 'hotel', 24, 1),
('Catalonia Dominicus', 'catalonia-dominicus', 'Catalonia Gran Dominicus, Bayahibe, RD', 'hotel', 25, 1),
('Dreams Macao', 'dreams-macao', 'Dreams Macao Beach, Macao, Punta Cana, RD', 'hotel', 26, 1),
('Sports Illustrated', 'sports-illustrated', 'Sports Illustrated Resorts, Cap Cana, RD', 'hotel', 27, 1),
('TRS Cap Cana', 'trs-cap-cana', 'TRS Cap Cana Waterfront & Marina, Cap Cana, RD', 'hotel', 28, 1),
('Aquamarina', 'aquamarina', 'Aquamarina Beach Hotel, Cap Cana, RD', 'hotel', 29, 1),
('Punta Palmera', 'punta-palmera', 'Punta Palmera Cap Cana, RD', 'hotel', 30, 1),
('Eden Roc', 'eden-roc', 'Eden Roc Cap Cana, RD', 'hotel', 31, 1),
('Secrets Cap Cana', 'secrets-cap-cana', 'Secrets Cap Cana Resort & Spa, RD', 'hotel', 32, 1),
('Hyatt Zilara Cap Cana', 'hyatt-zilara-cap-cana', 'Hyatt Zilara Cap Cana, RD', 'hotel', 33, 1),
('Hyatt Ziva Cap Cana', 'hyatt-ziva-cap-cana', 'Hyatt Ziva Cap Cana, RD', 'hotel', 34, 1),
('Sanctuary', 'sanctuary-cap-cana', 'Sanctuary Cap Cana, RD', 'hotel', 35, 1),
('Four Points by Sheraton', 'four-points-sheraton', 'Four Points by Sheraton Punta Cana Village, RD', 'hotel', 36, 1),
('Sensatori Cap Cana', 'sensatori-cap-cana', 'Sensatori Cap Cana, RD', 'hotel', 37, 1),
('Margaritaville', 'margaritaville', 'Margaritaville Island Reserve, Cap Cana, RD', 'hotel', 38, 1),
('Coral Cana Bay', 'coral-cana-bay', 'Coral Cana Bay, Punta Cana, RD', 'hotel', 39, 1),
('Catalonia Bavaro', 'catalonia-bavaro', 'Catalonia Bávaro Beach Resort, Bávaro, RD', 'hotel', 40, 1),
('Catalonia Royal', 'catalonia-royal', 'Catalonia Royal Bávaro, Bávaro, RD', 'hotel', 41, 1),
('Caribe Club Princess', 'caribe-club-princess', 'Caribe Club Princess Beach Resort, Bávaro, RD', 'hotel', 42, 1),
('Punta Cana Princess', 'punta-cana-princess', 'Punta Cana Princess All Suites Resort, RD', 'hotel', 43, 1),
('Barcelo Bavaro Beach', 'barcelo-bavaro-beach', 'Barceló Bávaro Beach, Punta Cana, RD', 'hotel', 44, 1),
('Barcelo Palace', 'barcelo-palace', 'Barceló Bávaro Palace, Punta Cana, RD', 'hotel', 45, 1),
('Iberostar Bavaro', 'iberostar-bavaro', 'Iberostar Bávaro Suites, Punta Cana, RD', 'hotel', 46, 1),
('Iberostar Grand', 'iberostar-grand', 'Iberostar Grand, Playa Bávaro, Punta Cana, RD', 'hotel', 47, 1),
('Iberostar Punta Cana', 'iberostar-punta-cana', 'Iberostar Punta Cana, RD', 'hotel', 48, 1),
('Iberostar Hacienda', 'iberostar-hacienda', 'Iberostar Hacienda Dominicus, Bayahibe, RD', 'hotel', 49, 1),
('Impressive Premium Punta Cana', 'impressive-premium-punta-cana', 'Impressive Premium Punta Cana, Bávaro, RD', 'hotel', 50, 1),
('Impressive Punta Cana', 'impressive-punta-cana', 'Impressive Punta Cana, Bávaro, RD', 'hotel', 51, 1),
('Jewel Palm Beach', 'jewel-palm-beach', 'Jewel Palm Beach, Punta Cana, RD', 'hotel', 52, 1),
('Jewel Punta Cana', 'jewel-punta-cana', 'Jewel Punta Cana, RD', 'hotel', 53, 1),
('Karibo', 'karibo', 'Karibo Punta Cana, RD', 'hotel', 54, 1),
('Live Aqua', 'live-aqua', 'Live Aqua Beach Resort, Punta Cana, RD', 'hotel', 55, 1),
('Lopesan Costa Bavaro', 'lopesan-costa-bavaro', 'Lopesan Costa Bávaro Resort, RD', 'hotel', 56, 1),
('Los Corales / Residencial Cibao', 'los-corales-residencial-cibao', 'Los Corales, Bávaro, Punta Cana, RD', 'hotel', 57, 1),
('Majestic Colonial', 'majestic-colonial', 'Majestic Colonial Punta Cana, RD', 'hotel', 58, 1),
('Majestic Elegance', 'majestic-elegance', 'Majestic Elegance Punta Cana, RD', 'hotel', 59, 1),
('Majestic Mirage', 'majestic-mirage', 'Majestic Mirage Punta Cana, RD', 'hotel', 60, 1),
('Melia Caribe', 'melia-caribe', 'Meliá Caribe Beach Resort, Bávaro, RD', 'hotel', 61, 1),
('Melia Punta Cana', 'melia-punta-cana', 'Meliá Punta Cana Beach Resort, RD', 'hotel', 62, 1),
('Occidental Caribe', 'occidental-caribe', 'Occidental Caribe, Punta Cana, RD', 'hotel', 63, 1),
('Occidental Punta Cana', 'occidental-punta-cana', 'Occidental Punta Cana, RD', 'hotel', 64, 1),
('Ocean Blue', 'ocean-blue', 'Ocean Blue & Sand, Bávaro, Punta Cana, RD', 'hotel', 65, 1),
('Ocean El Faro', 'ocean-el-faro', 'Ocean El Faro Resort, Uvero Alto, RD', 'hotel', 66, 1),
('Palladium Bavaro', 'palladium-bavaro', 'Grand Palladium Bávaro Suites, RD', 'hotel', 67, 1),
('Palladium Palace', 'palladium-palace', 'Grand Palladium Palace Resort, Bávaro, RD', 'hotel', 68, 1),
('Palladium Punta Cana', 'palladium-punta-cana', 'Grand Palladium Punta Cana Resort, RD', 'hotel', 69, 1),
('Palladium TRS Turquesa / Royal Suites', 'palladium-trs-turquesa', 'TRS Turquesa / The Royal Suites Turquesa, Bávaro, RD', 'hotel', 70, 1),
('Paradisus Grand Cana', 'paradisus-grand-cana', 'Paradisus Grand Cana, Bávaro, RD', 'hotel', 71, 1),
('Paradisus Palma Real', 'paradisus-palma-real', 'Paradisus Palma Real Golf & Spa Resort, RD', 'hotel', 72, 1),
('Paradisus Punta Cana', 'paradisus-punta-cana', 'Paradisus Punta Cana Resort, RD', 'hotel', 73, 1),
('Paradisus Punta Cana The Reserve', 'paradisus-the-reserve', 'The Reserve at Paradisus Punta Cana, RD', 'hotel', 74, 1),
('Playa Palmera', 'playa-palmera', 'Playa Palmera Beach Resort, Bávaro, RD', 'hotel', 75, 1),
('Presidential Suites', 'presidential-suites', 'Presidential Suites, Punta Cana, RD', 'hotel', 76, 1),
('Punta Palmera', 'punta-palmera-resort', 'Punta Palmera Resort, Bávaro, RD', 'hotel', 77, 1),
('Radisson Blu Punta Cana', 'radisson-blu-punta-cana', 'Radisson Blu Resort & Residence, Punta Cana, RD', 'hotel', 78, 1),
('Riu Bambu', 'riu-bambu', 'Riu Bambu, Bávaro, Punta Cana, RD', 'hotel', 79, 1),
('Riu Bavaro', 'riu-bavaro', 'Riu Bávaro, Punta Cana, RD', 'hotel', 80, 1),
('Riu Naiboa', 'riu-naiboa', 'Riu Naiboa, Punta Cana, RD', 'hotel', 81, 1),
('Riu Palace Macao', 'riu-palace-macao', 'Riu Palace Macao, Punta Cana, RD', 'hotel', 82, 1),
('Riu Palace Punta Cana', 'riu-palace-punta-cana', 'Riu Palace Punta Cana, RD', 'hotel', 83, 1),
('Riu Republica', 'riu-republica', 'Riu Republica, Punta Cana, RD', 'hotel', 84, 1),
('Royalton Bavaro', 'royalton-bavaro', 'Royalton Bávaro Resort & Spa, RD', 'hotel', 85, 1),
('Royalton Chic (Adultos)', 'royalton-chic', 'Royalton CHIC Punta Cana (Adults Only), RD', 'hotel', 86, 1),
('Royalton Punta Cana', 'royalton-punta-cana', 'Royalton Punta Cana Resort & Casino, RD', 'hotel', 87, 1),
('Royalton Splash', 'royalton-splash', 'Royalton Splash Punta Cana, RD', 'hotel', 88, 1),
('Secrets Royal Beach', 'secrets-royal-beach', 'Secrets Royal Beach Punta Cana, RD', 'hotel', 89, 1),
('Serenade Punta Cana', 'serenade-punta-cana', 'Serenade Punta Cana Beach & Spa Resort, RD', 'hotel', 90, 1),
('Sirenis', 'sirenis', 'Grand Sirenis Punta Cana Resort, Uvero Alto, RD', 'hotel', 91, 1),
('Sivory', 'sivory', 'Sivory Punta Cana Boutique Hotel, Uvero Alto, RD', 'hotel', 92, 1),
('Sunscape Coco Punta Cana', 'sunscape-coco-punta-cana', 'Sunscape Coco Punta Cana, RD', 'hotel', 93, 1),
('Tortuga Bay', 'tortuga-bay', 'Tortuga Bay Hotel, Punta Cana, RD', 'hotel', 94, 1),
('Sensation / Nickelodeon', 'sensation-nickelodeon', 'Nickelodeon Hotels & Resorts Punta Cana, Uvero Alto, RD', 'hotel', 95, 1),
('Sensatori Cap Cana (2)', 'sensatori-cap-cana-2', 'TUI BLUE Sensatori Cap Cana, RD', 'hotel', 96, 1),
('Green Coast Hotel', 'green-coast-hotel', 'Green Coast Hotel, Juan Dolio, RD', 'hotel', 97, 1),
('Hotel MF', 'hotel-mf', 'Hotel MF, Punta Cana, RD', 'hotel', 98, 1),
('HM Alma Bavaro', 'hm-alma-bavaro', 'HM Alma Bávaro, Bávaro, Punta Cana, RD', 'hotel', 99, 1),
('Club Med Miches Playa Esmeralda', 'club-med-miches', 'Club Med Miches Playa Esmeralda, Miches, RD', 'hotel', 100, 1),
('VIK Arena Blanca', 'vik-arena-blanca', 'VIK Hotel Arena Blanca, Bávaro, RD', 'hotel', 101, 1),
('Vista Sol', 'vista-sol', 'Vista Sol Punta Cana Beach Resort, RD', 'hotel', 102, 1),
('Westin', 'westin-punta-cana', 'The Westin Puntacana Resort & Club, RD', 'hotel', 103, 1),
('Whala Bavaro', 'whala-bavaro', 'Whala Bávaro, Bávaro, Punta Cana, RD', 'hotel', 104, 1),

-- Hotéis/Resorts Bayahibe e La Romana
('Whala Bayahibe', 'whala-bayahibe', 'Whala Bayahibe, Bayahibe, La Romana, RD', 'hotel', 105, 1),
('HM Alma Bayahibe', 'hm-alma-bayahibe', 'HM Alma Bayahibe, Bayahibe, La Romana, RD', 'hotel', 106, 1),
('Viva Wyndham Dominicus Beach', 'viva-wyndham-dominicus-beach', 'Viva Wyndham Dominicus Beach, Bayahibe, RD', 'hotel', 107, 1),
('Viva Wyndham Dominicus Palace', 'viva-wyndham-dominicus-palace', 'Viva Wyndham Dominicus Palace, Bayahibe, RD', 'hotel', 108, 1),
('Hilton La Romana', 'hilton-la-romana', 'Hilton La Romana, La Romana, RD', 'hotel', 109, 1),
('Bahia Principe La Romana', 'bahia-principe-la-romana', 'Bahia Principe Grand La Romana, RD', 'hotel', 110, 1),
('Bahia Principe Bavaro', 'bahia-principe-bavaro', 'Bahia Principe Grand Bávaro, RD', 'hotel', 111, 1),
('Bahia Principe Fantasia', 'bahia-principe-fantasia', 'Bahia Principe Fantasia, Bávaro, RD', 'hotel', 112, 1),
('Bahia Principe Grand Aquamarine', 'bahia-principe-grand-aquamarine', 'Bahia Principe Grand Aquamarine, Bávaro, RD', 'hotel', 113, 1),
('Bahia Principe Punta Cana', 'bahia-principe-punta-cana', 'Bahia Principe Grand Punta Cana, RD', 'hotel', 114, 1),
('Casa de Campo', 'casa-de-campo', 'Casa de Campo Resort & Villas, La Romana, RD', 'resort', 115, 1),

-- Hotéis/Resorts em Boca Chica e Juan Dolio
('Whala Boca Chica', 'whala-boca-chica', 'Whala Boca Chica, Boca Chica, RD', 'hotel', 116, 1),
('Be Live Hamaca', 'be-live-hamaca', 'Be Live Experience Hamaca, Boca Chica, RD', 'hotel', 117, 1),
('BelleVue Dominican Bay', 'bellevue-dominican-bay', 'BelleVue Dominican Bay, Boca Chica, RD', 'hotel', 118, 1),
('Emotions by Hodelpa Juan Dolio', 'emotions-by-hodelpa-juan-dolio', 'Emotions by Hodelpa, Juan Dolio, RD', 'hotel', 119, 1),
('Emotions by Hodelpa', 'emotions-by-hodelpa', 'Emotions by Hodelpa Playa Dorada, Puerto Plata, RD', 'hotel', 120, 1),
('Coral Costa Caribe', 'coral-costa-caribe', 'Coral Costa Caribe Resort, Juan Dolio, RD', 'hotel', 121, 1),
('Whala Urban', 'whala-urban', 'Whala Urban, Punta Cana, RD', 'hotel', 122, 1),

-- Outros resorts e locais
('Hard Rock', 'hard-rock', 'Hard Rock Hotel & Casino Punta Cana, RD', 'hotel', 123, 1),
('Cadaques', 'cadaques', 'Cadaqués Caribe, Bayahibe, RD', 'hotel', 124, 1),
('Iberostar Dominicana', 'iberostar-dominicana', 'Iberostar Dominicana, Bávaro, Punta Cana, RD', 'hotel', 125, 1),
('Iberostar Selection Bavaro', 'iberostar-selection-bavaro', 'Iberostar Selection Bávaro, Punta Cana, RD', 'hotel', 126, 1),
('Breathless', 'breathless', 'Breathless Punta Cana Resort & Spa, Uvero Alto, RD', 'hotel', 127, 1),

-- Cidades extras
('Boca Chica', 'boca-chica', 'Boca Chica, Santo Domingo Este, RD', 'city', 128, 1),
('Miches', 'miches', 'Miches, El Seibo, RD', 'city', 129, 1);


-- ============================================================
-- PARTE 2: CRIAR ROTAS PARA TODOS OS VEÍCULOS
-- Para cada local, criar rota IDA (Aeroporto → Local) e VOLTA (Local → Aeroporto)
-- para cada um dos 3 veículos já cadastrados.
-- Preço base será 0.00 (admin configura depois via painel, ou usa tarifas)
-- ============================================================

-- Criar rotas para TODOS os locais (exceto o próprio aeroporto)
-- Precisamos do ID do aeroporto e dos 3 veículos. Vamos usar variáveis.

SET @airport_id = (SELECT id FROM transfer_locations WHERE slug = 'aeroporto-punta-cana' LIMIT 1);

-- Criar rotas ida e volta para cada veículo e cada destino
-- Usamos INSERT IGNORE para não duplicar rotas existentes

INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, @airport_id, l.id, 0.00, 30, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.id != @airport_id
  AND v.status = 'active';

-- Rota inversa (volta: destino → aeroporto)
INSERT IGNORE INTO transfer_routes (vehicle_id, origin_id, destination_id, base_price, duration, status)
SELECT v.id, l.id, @airport_id, 0.00, 30, 1
FROM transfer_vehicles v
CROSS JOIN transfer_locations l
WHERE l.id != @airport_id
  AND v.status = 'active';
