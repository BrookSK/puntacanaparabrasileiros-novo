<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#f0fdf4;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #1B6F00;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">✅ Sua comissão foi paga!</h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá, <?= e($firstName ?? '') ?>!</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Boa notícia! Sua comissão foi <strong style="color:#1B6F00;">paga</strong>.
        </p>

        <!-- Detalhes -->
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:20px;margin-bottom:24px;">
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#1B6F00;font-size:16px;"><strong>Valor pago:</strong> <?= e($amount ?? '') ?></td></tr>
                <?php if (!empty($reference)): ?>
                <tr><td style="padding:6px 0;color:#333;"><strong>Referência do pagamento:</strong> <?= e($reference) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <p style="font-size:14px;color:#555;line-height:1.7;">
            Obrigado por fazer parte das agências parceiras da <strong>Punta Cana para Brasileiros</strong>! 🌴
        </p>

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
        <p style="font-size:11px;color:#aaa;margin:10px 0 0;">Você recebeu este email por ser uma agência parceira.</p>
    </div>
</div>
