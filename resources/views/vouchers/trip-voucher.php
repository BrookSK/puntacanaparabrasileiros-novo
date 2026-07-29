<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Voucher - Viagem - <?= e($item['trip_title'] ?? '') ?></title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Roboto,Arial,sans-serif;color:#1a1a1a;background:#fff;padding:0;margin:0}
.voucher-page{width:210mm;min-height:297mm;margin:0 auto;padding:24mm 20mm;position:relative}

/* Header */
.v-header{display:flex;align-items:center;justify-content:space-between;padding:24px 30px;background:#f5f5f5;border-bottom:2px solid #E4B505;margin-bottom:24px;border-radius:6px 6px 0 0}
.v-header-left{display:flex;align-items:center;gap:16px}
.v-logo{width:80px;height:auto}
.v-company-info h2{font-size:14px;font-weight:700;color:#1a1a1a;margin-bottom:3px}
.v-company-info p{font-size:10px;color:#555;line-height:1.5}
.v-header-right{text-align:right}
.v-header-right p{font-size:10px;color:#444;line-height:1.8}
.v-header-right strong{color:#1B6F00}

/* Type Badge */
.v-type-badge{display:inline-block;background:#1B6F00;color:#fff;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:8px 20px;border-radius:4px;margin-bottom:16px}

/* Title */
.v-title{font-size:22px;font-weight:700;color:#1a1a1a;margin-bottom:6px}
.v-code{font-size:12px;color:#666;margin-bottom:28px}
.v-code strong{color:#1a1a1a;font-size:13px}

/* Sections */
.v-section{margin-bottom:22px}
.v-section-title{font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#888;font-weight:600;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid #eee}
.v-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.v-cell{padding:12px 14px;background:#fafafa;border:1px solid #f0f0f0;border-radius:6px}
.v-cell-label{font-size:9px;text-transform:uppercase;letter-spacing:0.5px;color:#888;margin-bottom:3px}
.v-cell-value{font-size:13px;font-weight:600;color:#1a1a1a}

/* Instructions */
.v-instructions{padding:14px 18px;background:#fefce8;border:1px solid #fde047;border-radius:6px;margin-top:20px}
.v-instructions-title{font-size:10px;font-weight:700;text-transform:uppercase;color:#854d0e;margin-bottom:6px}
.v-instructions p{font-size:11px;color:#713f12;line-height:1.7}

/* QR */
.v-qr{text-align:center;margin-top:24px;padding-top:18px;border-top:1px solid #eee}
.v-qr img{width:110px;height:110px}
.v-qr p{font-size:9px;color:#aaa;margin-top:4px}

/* Footer */
.v-footer{position:absolute;bottom:20mm;left:20mm;right:20mm;text-align:center;padding-top:14px;border-top:1px solid #eee}
.v-footer p{font-size:9px;color:#888;line-height:1.6}
.v-footer .v-footer-brand{font-weight:600;color:#555}

/* Print */
@media print{
    body{padding:0}
    .voucher-page{padding:15mm;width:100%;min-height:auto}
    .v-footer{position:relative;bottom:auto;left:auto;right:auto;margin-top:30px}
    .no-print{display:none!important}
}
@media screen{
    body{background:#f0f0f0;padding:20px}
    .voucher-page{box-shadow:0 2px 20px rgba(0,0,0,0.1);border-radius:4px}
}
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
                Punta Cana, República Dominicana<br>
                +1 (829) 458-2170 | contato@puntacanaparabrasileiros.com</p>
            </div>
        </div>
        <div class="v-header-right">
            <p>Emissão: <strong><?= date('d/m/Y') ?></strong></p>
            <p>Validade: <strong><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '-' ?></strong></p>
            <p>Reserva: <strong><?= e($booking['booking_number'] ?? '') ?></strong></p>
            <p>Moeda: <strong>USD</strong></p>
        </div>
    </div>

    <!-- Type Badge + Code -->
    <div style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:#1a1a1a;text-transform:uppercase;letter-spacing:1px;margin:0 0 6px;">VOUCHER PASSEIO</h1>
        <p style="font-size:13px;color:#888;font-weight:400;">Código: <?= e($reference) ?></p>
    </div>

    <!-- Nome do Passeio -->
    <h2 style="font-size:20px;font-weight:700;color:#1B6F00;margin-bottom:24px;"><?= e($item['trip_title'] ?? '') ?></h2>

    <!-- Dados do Cliente -->
    <div class="v-section">
        <div class="v-section-title">Dados do Cliente</div>
        <div class="v-grid">
            <div class="v-cell">
                <div class="v-cell-label">Nome</div>
                <div class="v-cell-value"><?= e(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '')) ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Email</div>
                <div class="v-cell-value"><?= e($booking['billing_email'] ?? '') ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Telefone</div>
                <div class="v-cell-value"><?= e($booking['billing_phone'] ?? '-') ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Passageiros</div>
                <div class="v-cell-value"><?= e($item['pax'] ?? '1') ?></div>
            </div>
        </div>
    </div>

    <!-- Detalhes do Passeio -->
    <div class="v-section">
        <div class="v-section-title">Detalhes do Passeio</div>
        <div class="v-grid">
            <div class="v-cell">
                <div class="v-cell-label">Data</div>
                <div class="v-cell-value"><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '-' ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Horário</div>
                <div class="v-cell-value"><?= e($item['trip_time'] ?? 'Confirmar 1 dia antes') ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Ponto de Encontro</div>
                <div class="v-cell-value"><?= e($item['meeting_point'] ?? 'Lobby do hotel') ?></div>
            </div>
            <div class="v-cell">
                <div class="v-cell-label">Valor</div>
                <div class="v-cell-value">$<?= number_format((float)($item['price'] ?? 0), 2) ?> USD</div>
            </div>
        </div>
    </div>

    <!-- Instruções -->
    <div class="v-instructions">
        <div class="v-instructions-title">Instruções Importantes</div>
        <?php if (!empty($instructions)): ?>
        <p><?= nl2br(e($instructions)) ?></p>
        <?php else: ?>
        <p>Apresente este voucher (impresso ou no celular) no ponto de encontro. O horário exato será confirmado 1 dia antes via WhatsApp. Esteja pronto 10 minutos antes do horário agendado.</p>
        <?php endif; ?>
    </div>

    <!-- QR Code -->
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

<!-- Botão de download (apenas na visualização) -->
<div class="no-print" style="text-align:center;padding:20px;">
    <button onclick="window.print()" style="padding:12px 32px;background:#1B6F00;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">Baixar como PDF</button>
    <p style="font-size:11px;color:#888;margin-top:8px;">Use Ctrl+P → "Salvar como PDF" para gerar o arquivo.</p>
</div>
</body>
</html>
