<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Confirmação de Transfer — Punta Cana para Brasileiros</title>
</head>
<body style="font-family:'Segoe UI',Arial,sans-serif;background:#f4f7f6;margin:0;padding:30px 20px;">

<div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

    <!-- ===== HEADER ===== -->
    <div style="background:#f5f5f5;padding:30px;text-align:center;border-bottom:2px solid #E4B505;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png"
             alt="Punta Cana para Brasileiros"
             style="max-height:60px;margin-bottom:14px;display:block;margin-left:auto;margin-right:auto;">
        <h1 style="color:#1C2011;font-size:22px;margin:0 0 4px;">Transfer Confirmado!</h1>
        <p style="margin:0;font-size:13px;color:#666;">Seu transporte está garantido. Fique tranquilo!</p>
    </div>

    <!-- ===== BODY ===== -->
    <div style="background:#ffffff;padding:32px 36px;border-left:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">

        <!-- Saudação -->
        <p style="font-size:16px;color:#1e293b;margin:0 0 6px;">
            Olá, <strong><?= e($clientName ?? 'Cliente') ?></strong>!
        </p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin:0 0 28px;">
            Sua reserva de transfer foi <strong style="color:#1B6F00;">confirmada com sucesso</strong>.
            Abaixo você encontra todos os detalhes do seu transporte.
        </p>

        <!-- ===== RESUMO DA RESERVA ===== -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:28px;">
            <h3 style="font-size:13px;font-weight:700;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
                Resumo da Reserva
            </h3>
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tr>
                    <td style="padding:7px 0;color:#64748b;width:160px;">Número da Reserva</td>
                    <td style="padding:7px 0;font-weight:700;color:#1e293b;"><?= e($bookingNumber ?? '') ?></td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#64748b;border-top:1px solid #f0f0f0;">Tipo de Transfer</td>
                    <td style="padding:7px 0;font-weight:700;color:#1e293b;border-top:1px solid #f0f0f0;">
                        <?php
                            $tipo = $transferType ?? 'one_way';
                            if ($tipo === 'round_trip') echo 'Ida e Volta';
                            elseif ($tipo === 'multiple')  echo 'Múltiplos Transfers';
                            else                           echo 'Somente Ida';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#64748b;border-top:1px solid #f0f0f0;">Total Pago</td>
                    <td style="padding:7px 0;font-weight:700;color:#1B6F00;border-top:1px solid #f0f0f0;">
                        $<?= number_format((float)($totalAmount ?? 0), 2) ?> USD
                    </td>
                </tr>
                <?php if (!empty($serviceType)): ?>
                <tr>
                    <td style="padding:7px 0;color:#64748b;border-top:1px solid #f0f0f0;">Tipo de Serviço</td>
                    <td style="padding:7px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;">
                        <?= ($serviceType === 'shared') ? 'Compartilhado' : 'Privativo' ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <?php
        // ==========================================================
        // BLOCO CENTRAL: renderiza os transfers conforme o tipo
        // $transfers = array de transfer_bookings com origin_title,
        //              destination_title, date, time, vehicle_title,
        //              adults, children, flight_number, service_type, type
        // ==========================================================
        $tipo = $transferType ?? 'one_way';
        ?>

        <?php if ($tipo === 'round_trip' && !empty($transfers)): ?>
            <!-- ===== IDA E VOLTA ===== -->
            <h3 style="font-size:13px;font-weight:700;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
                Detalhes da Viagem
            </h3>

            <?php foreach ($transfers as $idx => $tr): ?>
            <?php $isArrival = (($tr['type'] ?? '') === 'arrival' || $idx === 0); ?>
            <div style="border:1px solid <?= $isArrival ? '#86efac' : '#fde047' ?>;border-radius:8px;overflow:hidden;margin-bottom:16px;">
                <!-- Cabeçalho do trecho -->
                <div style="background:<?= $isArrival ? '#f0fdf4' : '#fefce8' ?>;padding:12px 18px;border-bottom:1px solid <?= $isArrival ? '#86efac' : '#fde047' ?>;">
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:<?= $isArrival ? '#16a34a' : '#854d0e' ?>;">
                        <?= $isArrival ? '✈ Chegada — Trecho de Ida' : '✈ Partida — Trecho de Volta' ?>
                    </span>
                </div>
                <!-- Rota -->
                <div style="padding:16px 18px;">
                    <p style="font-size:16px;font-weight:700;color:#1a1a1a;margin:0 0 14px;">
                        <?= e(($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '')) ?>
                    </p>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <tr>
                            <td style="padding:6px 0;color:#64748b;width:140px;">Data</td>
                            <td style="padding:6px 0;font-weight:600;color:#1e293b;">
                                <?= !empty($tr['date']) ? date('d/m/Y', strtotime($tr['date'])) : '—' ?>
                                <?php if (!empty($tr['time'])): ?> às <?= e($tr['time']) ?><?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;color:#64748b;border-top:1px solid #f0f0f0;">Veículo</td>
                            <td style="padding:6px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;"><?= e($tr['vehicle_title'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;color:#64748b;border-top:1px solid #f0f0f0;">Passageiros</td>
                            <td style="padding:6px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;">
                                <?= (int)($tr['adults'] ?? 1) ?> adulto(s)<?= ((int)($tr['children'] ?? 0) > 0) ? ', ' . (int)$tr['children'] . ' criança(s)' : '' ?>
                            </td>
                        </tr>
                        <?php if (!empty($tr['flight_number'])): ?>
                        <tr>
                            <td style="padding:6px 0;color:#64748b;border-top:1px solid #f0f0f0;">Nº do Voo</td>
                            <td style="padding:6px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;"><?= e($tr['flight_number']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>

        <?php elseif ($tipo === 'multiple' && !empty($transfers)): ?>
            <!-- ===== MÚLTIPLOS TRANSFERS ===== -->
            <h3 style="font-size:13px;font-weight:700;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
                Seus Transfers (<?= count($transfers) ?> trecho<?= count($transfers) > 1 ? 's' : '' ?>)
            </h3>

            <?php foreach ($transfers as $idx => $tr): ?>
            <div style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:14px;">
                <!-- Número do trecho -->
                <div style="background:#f5f5f5;padding:10px 18px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px;">
                    <span style="display:inline-block;background:#1B6F00;color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;min-width:24px;text-align:center;">
                        <?= ($idx + 1) ?>
                    </span>
                    <span style="font-size:12px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:0.5px;">
                        Transfer <?= ($idx + 1) ?>
                        <?php if (!empty($tr['date'])): ?> — <?= date('d/m/Y', strtotime($tr['date'])) ?><?php endif; ?>
                    </span>
                </div>
                <!-- Detalhes -->
                <div style="padding:14px 18px;">
                    <p style="font-size:15px;font-weight:700;color:#1a1a1a;margin:0 0 12px;">
                        <?= e(($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '')) ?>
                    </p>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <tr>
                            <td style="padding:5px 0;color:#64748b;width:140px;">Data e Hora</td>
                            <td style="padding:5px 0;font-weight:600;color:#1e293b;">
                                <?= !empty($tr['date']) ? date('d/m/Y', strtotime($tr['date'])) : '—' ?>
                                <?php if (!empty($tr['time'])): ?> às <?= e($tr['time']) ?><?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0;color:#64748b;border-top:1px solid #f0f0f0;">Veículo</td>
                            <td style="padding:5px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;"><?= e($tr['vehicle_title'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0;color:#64748b;border-top:1px solid #f0f0f0;">Passageiros</td>
                            <td style="padding:5px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;">
                                <?= (int)($tr['adults'] ?? 1) ?> adulto(s)<?= ((int)($tr['children'] ?? 0) > 0) ? ', ' . (int)$tr['children'] . ' criança(s)' : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0;color:#64748b;border-top:1px solid #f0f0f0;">Serviço</td>
                            <td style="padding:5px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;">
                                <?= (($tr['service_type'] ?? '') === 'shared') ? 'Compartilhado' : 'Privativo' ?>
                            </td>
                        </tr>
                        <?php if (!empty($tr['flight_number'])): ?>
                        <tr>
                            <td style="padding:5px 0;color:#64748b;border-top:1px solid #f0f0f0;">Nº do Voo</td>
                            <td style="padding:5px 0;font-weight:600;color:#1e293b;border-top:1px solid #f0f0f0;"><?= e($tr['flight_number']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- ===== SOMENTE IDA ===== -->
            <?php $tr = $transfers[0] ?? []; ?>
            <h3 style="font-size:13px;font-weight:700;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 14px;">
                Detalhes do Transfer
            </h3>

            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:18px 20px;margin-bottom:20px;">
                <p style="font-size:18px;font-weight:700;color:#1a1a1a;margin:0 0 16px;">
                    <?= e((($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? ''))) ?>
                </p>
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <tr>
                        <td style="padding:7px 0;color:#374151;width:150px;">Data</td>
                        <td style="padding:7px 0;font-weight:600;color:#1e293b;">
                            <?= !empty($tr['date']) ? date('d/m/Y', strtotime($tr['date'])) : '—' ?>
                            <?php if (!empty($tr['time'])): ?> às <?= e($tr['time']) ?><?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:7px 0;color:#374151;border-top:1px solid #d1fae5;">Veículo</td>
                        <td style="padding:7px 0;font-weight:600;color:#1e293b;border-top:1px solid #d1fae5;"><?= e($tr['vehicle_title'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td style="padding:7px 0;color:#374151;border-top:1px solid #d1fae5;">Passageiros</td>
                        <td style="padding:7px 0;font-weight:600;color:#1e293b;border-top:1px solid #d1fae5;">
                            <?= (int)($tr['adults'] ?? 1) ?> adulto(s)<?= ((int)($tr['children'] ?? 0) > 0) ? ', ' . (int)$tr['children'] . ' criança(s)' : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:7px 0;color:#374151;border-top:1px solid #d1fae5;">Serviço</td>
                        <td style="padding:7px 0;font-weight:600;color:#1e293b;border-top:1px solid #d1fae5;">
                            <?= (($tr['service_type'] ?? '') === 'shared') ? 'Compartilhado' : 'Privativo' ?>
                        </td>
                    </tr>
                    <?php if (!empty($tr['flight_number'])): ?>
                    <tr>
                        <td style="padding:7px 0;color:#374151;border-top:1px solid #d1fae5;">Nº do Voo</td>
                        <td style="padding:7px 0;font-weight:600;color:#1e293b;border-top:1px solid #d1fae5;"><?= e($tr['flight_number']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        <?php endif; ?>

        <!-- ===== AVISO IMPORTANTE ===== -->
        <div style="background:#fffbeb;border:1px solid #fbbf24;border-radius:8px;padding:16px 20px;margin-bottom:28px;">
            <p style="font-size:13px;color:#78350f;margin:0;line-height:1.7;">
                <strong>📋 Importante:</strong>
                Nosso representante estará aguardando com uma placa com seu nome no local de chegada.
                Em caso de atraso no voo, avise imediatamente pelo WhatsApp.
                O motorista aguardará até <strong>45 minutos</strong> após o horário previsto.
                Para saída de hotel: esteja no lobby <strong>10 minutos antes</strong> do horário combinado.
            </p>
        </div>

        <!-- ===== VOUCHER ===== -->
        <?php if (!empty($voucherLinks)): ?>
        <h3 style="font-size:14px;font-weight:700;color:#333;margin:0 0 12px;">Seu(s) Voucher(s):</h3>
        <?php foreach ($voucherLinks as $v): ?>
        <div style="padding:14px 16px;background:#f3f4f6;border-radius:8px;margin-bottom:10px;">
            <strong style="font-size:13px;color:#1a1a1a;display:block;margin-bottom:4px;">
                🚐 VOUCHER TRANSFER — <?= e(($v['origin'] ?? '') . ' → ' . ($v['destination'] ?? '')) ?>
            </strong>
            <span style="display:block;font-size:11px;color:#888;margin-bottom:10px;">Código: <?= e($v['reference_code'] ?? '') ?></span>
            <a href="https://puntacananovo.lrvweb.com.br/voucher/<?= e($v['reference_code'] ?? '') ?>"
               style="display:inline-block;padding:8px 18px;background:#1B6F00;color:#fff;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                Visualizar Voucher
            </a>
        </div>
        <?php endforeach; ?>
        <div style="margin-bottom:24px;"></div>
        <?php endif; ?>

        <!-- ===== DOCUMENTOS IMPORTANTES ===== -->
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:20px;margin-bottom:28px;">
            <h3 style="font-size:13px;font-weight:700;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 12px;">📋 Documentos Importantes</h3>
            <p style="font-size:13px;color:#555;margin:0 0 16px;line-height:1.6;">
                Leia atentamente nossos termos e políticas. Você pode visualizar online ou baixar em PDF.
            </p>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:8px 0;vertical-align:middle;">
                        <strong style="font-size:13px;color:#1a1a1a;display:block;">Termos e Condições</strong>
                        <span style="font-size:11px;color:#888;">Regras gerais de uso e contratação dos serviços</span>
                    </td>
                    <td style="padding:8px 0;text-align:right;white-space:nowrap;vertical-align:middle;">
                        <a href="https://puntacananovo.lrvweb.com.br/termos-e-condicoes"
                           style="display:inline-block;padding:6px 14px;background:#1B6F00;color:#fff;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;margin-right:6px;">Visualizar</a>
                        <a href="https://puntacananovo.lrvweb.com.br/termos-e-condicoes/pdf"
                           style="display:inline-block;padding:6px 14px;background:#E4B505;color:#1a1a1a;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;">⬇ Baixar PDF</a>
                    </td>
                </tr>
                <tr><td colspan="2"><div style="border-top:1px solid #e5e7eb;margin:4px 0;"></div></td></tr>
                <tr>
                    <td style="padding:8px 0;vertical-align:middle;">
                        <strong style="font-size:13px;color:#1a1a1a;display:block;">Política de Cancelamento</strong>
                        <span style="font-size:11px;color:#888;">Prazos, reembolsos e reagendamentos</span>
                    </td>
                    <td style="padding:8px 0;text-align:right;white-space:nowrap;vertical-align:middle;">
                        <a href="https://puntacananovo.lrvweb.com.br/politicas-de-cancelamento"
                           style="display:inline-block;padding:6px 14px;background:#1B6F00;color:#fff;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;margin-right:6px;">Visualizar</a>
                        <a href="https://puntacananovo.lrvweb.com.br/politicas-de-cancelamento/pdf"
                           style="display:inline-block;padding:6px 14px;background:#E4B505;color:#1a1a1a;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;">⬇ Baixar PDF</a>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== WHATSAPP ===== -->
        <p style="font-size:13px;color:#666;margin:0 0 8px;line-height:1.7;">
            Dúvidas? Fale conosco pelo WhatsApp:
            <a href="https://api.whatsapp.com/send?phone=18294582170"
               style="color:#1B6F00;font-weight:600;text-decoration:none;">+1 (829) 458-2170</a>
        </p>

    </div><!-- /body -->

    <!-- ===== FOOTER ===== -->
    <div style="background:#f8f8f8;padding:20px 36px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira &amp; Ramos SRL</strong></p>
        <p style="font-size:11px;color:#888;margin:2px 0;">Av. Barceló, nº 91, Local 7 - Plaza Arrecife, Verón, Punta Cana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">RNC: 1-33-28776-5 | República Dominicana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">contato@puntacanaparabrasileiros.com | +1 (829) 458-2170</p>
    </div>

</div><!-- /wrapper -->
</body>
</html>
