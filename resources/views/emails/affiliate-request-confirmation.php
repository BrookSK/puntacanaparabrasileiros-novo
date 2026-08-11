<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#f5f5f5;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #E4B505;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">Solicitação de Afiliação Recebida!</h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá, <?= e($firstName ?? '') ?>!</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Recebemos sua solicitação para se tornar um afiliado da <strong>Punta Cana para Brasileiros</strong>. Obrigado pelo interesse em fazer parte do nosso programa!
        </p>

        <!-- Resumo -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;">Resumo da sua solicitação</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#333;"><strong>Nome:</strong> <?= e(($firstName ?? '') . ' ' . ($lastName ?? '')) ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>Email:</strong> <?= e($email ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>WhatsApp:</strong> <?= e($phone ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#333;"><strong>Nicho:</strong> <?= e($niche ?? '') ?></td></tr>
            </table>
        </div>

        <!-- Próximos passos -->
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;">Próximos passos</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#333;">1️⃣ Nossa equipe analisará seu perfil</td></tr>
                <tr><td style="padding:6px 0;color:#333;">2️⃣ Entraremos em contato em até 48 horas</td></tr>
                <tr><td style="padding:6px 0;color:#333;">3️⃣ Se aprovado, você receberá acesso ao painel de afiliados</td></tr>
            </table>
        </div>

        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Enquanto isso, conheça melhor nossos passeios e experiências para que você já esteja preparado para divulgar!
        </p>

        <!-- CTA -->
        <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= e($siteUrl ?? 'https://puntacananovo.lrvweb.com.br') ?>/passeios" style="display:inline-block;padding:10px 24px;background:#1B6F00;color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;">Explorar Passeios</a>
        </div>

        <p style="font-size:13px;color:#666;margin-top:20px;line-height:1.7;">
            Dúvidas? Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170" style="color:#1B6F00;font-weight:600;">+1 (829) 458-2170</a>
        </p>
    </div>

    <!-- Footer -->
    <div style="background:#f8f8f8;padding:20px 40px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira & Ramos SRL</strong></p>
        <p style="font-size:11px;color:#888;margin:2px 0;">RNC: 1-33-28776-5 | Punta Cana, República Dominicana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">contato@puntacanaparabrasileiros.com | +1 (829) 458-2170</p>
        <p style="font-size:11px;color:#aaa;margin:10px 0 0;">Você recebeu este email porque solicitou cadastro no programa de afiliados.</p>
    </div>
</div>
