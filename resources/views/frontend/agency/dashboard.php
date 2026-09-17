<section class="section" style="padding:40px 0;">
    <div class="container" style="max-width:1000px;">

        <!-- Cabeçalho -->
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:28px;">
            <div>
                <h1 style="font-size:24px;color:#1e293b;margin:0 0 4px;">Painel da Agência</h1>
                <p style="color:#64748b;font-size:15px;margin:0;">Bem-vindo, <strong><?= e($agency['trade_name'] ?: $agency['company_name']) ?></strong>!</p>
            </div>
            <a href="/logout" class="btn btn-outline">Sair</a>
        </div>

        <!-- Cards de resumo -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px;">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;">
                <div style="font-size:13px;color:#64748b;margin-bottom:6px;">Total de Vendas</div>
                <div style="font-size:26px;font-weight:700;color:#0077b6;"><?= money((float)($agency['total_sales'] ?? 0)) ?></div>
            </div>
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;">
                <div style="font-size:13px;color:#64748b;margin-bottom:6px;">Comissão Total</div>
                <div style="font-size:26px;font-weight:700;color:#16a34a;"><?= money((float)($agency['total_commission'] ?? 0)) ?></div>
            </div>
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;">
                <div style="font-size:13px;color:#64748b;margin-bottom:6px;">Comissões Pendentes</div>
                <div style="font-size:26px;font-weight:700;color:#f59e0b;"><?= money((float)$pendingTotal) ?></div>
            </div>
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;">
                <div style="font-size:13px;color:#64748b;margin-bottom:6px;">Sua Comissão</div>
                <div style="font-size:26px;font-weight:700;color:#1e293b;"><?= rtrim(rtrim(number_format((float)$agency['commission_rate'], 2), '0'), '.') ?>%</div>
            </div>
        </div>

        <!-- Link de indicação -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;margin-bottom:28px;">
            <h3 style="font-size:16px;color:#1e293b;margin:0 0 6px;">Seu link de indicação</h3>
            <p style="font-size:13px;color:#64748b;margin:0 0 14px;">Compartilhe este link com seus clientes. As vendas feitas por ele geram comissão para a sua agência.</p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <input type="text" id="refLink" value="<?= e($refLink) ?>" readonly style="flex:1;min-width:240px;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;background:#f8fafc;color:#334155;">
                <button type="button" class="btn btn-primary" onclick="copyRefLink()">Copiar link</button>
            </div>
            <p style="font-size:12px;color:#94a3b8;margin:10px 0 0;">Código da agência: <strong><?= e($agency['ref_code']) ?></strong></p>
        </div>

        <!-- Comissões -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;">
            <h3 style="font-size:16px;color:#1e293b;margin:0 0 14px;">Histórico de Comissões</h3>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <thead>
                        <tr style="text-align:left;color:#64748b;border-bottom:1px solid #e2e8f0;">
                            <th style="padding:10px 12px;">Reserva</th>
                            <th style="padding:10px 12px;">Valor da venda</th>
                            <th style="padding:10px 12px;">Comissão</th>
                            <th style="padding:10px 12px;">Status</th>
                            <th style="padding:10px 12px;">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($commissions)): ?>
                        <tr><td colspan="5" style="padding:24px;text-align:center;color:#94a3b8;">Nenhuma comissão ainda. Compartilhe seu link para começar!</td></tr>
                        <?php else: ?>
                        <?php foreach ($commissions as $c): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px 12px;"><?= e($c['booking_number'] ?? '—') ?></td>
                            <td style="padding:10px 12px;"><?= money((float)$c['base_amount']) ?></td>
                            <td style="padding:10px 12px;font-weight:600;color:#16a34a;"><?= money((float)$c['amount']) ?></td>
                            <td style="padding:10px 12px;">
                                <?php
                                    $st = $c['status'] ?? 'pending';
                                    $stLabel = ['pending' => 'Pendente', 'paid' => 'Pago', 'cancelled' => 'Cancelado'][$st] ?? $st;
                                    $stColor = ['pending' => '#f59e0b', 'paid' => '#16a34a', 'cancelled' => '#94a3b8'][$st] ?? '#64748b';
                                ?>
                                <span style="font-size:12px;font-weight:600;color:<?= $stColor ?>;"><?= e($stLabel) ?></span>
                            </td>
                            <td style="padding:10px 12px;color:#64748b;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<script>
function copyRefLink() {
    var input = document.getElementById('refLink');
    input.select();
    input.setSelectionRange(0, 99999);
    try {
        navigator.clipboard.writeText(input.value);
    } catch (e) {
        document.execCommand('copy');
    }
    alert('Link copiado!');
}
</script>
