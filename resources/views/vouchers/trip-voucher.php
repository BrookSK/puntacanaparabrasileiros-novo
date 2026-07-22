<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Voucher - <?= e($item['trip_title'] ?? '') ?></title>
<style>
body{font-family:Arial,sans-serif;margin:0;padding:20px;color:#333}
.voucher{max-width:700px;margin:0 auto;border:2px solid #0077b6;border-radius:10px;overflow:hidden}
.voucher-header{background:#0077b6;color:#fff;padding:20px;text-align:center}
.voucher-header img{max-height:50px;margin-bottom:10px}
.voucher-body{padding:25px}
.voucher-title{font-size:22px;font-weight:bold;margin-bottom:5px}
.voucher-ref{font-size:14px;color:#666;margin-bottom:20px}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px}
.info-item{padding:10px;background:#f8f9fa;border-radius:5px}
.info-label{font-size:12px;color:#666;text-transform:uppercase}
.info-value{font-size:16px;font-weight:bold;margin-top:3px}
.voucher-footer{padding:15px 25px;background:#f1f1f1;font-size:12px;text-align:center;color:#666}
.qr-section{text-align:center;margin-top:20px}
.print-btn{display:block;text-align:center;margin:20px auto;padding:10px 30px;background:#0077b6;color:#fff;border:none;border-radius:5px;cursor:pointer;font-size:16px}
@media print{.print-btn{display:none}}
</style>
</head>
<body>
<div class="voucher">
    <div class="voucher-header">
        <?php if ($logo): ?><img src="<?= e($logo) ?>" alt=""><?php endif; ?>
        <h2>VOUCHER DE PASSEIO</h2>
    </div>
    <div class="voucher-body">
        <div class="voucher-title"><?= e($item['trip_title'] ?? '') ?></div>
        <div class="voucher-ref">Código: <strong><?= e($reference) ?></strong></div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Cliente</div>
                <div class="info-value"><?= e($booking['billing_first_name'] . ' ' . $booking['billing_last_name']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value"><?= e($booking['billing_email']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Data</div>
                <div class="info-value"><?= format_date($item['trip_date'] ?? '') ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Horário</div>
                <div class="info-value"><?= e($item['trip_time'] ?? 'A confirmar') ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Passageiros</div>
                <div class="info-value"><?= e($item['pax'] ?? '') ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Valor</div>
                <div class="info-value"><?= money((float)($item['price'] ?? 0)) ?></div>
            </div>
        </div>

        <?php if ($instructions): ?>
        <div style="padding:10px;background:#fff3cd;border-radius:5px;margin-top:15px">
            <strong>Instruções:</strong><br><?= e($instructions) ?>
        </div>
        <?php endif; ?>

        <div class="qr-section">
            <img src="<?= e($qr_url) ?>" alt="QR Code" width="120" height="120">
        </div>
    </div>
    <div class="voucher-footer">
        <?= e($footer_text) ?>
    </div>
</div>
<button class="print-btn" onclick="window.print()">Imprimir Voucher</button>
</body>
</html>
