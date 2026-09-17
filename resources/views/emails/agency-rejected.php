<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <div style="background:#fef2f2;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #dc2626;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">Atualização sobre sua solicitação</h1>
    </div>
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá, <?= e($contactName ?? '') ?>!</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:20px;">
            Analisamos a solicitação da agência <strong><?= e($companyName ?? '') ?></strong> e, neste momento, ela <strong>não foi aprovada</strong>.
        </p>
        <?php if (!empty($reason)): ?>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px 20px;margin-bottom:20px;">
            <h3 style="font-size:13px;margin:0 0 8px;color:#991b1b;text-transform:uppercase;letter-spacing:0.5px;">Motivo</h3>
            <p style="font-size:14px;color:#333;margin:0;line-height:1.6;"><?= e($reason) ?></p>
        </div>
        <?php endif; ?>
        <p style="font-size:14px;color:#555;line-height:1.7;">
            Você pode tentar novamente futuramente. Qualquer dúvida, estamos à disposição.
        </p>
        <p style="font-size:13px;color:#666;margin-top:20px;line-height:1.7;">
            Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170" style="color:#1B6F00;font-weight:600;">+1 (829) 458-2170</a>
        </p>
    </div>
    <div style="background:#f8f8f8;padding:20px 40px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira &amp; Ramos SRL</strong></p>
        <p style="font-size:11px;color:#888;margin:2px 0;">contato@puntacanaparabrasileiros.com | +1 (829) 458-2170</p>
    </div>
</div>
