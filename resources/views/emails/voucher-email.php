<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#f5f5f5;padding:30px;text-align:center;border-radius:10px 10px 0 0;border-bottom:2px solid #E4B505;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#1C2011;font-size:22px;margin:0;">Seus Vouchers</h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá <strong><?= e($booking['billing_first_name'] ?? 'Cliente') ?></strong>,</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Sua reserva <strong><?= e($booking['booking_number'] ?? '') ?></strong> foi confirmada com sucesso!
            Clique nos links abaixo para visualizar, imprimir ou baixar seus vouchers.
        </p>

        <!-- Resumo da Reserva -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;">Resumo da Reserva</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#666;">Número:</td><td style="padding:6px 0;font-weight:600;"><?= e($booking['booking_number'] ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#666;">Total:</td><td style="padding:6px 0;font-weight:600;">$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?> USD</td></tr>
                <tr><td style="padding:6px 0;color:#666;">Status:</td><td style="padding:6px 0;font-weight:600;color:#16a34a;">Confirmado</td></tr>
            </table>
        </div>

        <!-- Instruções -->
        <div style="background:#fffbeb;border:1px solid #fbbf24;border-radius:8px;padding:16px 20px;margin-bottom:24px;">
            <p style="font-size:13px;color:#78350f;margin:0;line-height:1.7;">
                <strong>Importante:</strong> Apresente os vouchers (impressos ou no celular) no ponto de encontro.
                O horário exato será confirmado 1 dia antes via WhatsApp.
            </p>
        </div>

        <!-- Vouchers com links -->
        <h3 style="font-size:14px;margin:0 0 12px;color:#333;">Seus Vouchers:</h3>
        <?php foreach ($vouchers as $v): ?>
        <div style="padding:14px 16px;background:#f3f4f6;border-radius:8px;margin-bottom:10px;">
            <strong style="font-size:13px;color:#1a1a1a;display:block;margin-bottom:6px;">
                <?php if (($v['type'] ?? '') === 'transfer'): ?>
                    📄 VOUCHER TRANSFER
                <?php else: ?>
                    📄 VOUCHER PASSEIO<?php if (!empty($v['trip_name'])): ?> — <?= e($v['trip_name']) ?><?php endif; ?>
                <?php endif; ?>
            </strong>
            <span style="display:block;font-size:11px;color:#888;margin-bottom:10px;">Código: <?= e($v['reference_code'] ?? '') ?></span>
            <a href="https://puntacananovo.lrvweb.com.br/voucher/<?= e($v['reference_code'] ?? '') ?>" style="display:inline-block;padding:8px 18px;background:#1B6F00;color:#fff;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">Visualizar Voucher</a>
        </div>
        <?php endforeach; ?>

        <!-- Termos e Política de Cancelamento -->
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:20px;margin-top:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;">📋 Documentos Importantes</h3>
            <p style="font-size:13px;color:#555;margin:0 0 16px;line-height:1.6;">
                Leia atentamente nossos termos e políticas. Você pode visualizar online ou baixar em PDF para guardar.
            </p>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:8px 0;">
                        <strong style="font-size:13px;color:#1a1a1a;">Termos e Condições</strong>
                        <span style="display:block;font-size:11px;color:#888;margin-top:2px;">Regras gerais de uso e contratação dos serviços</span>
                    </td>
                    <td style="padding:8px 0;text-align:right;white-space:nowrap;">
                        <a href="https://puntacananovo.lrvweb.com.br/termos-e-condicoes" style="display:inline-block;padding:6px 14px;background:#1B6F00;color:#fff;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;margin-right:6px;">Visualizar</a>
                        <a href="https://puntacananovo.lrvweb.com.br/termos-e-condicoes/pdf" style="display:inline-block;padding:6px 14px;background:#E4B505;color:#1a1a1a;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;">⬇ Baixar PDF</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:0;"><div style="border-top:1px solid #e5e7eb;margin:4px 0;"></div></td>
                </tr>
                <tr>
                    <td style="padding:8px 0;">
                        <strong style="font-size:13px;color:#1a1a1a;">Política de Cancelamento</strong>
                        <span style="display:block;font-size:11px;color:#888;margin-top:2px;">Prazos, reembolsos e reagendamentos</span>
                    </td>
                    <td style="padding:8px 0;text-align:right;white-space:nowrap;">
                        <a href="https://puntacananovo.lrvweb.com.br/politicas-de-cancelamento" style="display:inline-block;padding:6px 14px;background:#1B6F00;color:#fff;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;margin-right:6px;">Visualizar</a>
                        <a href="https://puntacananovo.lrvweb.com.br/politicas-de-cancelamento/pdf" style="display:inline-block;padding:6px 14px;background:#E4B505;color:#1a1a1a;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;">⬇ Baixar PDF</a>
                    </td>
                </tr>
            </table>
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
    </div>
</div>
