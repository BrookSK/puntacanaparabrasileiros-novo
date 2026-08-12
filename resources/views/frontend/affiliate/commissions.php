<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('affiliate-nav', ['active' => 'comissoes']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Comissões</h1>
                        <p class="aff-page-subtitle">Acompanhe todas as comissões geradas pelas suas indicações</p>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="aff-stats-grid" style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px;">
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Total de Comissões</span>
                        <span class="aff-mini-card-value"><?= (int)($affiliate['total_referrals'] ?? 0) ?></span>
                    </div>
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Ganhos Totais</span>
                        <span class="aff-mini-card-value"><?= money((float)($affiliate['total_earnings'] ?? 0) - ($totalCancelled ?? 0)) ?></span>
                    </div>
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Pagos</span>
                        <span class="aff-mini-card-value" style="color:var(--success)"><?= money((float)($affiliate['total_paid'] ?? 0)) ?></span>
                    </div>
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Pendentes</span>
                        <span class="aff-mini-card-value" style="color:var(--warning)"><?= money((float)($affiliate['total_earnings'] ?? 0) - (float)($affiliate['total_paid'] ?? 0) - ($totalCancelled ?? 0)) ?></span>
                    </div>
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Canceladas</span>
                        <span class="aff-mini-card-value" style="color:var(--danger)"><?= money($totalCancelled ?? 0) ?></span>
                    </div>
                </div>

                <!-- Table -->
                <div class="aff-card">
                    <div class="aff-table-header">
                        <h3 class="aff-card-title">Histórico de Comissões</h3>
                        <span class="aff-table-count"><?= count($commissions ?? []) ?> registro<?= count($commissions ?? []) !== 1 ? 's' : '' ?></span>
                    </div>
                    <div class="aff-table-wrap">
                        <table class="aff-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Montante</th>
                                    <th>Referência</th>
                                    <th>Tipo</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($commissions)): ?>
                                <?php foreach ($commissions as $c): ?>
                                <tr>
                                    <td class="aff-td-id">#<?= (int)$c['id'] ?></td>
                                    <td class="aff-td-amount"><?= money((float)$c['amount']) ?></td>
                                    <td><?= e($c['booking_id'] ?? '-') ?></td>
                                    <td>Venda</td>
                                    <td><?= format_datetime($c['created_at']) ?></td>
                                    <td>
                                        <?php if ($c['status'] === 'paid'): ?>
                                            <span class="badge badge-success">Pago</span>
                                        <?php elseif ($c['status'] === 'approved'): ?>
                                            <span class="badge badge-info">Aprovado</span>
                                        <?php elseif ($c['status'] === 'rejected'): ?>
                                            <span class="badge badge-danger">Cancelada</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c['status'] === 'rejected' && !empty($c['notes'])): ?>
                                            <span style="font-size:12px;color:#64748b;"><?= e($c['notes']) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="aff-table-empty">Nenhuma comissão registrada ainda.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>
