<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cat&aacute;logo de Passeios - Punta Cana para Brasileiros</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
@page { size: A4; margin: 0; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    color: #1a1a1a;
    background: #fff;
    line-height: 1.5;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

/* ===== COVER ===== */
.cover {
    width: 100%;
    min-height: 100vh;
    background: linear-gradient(160deg, #0a2e0a 0%, #1B6F00 40%, #2d8a1e 70%, #1B6F00 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 60px 40px;
    position: relative;
    page-break-after: always;
}
.cover::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, #E4B505, #f0d050, #E4B505);
}
.cover-logo {
    width: 160px;
    height: auto;
    margin-bottom: 40px;
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
}
.cover-badge {
    display: inline-block;
    background: rgba(228,181,5,0.15);
    border: 1px solid rgba(228,181,5,0.4);
    color: #E4B505;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 3px;
    text-transform: uppercase;
    padding: 8px 24px;
    border-radius: 30px;
    margin-bottom: 24px;
}
.cover h1 {
    font-size: 44px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -1px;
    margin-bottom: 16px;
    line-height: 1.1;
}
.cover h1 span {
    color: #E4B505;
}
.cover-sub {
    font-size: 16px;
    color: rgba(255,255,255,0.75);
    font-weight: 300;
    max-width: 480px;
    margin-bottom: 50px;
}
.cover-stats {
    display: flex;
    gap: 40px;
    margin-bottom: 50px;
}
.cover-stat {
    text-align: center;
}
.cover-stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #E4B505;
    display: block;
}
.cover-stat-label {
    font-size: 11px;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.cover-footer {
    position: absolute;
    bottom: 30px;
    font-size: 12px;
    color: rgba(255,255,255,0.5);
}

/* ===== CONTENT ===== */
.content-page {
    padding: 30px 35px;
}
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    border-bottom: 2px solid #1B6F00;
    margin-bottom: 24px;
}
.page-header-logo {
    height: 36px;
}
.page-header-title {
    font-size: 13px;
    color: #1B6F00;
    font-weight: 600;
}

/* Trip Card */
.trip-card-pdf {
    display: flex;
    gap: 18px;
    padding: 18px 0;
    border-bottom: 1px solid #eee;
    page-break-inside: avoid;
}
.trip-card-pdf:last-child {
    border-bottom: none;
}
.trip-card-img {
    width: 160px;
    height: 110px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.trip-card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.trip-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
}
.trip-card-desc {
    font-size: 11px;
    color: #666;
    margin-bottom: 8px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.trip-card-meta {
    display: flex;
    gap: 14px;
    font-size: 10px;
    color: #888;
    margin-bottom: 8px;
}
.trip-card-meta span {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.trip-card-prices {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.price-chip {
    font-size: 10px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    display: inline-block;
}
.price-chip.adult {
    background: #f0fdf4;
    color: #166534;
}
.price-chip.child {
    background: #eff6ff;
    color: #1e40af;
}
.price-chip.free {
    background: #fffbeb;
    color: #92400e;
}
.trip-card-price-main {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    min-width: 80px;
}
.price-main-value {
    font-size: 22px;
    font-weight: 700;
    color: #1B6F00;
}

/* Page Footer */
.page-footer {
    text-align: center;
    padding-top: 20px;
    margin-top: 20px;
    border-top: 1px solid #eee;
    font-size: 9px;
    color: #aaa;
}

/* PDF Footer Final */
.final-footer {
    page-break-before: auto;
    text-align: center;
    padding: 40px;
    background: #fafafa;
    border-top: 3px solid #E4B505;
}
.final-footer h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1B6F00;
    margin-bottom: 8px;
}
.final-footer p {
    font-size: 13px;
    color: #666;
    margin-bottom: 4px;
}
.final-footer .whatsapp-link {
    display: inline-block;
    margin-top: 16px;
    background: #25D366;
    color: #fff;
    padding: 10px 24px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
}
.final-footer .legal {
    margin-top: 20px;
    font-size: 10px;
    color: #999;
}

/* Print Actions */
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
.print-bar button, .print-bar a {
    padding: 10px 24px;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: none;
}
.btn-print-pdf {
    background: #E4B505;
    color: #1C2011;
}
.btn-print-pdf:hover { background: #d4a505; }
.btn-back-catalog {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.2) !important;
}
@media print {
    .print-bar { display: none !important; }
    body { background: #fff; }
}
@media screen {
    body { background: #2a2a2a; }
    .cover { margin: 0 auto; max-width: 900px; }
    .content-page { max-width: 900px; margin: 0 auto; background: #fff; }
    .final-footer { max-width: 900px; margin: 0 auto; }
}
</style>
</head>
<body>

<div class="print-bar">
    <button class="btn-print-pdf" onclick="window.print()">&#128196; Imprimir / Salvar como PDF</button>
    <a href="/catalogo" class="btn-back-catalog">&#8592; Voltar ao Cat&aacute;logo</a>
</div>

<!-- CAPA -->
<div class="cover">
    <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" class="cover-logo">
    <span class="cover-badge">Cat&aacute;logo Oficial <?= date('Y') ?></span>
    <h1>Experi&ecirc;ncias em<br><span>Punta Cana</span></h1>
    <p class="cover-sub">Passeios exclusivos, transfers privativos e aventuras inesquec&iacute;veis no Caribe dominicano.</p>
    <div class="cover-stats">
        <div class="cover-stat">
            <span class="cover-stat-number"><?= count($trips) ?></span>
            <span class="cover-stat-label">Passeios</span>
        </div>
        <div class="cover-stat">
            <span class="cover-stat-number">4.8</span>
            <span class="cover-stat-label">Avalia&ccedil;&atilde;o</span>
        </div>
        <div class="cover-stat">
            <span class="cover-stat-number">1000+</span>
            <span class="cover-stat-label">Clientes</span>
        </div>
    </div>
    <p class="cover-footer">+1 (829) 458-2170 | puntacanaparabrasileiros.com</p>
</div>

<!-- CONTEÚDO -->
<div class="content-page">
    <div class="page-header">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="" class="page-header-logo">
        <span class="page-header-title">Cat&aacute;logo de Passeios <?= date('Y') ?></span>
    </div>

    <?php foreach ($trips as $trip): ?>
    <?php
        $duration = $trip['duration'] . ($trip['duration_unit'] === 'hours' ? ' horas' : ' dias');
        $price = $trip['min_price'] > 0 ? 'US$' . number_format($trip['min_price'], 0) : 'Consultar';
        $categories = $trip['price_categories'] ?? [];
    ?>
    <div class="trip-card-pdf">
        <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" class="trip-card-img">
        <div class="trip-card-body">
            <div class="trip-card-title"><?= e($trip['title']) ?></div>
            <p class="trip-card-desc"><?= e($trip['short_description'] ?? '') ?></p>
            <div class="trip-card-meta">
                <span>&#128205; Punta Cana</span>
                <span>&#9201; <?= e($duration) ?></span>
            </div>
            <?php if (!empty($categories)): ?>
            <div class="trip-card-prices">
                <?php foreach ($categories as $cat): ?>
                <?php $catPrice = (float)($cat['sale_price'] ?: $cat['price']); ?>
                <span class="price-chip <?= $cat['category_slug'] === 'crianca' ? 'child' : ($catPrice == 0 ? 'free' : 'adult') ?>">
                    <?= e($cat['category_name']) ?>: <?= $catPrice > 0 ? 'US$' . number_format($catPrice, 0) : 'Gr&aacute;tis' ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="trip-card-price-main">
            <span class="price-main-value"><?= $price ?></span>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="page-footer">
        Valores sujeitos a altera&ccedil;&atilde;o sem aviso pr&eacute;vio. Consulte disponibilidade.
    </div>
</div>

<!-- FOOTER FINAL -->
<div class="final-footer">
    <h3>Reserve sua Experi&ecirc;ncia</h3>
    <p>Entre em contato conosco para reservar ou tirar d&uacute;vidas</p>
    <a href="https://wa.me/18294582170" class="whatsapp-link">&#128172; WhatsApp: +1 (829) 458-2170</a>
    <p class="legal">Punta Cana para Brasileiros | Oliveira &amp; Ramos SRL &mdash; RNC: 1-33-28776-5<br>Av. Barcel&oacute;, n&ordm; 01, Local 7 - Plaza Arrecife, Ver&oacute;n, Punta Cana, Rep&uacute;blica Dominicana</p>
</div>

</body>
</html>
