<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Voucher - Transfer - <?= e(($transfer['origin_title'] ?? '') . ' - ' . ($transfer['destination_title'] ?? '')) ?></title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Roboto,Arial,sans-serif;color:#1a1a1a;background:#fff;padding:0;margin:0;font-size:11px}
.voucher-page{width:100%;max-width:180mm;margin:0 auto;padding:8mm 0;position:relative}

.v-header{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f5f5f5;border-bottom:2px solid #E4B505;margin-bottom:10px;border-radius:4px 4px 0 0;overflow:hidden}
.v-header-left{display:flex;align-items:center;gap:10px;flex:1;min-width:0}
.v-logo{width:50px;height:auto}
.v-company-info h2{font-size:11px;font-weight:700;color:#1a1a1a;margin-bottom:1px}
.v-company-info p{font-size:8px;color:#555;line-height:1.3}
.v-header-right{text-align:right}
.v-header-right p{font-size:8px;color:#444;line-height:1.5}
.v-header-right strong{color:#1B6F00}
.v-type-badge{display:inline-block;background:#0077b6;color:#fff;font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 12px;border-radius:3px;margin-bottom:6px}
.v-title{font-size:16px;font-weight:700;color:#1a1a1a;margin-bottom:3px}
.v-code{font-size:10px;color:#666;margin-bottom:10px}
.v-code strong{color:#1a1a1a;font-size:11px}
.v-route-card{background:#f0fdf4;border:1px solid #86efac;border-radius:4px;padding:8px 12px;margin-bottom:10px}
.v-route-type{font-size:8px;text-transform:uppercase;letter-spacing:1px;color:#16a34a;font-weight:700;margin-bottom:2px}
.v-route-name{font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:4px;word-wrap:break-word}
.v-route-details{display:flex;gap:12px;font-size:10px;color:#555;flex-wrap:wrap}
.v-section{margin-bottom:8px}
.v-section-title{font-size:8px;text-transform:uppercase;letter-spacing:1px;color:#888;font-weight:600;margin-bottom:5px;padding-bottom:3px;border-bottom:1px solid #eee}
.v-grid{display:grid;grid-template-columns:1fr 1fr;gap:5px}
.v-cell{padding:5px 8px;background:#fafafa;border:1px solid #f0f0f0;border-radius:3px;overflow:hidden;word-wrap:break-word}
.v-cell-label{font-size:7px;text-transform:uppercase;letter-spacing:0.5px;color:#888;margin-bottom:1px}
.v-cell-value{font-size:11px;font-weight:600;color:#1a1a1a}
.v-instructions{padding:8px 10px;background:#fefce8;border:1px solid #fde047;border-radius:3px;margin-top:8px}
.v-instructions-title{font-size:8px;font-weight:700;text-transform:uppercase;color:#854d0e;margin-bottom:3px}
.v-instructions p{font-size:9px;color:#713f12;line-height:1.4;word-wrap:break-word}
.v-qr{text-align:center;margin-top:10px;padding-top:8px;border-top:1px solid #eee}
.v-qr img{width:60px;height:60px}
.v-qr p{font-size:7px;color:#aaa;margin-top:2px}
.v-footer{text-align:center;padding-top:6px;border-top:1px solid #eee;margin-top:8px}
.v-footer p{font-size:7px;color:#888;line-height:1.4}
.v-footer .v-footer-brand{font-weight:600;color:#555}
@media print{body{padding:0}.voucher-page{padding:15mm;width:100%;min-height:auto}.v-footer{position:relative;bottom:auto;left:auto;right:auto;margin-top:30px}.no-print{display:none!important}}
@media screen{body{background:#f0f0f0;padding:20px}.voucher-page{box-shadow:0 2px 20px rgba(0,0,0,0.1);border-radius:4px}}
</style>
</head>
<body>
<div class="voucher-page">
    <!-- Header -->
    <div class="v-header">
        <div class="v-header-left">
            <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Logo" class="v-logo">
            <div class="v-company-info">
                <h2>Punta Cana para Brasileiros</h2>
                <p>Oliveira & Ramos SRL — RNC: 1-33-28776-5<br>
                Av. Barceló, nº 91, Local 7 - Plaza Arrecife<br>
                Verón, Punta Cana, República Dominicana<br>
                +1 (829) 458-2170 | contato@puntacanaparabrasileiros.com</p>
            </div>
        </div>
        <div class="v-header-right">
            <p>Emissão: <strong><?= date('d/m/Y') ?></strong></p>
            <p>Moeda: <strong>USD</strong></p>
        </div>
    </div>

    <!-- Type -->
    <div style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:#1a1a1a;text-transform:uppercase;letter-spacing:1px;margin:0 0 6px;">VOUCHER TRANSFER</h1>
        <p style="font-size:13px;color:#888;font-weight:400;">Código: <?= e($reference) ?></p>
    </div>

    <!-- Rota -->
    <h2 style="font-size:20px;font-weight:700;color:#0077b6;margin-bottom:20px;"><?= e(($transfer['origin_title'] ?? '') . ' → ' . ($transfer['destination_title'] ?? '')) ?></h2>

    <!-- Route Card -->
    <div class="v-route-card">
        <div class="v-route-type">Transfer <?= ($transfer['type'] ?? '') === 'arrival' ? 'IN — Chegada' : 'OUT — Saída' ?></div>
        <div class="v-route-name"><?= e(($transfer['origin_title'] ?? '') . ' → ' . ($transfer['destination_title'] ?? '')) ?></div>
        <div class="v-route-details">
            <span>📅 <?= !empty($transfer['date']) ? date('d/m/Y', strtotime($transfer['date'])) : '-' ?></span>
            <span>🕐 <?= e($transfer['time'] ?? 'A confirmar') ?></span>
            <span>🚐 <?= e($transfer['vehicle_title'] ?? '') ?></span>
        </div>
    </div>

    <!-- Dados do Passageiro -->
    <div class="v-section">
        <div class="v-section-title">Dados do Passageiro</div>
        <div class="v-grid">
            <div class="v-cell">
                <div class="v-cell-label">Nome</div>
                <div class="v-cell-value"><?= e($transfer['customer_name'] ?? '') ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Telefone</div>
                <div class="v-cell-value"><?= e($transfer['customer_phone'] ?? '-') ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Passageiros</div>
                <div class="v-cell-value"><?= (int)($transfer['adults'] ?? 1) ?> adulto(s), <?= (int)($transfer['children'] ?? 0) ?> criança(s)</div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Serviço</div>
                <div class="v-cell-value"><?= ($transfer['service_type'] ?? '') === 'shared' ? 'Compartilhado' : 'Privativo' ?></div>
            </div>
            <?php if (!empty($transfer['flight_number'])): ?>
            <div class="v-cell">
                <div class="v-cell-label">Nº do Voo</div>
                <div class="v-cell-value"><?= e($transfer['flight_number']) ?></div>
            </div>
            <?php endif; ?>
            <div class="v-cell">
                <div class="v-cell-label">Valor Total</div>
                <div class="v-cell-value">$<?= number_format((float)($transfer['price'] ?? 0), 2) ?> USD</div>
            </div>
            <?php if (!empty($booking)): ?>
            <div class="v-cell">
                <div class="v-cell-label">Valor Pago</div>
                <div class="v-cell-value" style="color:#059669;font-weight:700;">$<?= number_format((float)($booking['paid_amount'] ?? 0), 2) ?> USD</div>
            </div>
            <?php if ((float)($booking['due_amount'] ?? 0) > 0): ?>
            <div class="v-cell">
                <div class="v-cell-label">Saldo Pendente</div>
                <div class="v-cell-value" style="color:#d97706;font-weight:700;">$<?= number_format((float)($booking['due_amount'] ?? 0), 2) ?> USD</div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Instruções -->
    <div class="v-instructions">
        <div class="v-instructions-title">Instruções Importantes</div>
        <p>Apresente este voucher pelo celular ao motorista. Não é necessário imprimir. Um representante estará aguardando com uma placa com seu nome. Em caso de atraso no voo, avise pelo WhatsApp (+1 829 458-2170). O motorista esperará até 45 minutos. Para saída do hotel: esteja pronto no lobby 10 min antes.</p>
    </div>

    <!-- QR -->
    <div class="v-qr">
        <img src="<?= e($qr_url) ?>" alt="QR">
        <p>Escaneie para validar</p>
    </div>

    <!-- Footer -->
    <div class="v-footer">
        <p class="v-footer-brand">Punta Cana para Brasileiros Oliveira & Ramos SRL — RNC: 1-33-28776-5</p>
        <p>Av. Barceló, nº 01, Local 7 - Plaza Arrecife, Verón, Punta Cana | +1 (829) 458-2170</p>
    </div>
</div>


</body>
</html>
