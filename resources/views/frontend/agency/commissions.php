<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'comissoes']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Comissões</h1>
                        <p class="aff-page-subtitle">Todas as comissões geradas pela sua agência</p>
                    </div>
                </div>

                <!-- Resumo -->
                <div class="aff-stats-grid">
                    <div class="aff-stat-card">
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= money((float)($agency['total_commission'] ?? 0)) ?></span>
                            <span class="aff-stat-label">Comissão Total</span>
                        </div>
                    </div>
                    <div class="aff-stat-card">
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= money((float)($agency['total_paid'] ?? 0)) ?></span>
                            <span class="aff-stat-label">Já Pago</span>
                        </div>
                    </div>
                    <div class="aff-stat-card">
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= money((float)$totalCancelled) ?></span>
                            <span class="aff-stat-label">Canceladas</span>
                        </div>
                    </div>
                </div>

                <div class="aff-card">
                    <h3 class="aff-card-title">Histórico de Comissões</h3>
                    <div class="aff-table-wrap" style="margin-top:14px;">
                        <table class="aff-table">
                            <thead>
                                <tr><th>Reserva</th><th>Valor da venda</th><th>Comissão</th><th>Status</th><th>Data</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($commissions)): ?>
                                <tr><td colspan="5" class="aff-table-empty">Nenhuma comissão ainda. Compartilhe seu link para começar!</td></tr>
                                <?php else: ?>
                                <?php foreach ($commissions as $c): ?>
                                <tr>
                                    <td><?= e($c['booking_number'] ?? '—') ?></td>
                                    <td><?= money((float)$c['base_amount']) ?></td>
                                    <td class="aff-td-amount" style="color:#16a34a;"><?= money((float)$c['amount']) ?></td>
                                    <td>
                                        <?php
                                            $st = $c['status'] ?? 'pending';
                                            $map = ['pending' => ['Pendente','badge-warning'], 'paid' => ['Pago','badge-success'], 'cancelled' => ['Cancelado','badge-secondary']];
                                            [$lbl, $cls] = $map[$st] ?? [$st, 'badge-info'];
                                        ?>
                                        <span class="badge <?= $cls ?>"><?= e($lbl) ?></span>
                                    </td>
                                    <td style="color:#636e72;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
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
