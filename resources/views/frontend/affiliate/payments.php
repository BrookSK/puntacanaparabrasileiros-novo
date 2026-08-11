<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('affiliate-nav', ['active' => 'pagamentos']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Pagamentos</h1>
                        <p class="aff-page-subtitle">Histórico de todos os pagamentos recebidos</p>
                    </div>
                </div>

                <!-- Summary -->
                <div class="aff-stats-grid aff-stats-grid--3">
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Total Ganho</span>
                        <span class="aff-mini-card-value"><?= money((float)($affiliate['total_earnings'] ?? 0)) ?></span>
                    </div>
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Total Pago</span>
                        <span class="aff-mini-card-value" style="color:var(--success)"><?= money((float)($affiliate['total_paid'] ?? 0)) ?></span>
                    </div>
                    <div class="aff-mini-card">
                        <span class="aff-mini-card-label">Saldo Pendente</span>
                        <span class="aff-mini-card-value" style="color:var(--warning)"><?= money((float)($affiliate['total_earnings'] ?? 0) - (float)($affiliate['total_paid'] ?? 0)) ?></span>
                    </div>
                </div>

                <!-- Table -->
                <div class="aff-card">
                    <div class="aff-table-header">
                        <h3 class="aff-card-title">Histórico de Pagamentos</h3>
                        <span class="aff-table-count"><?= count($payments ?? []) ?> registro<?= count($payments ?? []) !== 1 ? 's' : '' ?></span>
                    </div>
                    <div class="aff-table-wrap">
                        <table class="aff-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Montante</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Referência</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php foreach ($payments as $p): ?>
                                <tr>
                                    <td class="aff-td-id">#<?= (int)$p['id'] ?></td>
                                    <td class="aff-td-amount"><?= money((float)$p['amount']) ?></td>
                                    <td><?= format_datetime($p['paid_at'] ?? $p['created_at']) ?></td>
                                    <td><span class="badge badge-success">Pago</span></td>
                                    <td><?= e($p['payout_reference'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="aff-table-empty">Nenhum pagamento realizado ainda.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Info -->
                <div class="aff-card aff-card--tip">
                    <div class="aff-card-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <h3 class="aff-card-title">Informações de Pagamento</h3>
                    </div>
                    <ul class="aff-tips-list">
                        <li>Pagamentos são realizados mensalmente para comissões acumuladas no mês anterior</li>
                        <li>O método de pagamento configurado é PIX</li>
                        <li>Valor mínimo para saque: $50.00</li>
                        <li>Dúvidas sobre pagamentos? Entre em contato pelo WhatsApp</li>
                    </ul>
                </div>
            </main>
        </div>
    </div>
</section>
