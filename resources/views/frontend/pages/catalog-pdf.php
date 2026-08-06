<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cat&aacute;logo de Experi&ecirc;ncias - Punta Cana para Brasileiros</title>
<style>
@page {
    size: A4;
    margin: 15mm;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Segoe UI', Roboto, Arial, sans-serif;
    color: #1a1a1a;
    background: #fff;
    line-height: 1.5;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

/* Cover Page */
.cover {
    page-break-after: always;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: linear-gradient(135deg, #1C2011 0%, #2d4a1e 50%, #1B6F00 100%);
    color: #fff;
    padding: 40px;
    position: relative;
    overflow: hidden;
}
.cover::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.cover-logo {
    width: 120px;
    height: auto;
    margin-bottom: 30px;
    position: relative;
    z-index: 1;
}
.cover h1 {
    font-size: 36px;
    font-weight: 700;
    letter-spacing: -0.5px;
    margin-bottom: 12px;
    position: relative;
    z-index: 1;
}
.cover p {
    font-size: 16px;
    opacity: 0.85;
    max-width: 500px;
    position: relative;
    z-index: 1;
}
.cover-year {
    margin-top: 40px;
    font-size: 14px;
    opacity: 0.6;
    position: relative;
    z-index: 1;
}
.cover-divider {
    width: 60px;
    height: 3px;
    background: #E4B505;
    margin: 20px auto;
    border-radius: 2px;
    position: relative;
    z-index: 1;
}
.cover-contact {
    margin-top: 30px;
    font-size: 13px;
    opacity: 0.7;
    position: relative;
    z-index: 1;
}

/* Trips Grid */
.catalog-content {
    padding: 10px 0;
}
.catalog-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #E4B505;
}
.catalog-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #1B6F00;
    margin-bottom: 6px;
}
.catalog-header p {
    font-size: 12px;
    color: #666;
}

/* Trip Item */
.trip-item {
    display: flex;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #f0f0f0;
    page-break-inside: avoid;
}
.trip-item:last-child {
    border-bottom: none;
}
.trip-img {
    width: 140px;
    height: 95px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}
.trip-info {
    flex: 1;
}
.trip-info h3 {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
}
.trip-info .trip-desc {
    font-size: 11px;
    color: #666;
    margin-bottom: 8px;
    line-height: 1.4;
}
.trip-meta-row {
    display: flex;
    gap: 16px;
    font-size: 10px;
    color: #888;
    margin-bottom: 6px;
}
.trip-meta-row span {
    display: flex;
    align-items: center;
    gap: 3px;
}
.trip-prices-row {
    display: flex;
    gap: 12px;
    font-size: 11px;
}
.trip-price-tag {
    background: #f0fdf4;
    color: #1B6F00;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 600;
}
.trip-price-tag.child {
    background: #eff6ff;
    color: #2563eb;
}
.trip-price-tag.free {
    background: #fffbeb;
    color: #d97706;
}
.trip-main-price {
    font-size: 18px;
    font-weight: 700;
    color: #1B6F00;
    text-align: right;
    white-space: nowrap;
    align-self: center;
}

/* Footer */
.pdf-footer {
    text-align: center;
    padding-top: 20px;
    margin-top: 30px;
    border-top: 2px solid #E4B505;
    font-size: 11px;
    color: #666;
}
.pdf-footer strong {
    color: #1B6F00;
}

/* Print buttons */
.print-actions {
    text-align: center;
    padding: 20px;
    background: #f5f5f5;
    position: sticky;
    top: 0;
    z-index: 100;
}
.print-actions button, .print-actions a {
    display: inline-block;
    padding: 12px 28px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    margin: 0 8px;
    border: none;
}
.btn-pdf-print {
    background: #1B6F00;
    color: #fff;
}
.btn-pdf-back {
    background: #e5e7eb;
    color: #374151;
}
@media print {
    .print-actions { display: none; }
    .cover { height: auto; min-height: 100vh; }
}
@media screen {
    body { background: #e5e7eb; }
    .cover { min-height: 60vh; height: auto; border-radius: 0; }
    .catalog-content { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
    .pdf-footer { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px 30px 30px; }
}
</style>
</head>
<body>

<!-- Print Actions (visible only on screen) -->
<div class="print-actions">
    <button class="btn-pdf-print" onclick="window.print()">Imprimir / Salvar como PDF</button>
    <a href="/catalogo" class="btn-pdf-back">Voltar ao Cat&aacute;logo</a>
</div>

<!-- Cover Page -->
<div class="cover">
    <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Logo" class="cover-logo">
    <div class="cover-divider"></div>
    <h1>Cat&aacute;logo de Experi&ecirc;ncias</h1>
    <p>Descubra passeios exclusivos e transfers privativos em Punta Cana. Experi&ecirc;ncias inesquec&iacute;veis para brasileiros.</p>
    <div class="cover-divider"></div>
    <p class="cover-year"><?= date('Y') ?></p>
    <p class="cover-contact">+1 (829) 458-2170 | contato@puntacanaparabrasileiros.com</p>
</div>

<!-- Catalog Content -->
<div class="catalog-content">
    <div class="catalog-header">
        <h2>Passeios em Punta Cana</h2>
        <p><?= count($trips) ?> experi&ecirc;ncias dispon&iacute;veis</p>
    </div>

    <?php foreach ($trips as $trip): ?>
    <?php
        $duration = $trip['duration'] . ($trip['duration_unit'] === 'hours' ? 'h' : ' dias');
        $price = $trip['min_price'] > 0 ? 'US$' . number_format($trip['min_price'], 0) : 'Consultar';
        $categories = $trip['price_categories'] ?? [];
    ?>
    <div class="trip-item">
        <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" class="trip-img">
        <div class="trip-info">
            <h3><?= e($trip['title']) ?></h3>
            <p class="trip-desc"><?= e(mb_substr($trip['short_description'] ?? '', 0, 150)) ?></p>
            <div class="trip-meta-row">
                <span>&#128205; Punta Cana</span>
                <span>&#9201; <?= e($duration) ?></span>
            </div>
            <?php if (!empty($categories)): ?>
            <div class="trip-prices-row">
                <?php foreach ($categories as $cat): ?>
                <?php $catPrice = (float)($cat['sale_price'] ?: $cat['price']); ?>
                <span class="trip-price-tag <?= $cat['category_slug'] === 'crianca' ? 'child' : ($catPrice == 0 ? 'free' : '') ?>">
                    <?= e($cat['category_name']) ?>: <?= $catPrice > 0 ? 'US$' . number_format($catPrice, 0) : 'Gratis' ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="trip-main-price"><?= $price ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Footer -->
<div class="pdf-footer">
    <p><strong>Punta Cana para Brasileiros</strong> | Oliveira & Ramos SRL &mdash; RNC: 1-33-28776-5</p>
    <p>Av. Barcel&oacute;, n&ordm; 01, Local 7 - Plaza Arrecife, Ver&oacute;n, Punta Cana | +1 (829) 458-2170</p>
    <p style="margin-top:8px;font-size:10px;color:#999;">Valores sujeitos a altera&ccedil;&atilde;o. Consulte disponibilidade antes de reservar.</p>
</div>

</body>
</html>
