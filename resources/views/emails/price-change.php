<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#f5f5f5;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #E4B505;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">Atualização de Preço</h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá <strong><?= e($customerName ?? 'Cliente') ?></strong>,</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            O preço de um passeio no seu carrinho foi atualizado. Veja os detalhes abaixo:
        </p>

        <!-- Tabela de mudanças -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;">Itens Atualizados</h3>
            <?php foreach ($changes as $change): ?>
            <div style="padding:12px 0;border-bottom:1px solid #f0f0f0;">
                <p style="font-size:14px;font-weight:600;margin:0 0 6px;color:#1f2937;"><?= e($change['title']) ?></p>
                <p style="font-size:13px;margin:0;color:#666;">
                    Preço anterior: <span style="text-decoration:line-through;color:#dc2626;">$<?= number_format($change['old_price'], 2) ?></span>
                    &nbsp;&rarr;&nbsp;
                    Novo preço: <strong style="color:#1B6F00;">$<?= number_format($change['new_price'], 2) ?></strong>
                </p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Info -->
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px 20px;margin-bottom:24px;">
            <p style="font-size:13px;color:#166534;margin:0;line-height:1.7;">
                O valor no seu carrinho foi atualizado automaticamente. Acesse seu carrinho para continuar com a reserva.
            </p>
        </div>

        <!-- CTA -->
        <div style="text-align:center;margin:24px 0;">
            <a href="<?= e($siteUrl ?? 'https://puntacananovo.lrvweb.com.br') ?>/carrinho" style="background:#1B6F00;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;display:inline-block;">Ver meu carrinho</a>
        </div>

        <p style="font-size:13px;color:#666;line-height:1.7;">
            Dúvidas? Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170" style="color:#1B6F00;font-weight:600;">+1 (829) 458-2170</a>
        </p>
    </div>

    <!-- Footer -->
    <div style="background:#f8f8f8;padding:20px 40px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira & Ramos SRL</strong></p>
        <p style="font-size:11px;color:#888;margin:2px 0;">Av. Barceló, nº 91, Local 7 - Plaza Arrecife, Verón, Punta Cana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">RNC: 1-33-28776-5 | República Dominicana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">contato@puntacanaparabrasileiros.com | +1 (829) 458-2170</p>
    </div>
</div>
