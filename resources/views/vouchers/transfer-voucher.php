<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Voucher Transfer - <?= e($reference) ?></title>
<style>
body{font-family:Arial,sans-serif;margin:0;padding:20px;color:#333}
.voucher{max-width:700px;margin:0 auto;border:2px solid #00b4d8;border-radius:10px;overflow:hidden}
.voucher-header{background:#00b4d8;color:#fff;padding:20px;text-align:center}
.voucher-body{padding:25px}
.route-info{text-align:center;margin:20px 0;font-size:18px}
.route-arrow{font-size:24px;margin:0 10px;color:#00b4d8}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px}
.info-item{padding:10px;background:#f8f9fa;border-radius:5px}
.info-label{font-size:12px;color:#666;text-transform:uppercase}
.info-value{font-size:15px;font-weight:bold;margin-top:3px}
.voucher-footer{padding:15px 25px;background:#f1f1f1;font-size:12px;text-align:center;color:#666}
.qr-section{text-align:center;margin-top:20px}
.print-btn{display:block;text-align:center;margin:20px auto;padding:10px 30px;background:#00b4d8;color:#fff;border:none;border-radius:5px;cursor:pointer;font-size:16px}
@media print{.print-btn{display:none}}
</style>
</head>
<body>
<div class="voucher">
    <div class="voucher-header">
        <?php if ($logo): ?><img src="<?= e($logo) ?>" alt="" style="max-height:50px;margin-bottom:10px"><?php endif; ?>
        <h2>VOUCHER DE TRANSFER</h2>
    </div>
    <div class="voucher-body">
        <div style="text-align:center;margin-bottom:5px;font-size:14px;color:#666">Código: <strong><?= e($reference) ?></strong></div>

        <div class="route-info">
            <strong><?= e($transfer['origin_title']) ?></strong>
            <span class="route-arrow">&rarr;</span>
            <strong><?= e($transfer['destination_title']) ?></strong>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Veículo</div>
                <div class="info-value"><?= e($transfer['vehicle_title']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Tipo</div>
                <div class="info-value"><?= $transfer['type'] === 'arrival' ? 'Chegada' : 'Partida' ?> (<?= e(ucfirst($transfer['service_type'])) ?>)</div>
            </div>
            <div class="info-item">
                <div class="info-label">Data</div>
                <div class="info-value"><?= format_date($transfer['date']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Horário</div>
                <div class="info-value"><?= e($transfer['time']) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Passageiros</div>
                <div class="info-value"><?= (int)$transfer['adults'] ?> adulto(s), <?= (int)$transfer['children'] ?> criança(s), <?= (int)$transfer['infants'] ?> bebê(s)</div>
            </div>
            <div class="info-item">
                <div class="info-label">Cliente</div>
                <div class="info-value"><?= e($transfer['customer_name']) ?></div>
            </div>
            <?php if ($transfer['flight_number']): ?>
            <div class="info-item">
                <div class="info-label">Voo</div>
                <div class="info-value"><?= e($transfer['flight_number']) ?><?= $transfer['flight_time'] ? ' às ' . e($transfer['flight_time']) : '' ?></div>
            </div>
            <?php endif; ?>
            <div class="info-item">
                <div class="info-label">Valor</div>
                <div class="info-value"><?= money((float)$transfer['price']) ?></div>
            </div>
        </div>

        <div class="qr-section">
            <img src="<?= e($qr_url) ?>" alt="QR Code" width="120" height="120">
        </div>
    </div>
    <div class="voucher-footer"><?= e($footer_text) ?></div>
</div>
<button class="print-btn" onclick="window.print()">Imprimir Voucher</button>
</body>
</html>
