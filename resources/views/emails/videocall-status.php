<?php
$status = $status ?? '';
$showLink = !empty($link) && $status !== 'cancelled';
// Cor do header: verde para confirmada/concluída, vermelho suave para cancelada/removida
$isNegative = in_array($status, ['cancelled'], true);
$headerBg = $isNegative ? '#fef2f2' : '#f0fdf4';
$headerBorder = $isNegative ? '#dc2626' : '#1B6F00';
$boxBg = $isNegative ? '#fef2f2' : '#f0fdf4';
$boxBorder = $isNegative ? '#fecaca' : '#bbf7d0';
$accent = $isNegative ? '#b91c1c' : '#1B6F00';
?>
<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:<?= $headerBg ?>;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid <?= $headerBorder ?>;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">📹 <?= e($title ?? 'Atualização da sua chamada de vídeo') ?></h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá, <?= e($firstName ?? '') ?>!</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            <?= $intro ?? '' ?>
        </p>

        <!-- Detalhes -->
        <div style="background:<?= $boxBg ?>;border:1px solid <?= $boxBorder ?>;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:<?= $accent ?>;text-transform:uppercase;letter-spacing:0.5px;">Detalhes da chamada</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#333;"><strong>📅 Data e hora:</strong> <?= e($when ?? '') ?></td></tr>
                <?php if (!empty($tripTitle)): ?>
                <tr><td style="padding:6px 0;color:#333;"><strong>🏝️ Passeio:</strong> <?= e($tripTitle) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <?php if ($showLink): ?>
        <!-- CTA -->
        <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= e($link) ?>" style="display:inline-block;padding:12px 28px;background:#1B6F00;color:#fff;border-radius:6px;font-size:14px;font-weight:600;text-decoration:none;">Entrar na reunião</a>
        </div>
        <p style="font-size:13px;color:#666;line-height:1.7;margin-bottom:20px;">
            Se o botão não funcionar, copie e cole este link no seu navegador:<br>
            <a href="<?= e($link) ?>" style="color:#1B6F00;word-break:break-all;"><?= e($link) ?></a>
        </p>
        <?php else: ?>
        <!-- CTA agendar novo -->
        <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= e($siteUrl ?? 'https://puntacananovo.lrvweb.com.br') ?>/passeios" style="display:inline-block;padding:12px 28px;background:#1B6F00;color:#fff;border-radius:6px;font-size:14px;font-weight:600;text-decoration:none;">Ver Passeios</a>
        </div>
        <?php endif; ?>

        <p style="font-size:13px;color:#666;margin-top:20px;line-height:1.7;">
            Dúvidas? Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170" style="color:#1B6F00;font-weight:600;">+1 (829) 458-2170</a>
        </p>
    </div>

    <!-- Footer -->
    <div style="background:#f8f8f8;padding:20px 40px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira &amp; Ramos SRL</strong></p>
        <p style="font-size:11px;color:#888;margin:2px 0;">Av. Barceló, nº 91, Local 7 - Plaza Arrecife, Verón, Punta Cana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">RNC: 1-33-28776-5 | República Dominicana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">contato@puntacanaparabrasileiros.com | +1 (829) 458-2170</p>
        <p style="font-size:11px;color:#aaa;margin:10px 0 0;">Você recebeu este email por ter agendado uma chamada de vídeo no nosso site.</p>
    </div>
</div>
