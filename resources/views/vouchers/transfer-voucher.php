<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Voucher Transfer - <?= e(($transfer['origin_title'] ?? '') . ' → ' . ($transfer['destination_title'] ?? '')) ?></title>
<style>
body{font-family:'Segoe UI',Arial,sans-serif;margin:0;padding:20px;color:#1a1a1a;background:#f5f5f5}
.voucher{max-width:750px;margin:0 auto;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
.voucher-header{padding:30px 40px;border-bottom:3px solid #1B6F00;display:flex;align-items:flex-start;gap:20px}
.voucher-logo{flex-shrink:0}
.voucher-logo img{max-height:90px}
.voucher-company{flex:1}
.voucher-company h1{font-size:18px;font-weight:700;margin:0 0 6px;color:#1a1a1a}
.voucher-company p{font-size:12px;color:#555;margin:2px 0;line-height:1.5}
.voucher-meta{text-align:right;flex-shrink:0}
.voucher-meta p{font-size:12px;color:#333;margin:3px 0;font-weight:500}
.voucher-meta strong{color:#1B6F00}
.voucher-body{padding:30px 40px}
.voucher-type{font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:#1B6F00;font-weight:700;margin-bottom:6px}
.voucher-title{font-size:22px;font-weight:700;color:#1a1a1a;margin-bottom:4px}
.voucher-ref{font-size:13px;color:#666;margin-bottom:24px}
.voucher-ref strong{color:#1a1a1a}
.route-card{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:20px;margin-bottom:20px}
.route-label{font-size:11px;text-transform:uppercase;color:#16a34a;font-weight:700;letter-spacing:0.5px;margin-bottom:8px}
.route-value{font-size:18px;font-weight:700;color:#1a1a1a}
.route-details{margin-top:10px;font-size:13px;color:#555}
.route-details span{display:inline-block;margin-right:16px}
.info-section{margin-bottom:24px}
.info-section-title{font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#999;font-weight:600;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #eee}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.info-item{padding:14px 16px;background:#f9fafb;border-radius:8px;border:1px solid #f0f0f0}
.info-label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.3px;margin-bottom:4px}
.info-value{font-size:15px;font-weight:600;color:#1a1a1a}
.voucher-instructions{padding:16px 20px;background:#fffbeb;border:1px solid #fbbf24;border-radius:8px;margin-top:20px}
.voucher-instructions strong{display:block;font-size:12px;color:#92400e;margin-bottom:6px;text-transform:uppercase}
.voucher-instructions p{font-size:13px;color:#78350f;margin:0;line-height:1.6}
.voucher-qr{text-align:center;margin-top:24px;padding-top:20px;border-top:1px solid #eee}
.voucher-qr img{border-radius:8px}
.voucher-qr p{font-size:11px;color:#999;margin-top:6px}
.voucher-footer{padding:20px 40px;background:#f8f8f8;border-top:1px solid #eee;text-align:center}
.voucher-footer p{font-size:11px;color:#888;margin:2px 0;line-height:1.5}
.voucher-footer .company-data{font-weight:600;color:#555}
.print-btn{display:block;text-align:center;margin:20px auto;padding:12px 36px;background:#1B6F00;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:600}
.print-btn:hover{background:#145200}
@media print{.print-btn{display:none}.voucher{box-shadow:none}body{background:#fff;padding:0}}
</style>
</head>
<body>
<div class="voucher">
    <!-- Cabeçalho -->
    <div class="voucher-header">
        <div class="voucher-logo">
            <?php if ($logo): ?>
            <img src="<?= e($logo) ?>" alt="Punta Cana para Brasileiros">
            <?php else: ?>
            <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros">
            <?php endif; ?>
        </div>
        <div class="voucher-company">
            <h1>Punta Cana para Brasileiros Oliveira & Ramos SRL</h1>
            <p>RNC: 1-33-28776-5</p>
            <p>Punta Cana - República Dominicana</p>
            <p>contato@puntacanaparabrasileiros.com</p>
            <p>Teléfono: +1-829-458-2170</p>
        </div>
        <div class="voucher-meta">
            <p>Data de emissão: <strong><?= date('d/m/Y') ?></strong></p>
            <p>Validade: <strong><?= !empty($transfer['date']) ? date('d/m/Y', strtotime($transfer['date'])) : '-' ?></strong></p>
            <p>Moeda: <strong>USD (Dólares)</strong></p>
        </div>
    </div>

    <!-- Corpo -->
    <div class="voucher-body">
        <div class="voucher-type">Voucher de Transfer</div>
        <div class="voucher-title"><?= e(($transfer['origin_title'] ?? '') . ' → ' . ($transfer['destination_title'] ?? '')) ?></div>
        <div class="voucher-ref">Código do Voucher: <strong><?= e($reference) ?></strong></div>

        <!-- Rota -->
        <div class="route-card">
            <div class="route-label">Transfer <?= ($transfer['type'] ?? '') === 'arrival' ? 'IN (Chegada)' : 'OUT (Saída)' ?></div>
            <div class="route-value"><?= e(($transfer['origin_title'] ?? '') . ' → ' . ($transfer['destination_title'] ?? '')) ?></div>
            <div class="route-details">
                <span>📅 <?= !empty($transfer['date']) ? date('d/m/Y', strtotime($transfer['date'])) : '-' ?></span>
                <span>🕐 <?= e($transfer['time'] ?? 'A confirmar') ?></span>
                <span>🚐 <?= e($transfer['vehicle_title'] ?? '') ?></span>
            </div>
        </div>

        <!-- Dados do Cliente -->
        <div class="info-section">
            <div class="info-section-title">Dados do Passageiro</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Cliente</div>
                    <div class="info-value"><?= e($transfer['customer_name'] ?? '') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telefone</div>
                    <div class="info-value"><?= e($transfer['customer_phone'] ?? '-') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Passageiros</div>
                    <div class="info-value"><?= (int)($transfer['adults'] ?? 1) ?> adulto(s), <?= (int)($transfer['children'] ?? 0) ?> criança(s)</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tipo de Serviço</div>
                    <div class="info-value"><?= ($transfer['service_type'] ?? 'private') === 'private' ? 'Privativo' : 'Compartilhado' ?></div>
                </div>
                <?php if (!empty($transfer['flight_number'])): ?>
                <div class="info-item">
                    <div class="info-label">Nº do Voo</div>
                    <div class="info-value"><?= e($transfer['flight_number']) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <div class="info-label">Valor</div>
                    <div class="info-value">$<?= number_format((float)($transfer['price'] ?? 0), 2) ?> USD</div>
                </div>
            </div>
        </div>

        <!-- Instruções -->
        <div class="voucher-instructions">
            <strong>Instruções Importantes</strong>
            <p>Um representante estará aguardando no aeroporto/hotel com uma placa com seu nome. Em caso de atraso no voo, avise pelo WhatsApp. O motorista esperará até 45 minutos após o horário previsto de chegada. Saída do hotel: esteja pronto no lobby com 10 min de antecedência.</p>
        </div>

        <!-- QR Code -->
        <div class="voucher-qr">
            <img src="<?= e($qr_url) ?>" alt="QR Code" width="130" height="130">
            <p>Escaneie para validar o voucher</p>
        </div>
    </div>

    <!-- Rodapé -->
    <div class="voucher-footer">
        <?php if ($footer_text): ?>
        <p><?= e($footer_text) ?></p>
        <?php endif; ?>
        <p class="company-data">Punta Cana para Brasileiros Oliveira & Ramos SRL — RNC: 1-33-28776-5</p>
        <p>Av. Barceló, nº 01, Local 7 - Plaza Arrecife, Verón - Punta Cana, República Dominicana</p>
        <p>WhatsApp: +1 (829) 458-2170 | contato@puntacanaparabrasileiros.com</p>
    </div>
</div>

<button class="print-btn" onclick="window.print()">Imprimir / Salvar PDF</button>
</body>
</html>
