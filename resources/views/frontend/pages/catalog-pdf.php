<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catálogo de Experiências - Punta Cana para Brasileiros</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ===== RESET & BASE ===== */
@page {
    size: A4;
    margin: 14mm 12mm; /* margens reais — dá respiro no topo/rodapé de cada página */
}
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    --text-green: #1B6F00;
    --secondary: #E4B505;
    --accent: #3772C0;
    --dark: #1C2011;
    --gray: #636e72;
    --light-bg: #f7f8fa;
    --white: #ffffff;
    --radius: 12px;
}

body {
    font-family: 'Poppins', sans-serif;
    color: var(--dark);
    background: var(--white);
    line-height: 1.6;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

/* ===== PRINT BAR (apenas tela) ===== */
.print-bar {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: #1C2011;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
.print-bar button,
.print-bar a {
    padding: 10px 24px;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-print-pdf { background: #E4B505; color: #1C2011; }
.btn-back-catalog {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.2) !important;
}

/* ===== HEADER — não entra na @page margin, sai do fluxo de impressão ===== */
.site-header {
    background: var(--white);
    border-bottom: 1px solid #e8e8e8;
    /* Na impressão o header aparece uma só vez na primeira página */
}
.header-inner {
    max-width: 860px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 70px;
}
.header-logo img { height: 48px; width: auto; }
.header-title {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-green);
}

/* ===== HERO ===== */
.hero {
    position: relative;
    background: linear-gradient(135deg, #1C2011 0%, #2d4a1e 50%, #1B6F00 100%);
    padding: 52px 24px 72px;
    text-align: center;
    overflow: hidden;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.hero-content { position: relative; z-index: 2; max-width: 640px; margin: 0 auto; }
.hero h1 {
    font-size: 32px;
    font-weight: 700;
    color: var(--white);
    margin-bottom: 10px;
    letter-spacing: -0.5px;
}
.hero p { font-size: 15px; color: rgba(255,255,255,0.8); font-weight: 300; }
.hero-stats {
    display: flex;
    justify-content: center;
    gap: 48px;
    margin-top: 28px;
    position: relative;
    z-index: 2;
}
.hero-stat-number {
    font-size: 26px;
    font-weight: 700;
    color: var(--secondary);
    display: block;
}
.hero-stat-label {
    font-size: 10px;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.hero-wave {
    position: absolute;
    bottom: -2px; left: 0;
    width: 100%; height: 40px;
}
.hero-wave svg { width: 100%; height: 100%; display: block; }

/* ===== SECTION INTRO ===== */
.section-intro {
    text-align: center;
    padding: 36px 24px 20px;
    max-width: 600px;
    margin: 0 auto;
}
.caveat-label {
    font-family: 'Caveat', cursive;
    font-size: 24px;
    color: var(--secondary);
    margin-bottom: 2px;
    display: block;
}
.section-intro h2 {
    font-size: 26px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 14px;
}
.wave-divider { display: flex; justify-content: center; margin-bottom: 6px; }
.wave-divider svg { width: 110px; height: 18px; }
.wave-divider path { stroke: var(--accent); stroke-width: 2.5; fill: none; }

/* ===== GRID DE CARDS
   2 colunas: cada linha é menor, menor risco de corte.
   Cada card tem break-inside:avoid — o browser pula a linha inteira
   para a próxima página em vez de cortar a imagem.
===== */
.catalog-container {
    max-width: 860px;
    margin: 0 auto;
    padding: 0 24px 48px;
}

.trips-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
}

/* ===== TRIP CARD ===== */
.trip-card {
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid #e8e8e8;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    /* A chave: nunca cortar este elemento */
    page-break-inside: avoid;
    break-inside: avoid;
    -webkit-column-break-inside: avoid;
}
.card-img-wrap {
    position: relative;
    width: 100%;
    /* Altura fixa em px é mais previsível que aspect-ratio para impressão */
    height: 170px;
    overflow: hidden;
    background: #e9ecef;
    flex-shrink: 0;
}
.card-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.card-badge {
    position: absolute;
    top: 10px; left: 10px;
    background: var(--text-green);
    color: var(--white);
    font-size: 10px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
}
.card-body {
    padding: 14px 16px 10px;
    flex: 1;
}
.card-body h3 {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 5px;
    line-height: 1.3;
}
.card-body .description {
    font-size: 11px;
    color: var(--gray);
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 10px;
}
.card-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    font-size: 11px;
    color: var(--gray);
    margin-bottom: 8px;
}
.card-meta span { display: flex; align-items: center; gap: 3px; }
.card-meta svg {
    width: 12px; height: 12px;
    stroke: var(--gray); fill: none; stroke-width: 1.8;
}
.card-meta .stars svg { fill: var(--secondary); stroke: var(--secondary); }
.card-prices {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}
.price-chip {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 10px;
    display: inline-block;
}
.price-chip.adult { background: #f0fdf4; color: #166534; }
.price-chip.child { background: #eff6ff; color: #1e40af; }
.price-chip.free  { background: #fffbeb; color: #92400e; }
.card-footer {
    padding: 10px 16px;
    border-top: 1px solid #f0f0f0;
}
.card-price .label {
    font-size: 10px;
    color: var(--gray);
    display: block;
    line-height: 1;
}
.card-price .amount {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-green);
}

/* ===== FOOTER ===== */
.catalog-footer {
    background: var(--light-bg);
    padding: 40px 24px;
    text-align: center;
    border-top: 3px solid var(--secondary);
    page-break-inside: avoid;
    break-inside: avoid;
}
.catalog-footer h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-green);
    margin-bottom: 6px;
}
.catalog-footer > p {
    font-size: 13px;
    color: var(--gray);
    margin-bottom: 14px;
}
.btn-whatsapp {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #25D366;
    color: var(--white);
    padding: 11px 24px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
}
.btn-whatsapp svg { width: 18px; height: 18px; fill: var(--white); }
.catalog-footer .legal {
    margin-top: 20px;
    font-size: 10px;
    color: #adb5bd;
    line-height: 1.8;
}

/* ===== PRINT ===== */
@media print {
    .print-bar { display: none !important; }
    .site-header {
        /* Repete o header no topo de cada página impressa */
        position: running(header);
    }
    body { background: #fff; }
    .hero,
    .card-img-wrap,
    .catalog-footer {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    /* Hero e intro ficam na primeira página — não quebrar */
    .hero { page-break-after: avoid; break-after: avoid; }
    .section-intro { page-break-after: avoid; break-after: avoid; }
    /* Cada card nunca é cortado */
    .trip-card {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    /* Footer nunca orphão no topo de página */
    .catalog-footer { page-break-before: avoid; break-before: avoid; }
}
</style>
</head>
<body>

<!-- PRINT BAR -->
<div class="print-bar">
    <button class="btn-print-pdf" onclick="window.print()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Imprimir / Salvar como PDF
    </button>
    <a href="/catalogo" class="btn-back-catalog">&#8592; Voltar ao Catálogo</a>
</div>

<!-- HEADER -->
<header class="site-header">
    <div class="header-inner">
        <a href="/" class="header-logo">
            <img src="<?= asset('images/layout/PUNTA-CANA-1.png') ?>" alt="Punta Cana Para Brasileiros">
        </a>
        <span class="header-title">Catálogo de Passeios <?= date('Y') ?></span>
    </div>
</header>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <h1>Nosso Catálogo de Experiências</h1>
        <p>Passeios exclusivos e transfers privativos no Caribe dominicano</p>
    </div>
    <div class="hero-stats">
        <div>
            <span class="hero-stat-number"><?= count($trips) ?></span>
            <span class="hero-stat-label">Passeios</span>
        </div>
        <div>
            <span class="hero-stat-number">4.8</span>
            <span class="hero-stat-label">Avaliação</span>
        </div>
        <div>
            <span class="hero-stat-number">1000+</span>
            <span class="hero-stat-label">Clientes</span>
        </div>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1440 40" preserveAspectRatio="none">
            <path d="M0,20 C360,40 720,0 1080,20 C1260,30 1380,35 1440,30 L1440,40 L0,40 Z" fill="#ffffff"/>
            <path d="M0,25 C300,10 600,35 900,20 C1100,10 1300,30 1440,22 L1440,40 L0,40 Z" fill="#ffffff" opacity="0.5"/>
        </svg>
    </div>
</section>

<!-- SECTION INTRO -->
<div class="section-intro">
    <span class="caveat-label">Experiências Exclusivas</span>
    <h2>Passeios em Punta Cana</h2>
    <div class="wave-divider">
        <svg viewBox="0 0 120 20">
            <path d="M0,10 C20,0 40,20 60,10 C80,0 100,20 120,10" stroke-linecap="round"/>
        </svg>
    </div>
</div>

<!-- TRIPS GRID -->
<div class="catalog-container">
    <div class="trips-grid">
        <?php foreach ($trips as $trip): ?>
        <?php
            $img       = $trip['featured_image'] ?? '/assets/images/placeholder.jpg';
            $duration  = $trip['duration'] . ($trip['duration_unit'] === 'hours' ? 'h' : ' dias');
            $price     = $trip['min_price'] > 0 ? 'US$' . number_format($trip['min_price'], 0) : 'Consultar';
            $rating    = isset($trip['rating']) && $trip['rating'] > 0 ? $trip['rating'] : 4.5;
            $badge     = !empty($trip['featured']) ? 'Premium' : '';
            $categories = $trip['price_categories'] ?? [];
        ?>
        <div class="trip-card">
            <div class="card-img-wrap">
                <img src="<?= e($img) ?>" alt="<?= e($trip['title']) ?>">
                <?php if ($badge): ?>
                <span class="card-badge"><?= e($badge) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <h3><?= e($trip['title']) ?></h3>
                <p class="description"><?= e($trip['short_description'] ?? '') ?></p>
                <div class="card-meta">
                    <span>
                        <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Punta Cana
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?= e($duration) ?>
                    </span>
                    <span class="stars">
                        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <?= $rating ?>
                    </span>
                </div>
                <?php if (!empty($categories)): ?>
                <div class="card-prices">
                    <?php foreach ($categories as $cat): ?>
                    <?php $catPrice = (float)($cat['sale_price'] ?: $cat['price']); ?>
                    <span class="price-chip <?= $cat['category_slug'] === 'crianca' ? 'child' : ($catPrice == 0 ? 'free' : 'adult') ?>">
                        <?= e($cat['category_name']) ?>: <?= $catPrice > 0 ? 'US$' . number_format($catPrice, 0) : 'Grátis' ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <div class="card-price">
                    <span class="label">Desde</span>
                    <span class="amount"><?= $price ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- FOOTER -->
<footer class="catalog-footer">
    <h3>Reserve sua Experiência</h3>
    <p>Entre em contato para reservar ou tirar dúvidas</p>
    <a href="https://api.whatsapp.com/send?phone=18294582170&text=Ol%C3%A1%2C%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es!" class="btn-whatsapp" target="_blank">
        <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        WhatsApp: +1 (829) 458-2170
    </a>
    <p class="legal">
        Punta Cana para Brasileiros | Oliveira &amp; Ramos SRL &mdash; RNC: 1-33-28776-5<br>
        Av. Barceló, nº 01, Local 7 - Plaza Arrecife, Verón, Punta Cana, República Dominicana<br>
        &copy; <?= date('Y') ?> Todos os direitos reservados.
    </p>
</footer>

</body>
</html>
