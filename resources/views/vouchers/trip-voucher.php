<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Voucher - <?= e($item['trip_title'] ?? '') ?></title>
<style>
body{font-family:'Segoe UI',Arial,sans-serif;margin:0;padding:20px;color:#1a1a1a;background:#f5f5f5}
.voucher{max-width:750px;margin:0 auto;background:#fff;border-radius:0;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
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
.voucher-title{font-size:24px;font-weight:700;color:#1a1a1a;margin-bottom:4px}
.voucher-ref{font-size:13px;color:#666;margin-bottom:24px}
.voucher-ref strong{color:#1a1a1a}
.info-section{margin-bottom:24px}
.info-section-title{font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#999;font-weight:600;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #eee}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.info-item{padding:14px 16px;background:#f9fafb;border-radius:8px;border:1px solid #f0f0f0}
.info-label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.3px;margin-bottom:4px}
.info-value{font-size:15px;font-weight:600;color:#1a1a1a}
.info-full{grid-column:1/-1}
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
    <!-- Cabeçalho com Logo + Dados da Empresa -->
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
            <p>Validade: <strong><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '-' ?></strong></p>
            <p>Reserva: <strong><?= e($booking['booking_number'] ?? '') ?></strong></p>
            <p>Moeda: <strong>USD (Dólares)</strong></p>
        </div>
    </div>

    <!-- Corpo do Voucher -->
    <div class="voucher-body">
        <div class="voucher-type">Voucher de Passeio</div>
        <div class="voucher-title"><?= e($item['trip_title'] ?? '') ?></div>
        <div class="voucher-ref">Código do Voucher: <strong><?= e($reference) ?></strong></div>

        <!-- Informações do Cliente -->
        <div class="info-section">
            <div class="info-section-title">Dados do Cliente</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Cliente</div>
                    <div class="info-value"><?= e(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '')) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= e($booking['billing_email'] ?? '') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telefone</div>
                    <div class="info-value"><?= e($booking['billing_phone'] ?? '-') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Passageiros</div>
                    <div class="info-value"><?= e($item['pax'] ?? '1') ?></div>
                </div>
            </div>
        </div>

        <!-- Informações do Passeio -->
        <div class="info-section">
            <div class="info-section-title">Detalhes do Passeio</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Data</div>
                    <div class="info-value"><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Horário</div>
                    <div class="info-value"><?= e($item['trip_time'] ?? 'Confirmar 1 dia antes') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ponto de Encontro</div>
                    <div class="info-value"><?= e($item['meeting_point'] ?? 'Lobby do hotel') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Valor Pago</div>
                    <div class="info-value">$<?= number_format((float)($item['price'] ?? 0), 2) ?> USD</div>
                </div>
            </div>
        </div>

        <!-- Instruções -->
        <?php if ($instructions): ?>
        <div class="voucher-instructions">
            <strong>Instruções Importantes</strong>
            <p><?= nl2br(e($instructions)) ?></p>
        </div>
        <?php else: ?>
        <div class="voucher-instructions">
            <strong>Instruções Importantes</strong>
            <p>Apresente este voucher (impresso ou no celular) no ponto de encontro. O horário exato será confirmado 1 dia antes via WhatsApp. Esteja pronto 10 minutos antes do horário agendado.</p>
        </div>
        <?php endif; ?>

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
