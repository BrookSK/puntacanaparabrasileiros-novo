<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#f0fdf4;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #1B6F00;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">📹 <?= e($eventLabel ?? 'Nova chamada de vídeo agendada') ?></h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Atualização de um agendamento de chamada de vídeo. Confira os detalhes abaixo:
        </p>

        <!-- Dados do cliente -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;">Dados do agendamento</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#333;"><strong>👤 Cliente:</strong> <?= e($name ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>📞 Telefone:</strong> <?= e($phone ?? '-') ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>✉️ Email:</strong> <?= e($email ?? '-') ?></td></tr>
                <?php if (!empty($tripTitle)): ?>
                <tr><td style="padding:6px 0;color:#333;"><strong>🏝️ Passeio:</strong> <?= e($tripTitle) ?></td></tr>
                <?php endif; ?>
                <tr><td style="padding:6px 0;color:#333;"><strong>📅 Data e hora:</strong> <?= e($when ?? '') ?></td></tr>
                <?php if (!empty($notes)): ?>
                <tr><td style="padding:6px 0;color:#333;"><strong>📝 Observações:</strong> <?= e($notes) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- CTA -->
        <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= e(($siteUrl ?? 'https://puntacananovo.lrvweb.com.br')) ?>/admin/agendamentos" style="display:inline-block;padding:12px 28px;background:#1B6F00;color:#fff;border-radius:6px;font-size:14px;font-weight:600;text-decoration:none;">Gerenciar Agendamentos</a>
        </div>

        <p style="font-size:13px;color:#666;line-height:1.7;">
            Link da reunião gerado:<br>
            <a href="<?= e($link ?? '#') ?>" style="color:#1B6F00;word-break:break-all;"><?= e($link ?? '') ?></a>
        </p>
    </div>

    <!-- Footer -->
    <div style="background:#f8f8f8;padding:20px 40px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira &amp; Ramos SRL</strong></p>
        <p style="font-size:11px;color:#888;margin:2px 0;">Notificação interna do sistema.</p>
    </div>
</div>
