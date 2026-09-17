<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#f0fdf4;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #1B6F00;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">Nova solicitação de agência</h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Uma nova agência solicitou parceria com a <strong>Punta Cana para Brasileiros</strong>. Confira os dados abaixo e aprove ou recuse pelo painel.
        </p>

        <!-- Dados da agência -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;">Dados da agência</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#333;"><strong>Razão social:</strong> <?= e($companyName ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>Contato:</strong> <?= e($contactName ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>E-mail:</strong> <?= e($email ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>Telefone:</strong> <?= e($phone ?? '') ?></td></tr>
            </table>
        </div>

        <!-- CTA -->
        <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= e($siteUrl ?? 'https://puntacananovo.lrvweb.com.br') ?>/admin/agencias" style="display:inline-block;padding:12px 28px;background:#1B6F00;color:#fff;border-radius:6px;font-size:14px;font-weight:600;text-decoration:none;">Abrir Painel de Agências</a>
        </div>
    </div>

    <!-- Footer -->
    <div style="background:#f8f8f8;padding:20px 40px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira &amp; Ramos SRL</strong></p>
        <p style="font-size:11px;color:#aaa;margin:10px 0 0;">Notificação automática do sistema.</p>
    </div>
</div>
