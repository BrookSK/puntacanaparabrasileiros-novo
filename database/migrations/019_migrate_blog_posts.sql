-- Migration: Migrar posts do blog do site antigo
-- Data: 2026-08-09

START TRANSACTION;

-- Garantir que autor existe (usar ID 1 como admin)
-- Se não existir o autor, os posts ficam sem autor

-- Post 1: O que fazer em Punta Cana em 2026
INSERT IGNORE INTO `blog_posts` (`title`, `slug`, `excerpt`, `content`, `featured_image`, `category_id`, `author_id`, `status`, `published_at`, `created_at`) VALUES
(
    'O que fazer em Punta Cana em 2026 – Guia para Brasileiros',
    'o-que-fazer-em-punta-cana-em-2026-guia-para-brasileiros',
    'Se você está planejando sua viagem e quer saber o que fazer em Punta Cana, este guia para brasileiros vai te ajudar a escolher os melhores passeios na República Dominicana.',
    '<p>Se você está planejando sua viagem e quer saber o que fazer em Punta Cana, este guia para brasileiros vai te ajudar a escolher os melhores passeios na República Dominicana.</p>\n<p>Os passeios mais procurados em Punta Cana são Ilha Saona, Coco Bongo, buggy, Golfinho e Santo Domingo.</p>\n<p>A Ilha Saona é o passeio mais famoso, com piscinas naturais e praias paradisíacas.</p>\n<p>O Coco Bongo é o show noturno mais popular do Caribe, muito procurado por brasileiros.</p>\n<p>O passeio de buggy em Punta Cana é ideal para quem gosta de aventura.</p>\n<p>O passeio de nadar com golfinhos em Punta Cana é ideal para famílias, casais e pessoas que querem viver uma experiência diferente durante a viagem.</p>\n<p>É uma atividade recomendada para quem gosta de animais, para quem viaja com crianças e também para quem quer fazer um passeio tranquilo e seguro.</p>',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/O-que-fazer-em-Punta-Cana-Guia-para-Brasileiros.png',
    (SELECT id FROM blog_categories WHERE slug = 'sem-categoria' LIMIT 1),
    1,
    'published',
    '2026-03-15 10:00:00',
    '2026-03-15 10:00:00'
),
-- Post 2: Buggies em Macao
(
    'Buggies em Macao: Aventura, lama e cenote em Punta Cana',
    'buggies-em-macao-aventura-lama-e-cenote-em-punta-cana',
    'Descubra a emoção de dirigir buggies pelas trilhas de Macao em Punta Cana. Uma aventura cheia de lama, natureza e um cenote de águas cristalinas.',
    '<p>Descubra a emoção de dirigir buggies pelas trilhas de Macao em Punta Cana. Uma aventura cheia de lama, natureza e um cenote de águas cristalinas.</p>\n<p>O passeio de buggy em Punta Cana é uma das experiências mais procuradas por turistas brasileiros. Você vai dirigir por estradas de terra, atravessar poças de lama e explorar a natureza dominicana de uma forma única.</p>\n<p>Um dos pontos altos do passeio é a visita ao cenote — uma piscina natural escondida entre as rochas, com águas azuis incrivelmente cristalinas. É o lugar perfeito para se refrescar depois da aventura.</p>\n<p>O passeio também inclui uma parada em uma casa típica dominicana, onde você pode experimentar café local, chocolate e mamajuana, a bebida típica da República Dominicana.</p>\n<p>A duração do passeio é de aproximadamente 4 horas, com transporte incluído desde o hotel.</p>',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0101.jpg',
    (SELECT id FROM blog_categories WHERE slug = 'geral' LIMIT 1),
    1,
    'published',
    '2025-06-02 10:00:00',
    '2025-06-02 10:00:00'
),
-- Post 3: Coco Bongo
(
    'Coco Bongo em Punta Cana: Tudo o que você precisa saber',
    'coco-bongo-em-punta-cana-tudo-o-que-voce-precisa-saber',
    'A Coco Bongo é a casa noturna mais famosa do Caribe. Saiba tudo sobre ingressos, shows e como aproveitar essa experiência incrível em Punta Cana.',
    '<p>A Coco Bongo é a casa noturna mais famosa de Punta Cana e de todo o Caribe. É referência em diversão, espetáculos, shows, performances circenses, balada, boa música e gente bonita.</p>\n<p>Os shows são inspirados em grandes produções da Broadway e Las Vegas, além de homenagens a estrelas da música e do cinema. Personagens como O Máscara, Moulin Rouge, Batman, Madonna, Michael Jackson e The Beatles são recriados de forma impressionante.</p>\n<h3>Tipos de Ingresso</h3>\n<p><strong>Open Bar:</strong> Inclui entrada e bebidas ilimitadas durante toda a noite. É a opção mais popular entre brasileiros.</p>\n<p><strong>Gold Member:</strong> Área VIP com serviço premium, mesa reservada e atendimento diferenciado.</p>\n<p><strong>Front Row:</strong> Lugar privilegiado na primeira fila para assistir aos espetáculos de perto.</p>\n<h3>Informações Importantes</h3>\n<p>Entrada proibida para menores de 18 anos. Preço para sexta-feira e sábado pode ser diferente dos demais dias.</p>',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-scaled.jpeg',
    (SELECT id FROM blog_categories WHERE slug = 'passeio' LIMIT 1),
    1,
    'published',
    '2025-06-02 11:00:00',
    '2025-06-02 11:00:00'
),
-- Post 4: Ilha Saona
(
    'Ilha Saona: O passeio mais famoso de Punta Cana',
    'ilha-saona-o-passeio-mais-famoso-de-punta-cana',
    'Conheça a Ilha Saona, o passeio mais procurado por brasileiros em Punta Cana. Praias paradisíacas, piscinas naturais e muito mais.',
    '<p>A Ilha Saona é o destino mais procurado por quem visita Punta Cana. Com praias de areia branca, águas cristalinas e piscinas naturais, é o cenário perfeito para um dia inesquecível no Caribe.</p>\n<p>O passeio até a Ilha Saona geralmente começa pela manhã, com transporte do hotel até Bayahibe, de onde partem as lanchas e catamarãs.</p>\n<h3>Opções de Passeio</h3>\n<p><strong>Saona Clássica (Catamarã):</strong> Ideal para quem quer um passeio mais tranquilo. O trajeto combina lancha e catamarã, com parada na piscina natural e tempo livre na ilha.</p>\n<p><strong>Saona VIP Mano Juan:</strong> Uma experiência mais exclusiva, com lancha ida e volta e visita ao vilarejo de pescadores de Mano Juan.</p>\n<p><strong>Saona Premium Brasil:</strong> Exclusividade para brasileiros, com lancha ida e volta e serviço diferenciado.</p>\n<p>Todos os passeios incluem almoço na ilha, open bar e muita diversão. Gestantes não são permitidas.</p>',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_0948.jpeg',
    (SELECT id FROM blog_categories WHERE slug = 'geral' LIMIT 1),
    1,
    'published',
    '2025-06-02 12:00:00',
    '2025-06-02 12:00:00'
),
-- Post 5: Golfinhos
(
    'Nadar com Golfinhos em Punta Cana: Guia Completo',
    'nadar-com-golfinhos-em-punta-cana-guia-completo',
    'Tudo sobre o passeio de nadar com golfinhos em Punta Cana. Tipos de experiência, preços e dicas para aproveitar ao máximo.',
    '<p>Nadar com golfinhos em Punta Cana é uma das experiências mais emocionantes que você pode viver durante sua viagem ao Caribe. Existem diferentes níveis de interação para todos os perfis de viajante.</p>\n<h3>Tipos de Experiência</h3>\n<p><strong>Interação com Golfinho:</strong> Você interage com o golfinho por 40 minutos tocando, abraçando e beijando. Não inclui nado. Ideal para quem quer uma experiência mais tranquila.</p>\n<p><strong>Nado com 1 Golfinho:</strong> Além da interação, você nada com o golfinho por 50 minutos. Inclui reboque dorsal e empurrão.</p>\n<p><strong>Nado com 2 Golfinhos:</strong> A experiência mais completa, com 60 minutos de interação e nado com dois golfinhos simultaneamente.</p>\n<h3>Informações Importantes</h3>\n<p>Gestantes não podem participar do nado com golfinhos. É permitida apenas a interação, e somente até no máximo 5 meses de gestação.</p>\n<p>Crianças a partir de 1 ano podem participar da interação acompanhadas dos pais.</p>',
    'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0135.jpg',
    (SELECT id FROM blog_categories WHERE slug = 'geral' LIMIT 1),
    1,
    'published',
    '2025-06-02 13:00:00',
    '2025-06-02 13:00:00'
);

COMMIT;
