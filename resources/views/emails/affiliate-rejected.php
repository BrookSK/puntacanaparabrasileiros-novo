<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#fef2f2;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #dc2626;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#991b1b;font-size:22px;margin:0;">Atualização sobre sua solicitação</h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá, <?= e($firstName ?? '') ?>!</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Agradecemos seu interesse no programa de afiliados da <strong>Punta Cana para Brasileiros</strong>. Após análise, infelizmente não foi possível aprovar sua solicitação neste momento.
        </p>

        <?php if (!empty($adminNotes)): ?>
        <!-- Motivo -->
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#991b1b;text-transform:uppercase;letter-spacing:0.5px;">Observação da equipe</h3>
            <p style="font-size:14px;color:#333;margin:0;line-height:1.7;"><?= e($adminNotes) ?></p>
        </div>
        <?php endif; ?>

        <!-- Orientações -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#555;text-transform:uppercase;letter-spacing:0.5px;">O que fazer agora?</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#333;">📌 Você pode tentar novamente no futuro</td></tr>
                <tr><td style="padding:6px 0;color:#333;">📌 Fortaleça sua presença nas redes sociais</td></tr>
                <tr><td style="padding:6px 0;color:#333;">📌 Entre em contato conosco se tiver dúvidas</td></tr>
            </table>
        </div>

        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Não desanime! Nosso programa está em constante crescimento e adoraríamos contar com você no futuro. Continue acompanhando nossas novidades!
        </p>

        <!-- CTA -->
        <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= e($siteUrl ?? 'https://puntacananovo.lrvweb.com.br') ?>/passeios" style="display:inline-block;padding:10px 24px;background:#1B6F00;color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;">Conhecer Nossos Passeios</a>
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
        <p style="font-size:11px;color:#aaa;margin:10px 0 0;">Você recebeu este email referente à sua solicitação ao programa de afiliados.</p>
    </div>
</div>
