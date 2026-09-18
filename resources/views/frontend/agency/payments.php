<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'pagamentos']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Pagamentos</h1>
                        <p class="aff-page-subtitle">Comissões que já foram pagas à sua agência</p>
                    </div>
                    <div class="aff-period-selector">
                        <span class="aff-period-badge">Total pago: <?= money((float)($agency['total_paid'] ?? 0)) ?></span>
                    </div>
                </div>

                <div class="aff-card">
                    <h3 class="aff-card-title">Histórico de Pagamentos</h3>
                    <div class="aff-table-wrap" style="margin-top:14px;">
                        <table class="aff-table">
                            <thead>
                                <tr><th>Reserva</th><th>Valor</th><th>Referência</th><th>Data do pagamento</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                <tr><td colspan="4" class="aff-table-empty">Nenhum pagamento efetuado ainda.</td></tr>
                                <?php else: ?>
                                <?php foreach ($payments as $p): ?>
                                <tr>
                                    <td><?= e($p['booking_number'] ?? '—') ?></td>
                                    <td class="aff-td-amount" style="color:#16a34a;"><?= money((float)$p['amount']) ?></td>
                                    <td><?= e($p['payout_reference'] ?? '—') ?></td>
                                    <td style="color:#636e72;"><?= !empty($p['paid_at']) ? date('d/m/Y', strtotime($p['paid_at'])) : '—' ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>
