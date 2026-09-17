<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'dashboard']) ?>

            <main class="aff-main">
                <!-- Header -->
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Dashboard</h1>
                        <p class="aff-page-subtitle">Visão geral da sua agência parceira</p>
                    </div>
                    <div class="aff-period-selector">
                        <span class="aff-period-badge">Agência Parceira</span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="aff-stats-grid aff-stats-grid--4">
                    <div class="aff-stat-card">
                        <div class="aff-stat-icon aff-stat-icon--blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        </div>
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= money((float)($agency['total_sales'] ?? 0)) ?></span>
                            <span class="aff-stat-label">Total de Vendas</span>
                        </div>
                    </div>
                    <div class="aff-stat-card">
                        <div class="aff-stat-icon aff-stat-icon--green">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= money((float)($agency['total_commission'] ?? 0)) ?></span>
                            <span class="aff-stat-label">Comissão Total</span>
                        </div>
                    </div>
                    <div class="aff-stat-card">
                        <div class="aff-stat-icon aff-stat-icon--orange">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= money((float)$pendingTotal) ?></span>
                            <span class="aff-stat-label">Comissões Pendentes</span>
                        </div>
                    </div>
                    <div class="aff-stat-card">
                        <div class="aff-stat-icon aff-stat-icon--gray">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        </div>
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= rtrim(rtrim(number_format((float)$agency['commission_rate'], 2), '0'), '.') ?>%</span>
                            <span class="aff-stat-label">Sua Comissão</span>
                        </div>
                    </div>
                </div>

                <!-- Link de indicação -->
                <div class="aff-card" id="link">
                    <h3 class="aff-card-title">Seu link de indicação</h3>
                    <p class="aff-card-desc">Compartilhe este link com seus clientes. As vendas feitas por ele geram comissão para a sua agência.</p>
                    <div class="aff-link-copy-box">
                        <input type="text" id="refLink" class="aff-link-input" value="<?= e($refLink) ?>" readonly>
                        <button type="button" class="btn btn-primary aff-copy-btn" id="copyBtn" onclick="copyRefLink()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            Copiar link
                        </button>
                    </div>
                    <p style="font-size:12px;color:#94a3b8;margin:10px 0 0;">Código da agência: <strong><?= e($agency['ref_code']) ?></strong></p>
                </div>

                <!-- Comissões -->
                <div class="aff-card" id="comissoes">
                    <h3 class="aff-card-title">Histórico de Comissões</h3>
                    <div class="aff-table-wrap" style="margin-top:14px;">
                        <table class="aff-table">
                            <thead>
                                <tr>
                                    <th>Reserva</th>
                                    <th>Valor da venda</th>
                                    <th>Comissão</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
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
                                            $map = ['pending' => ['Pendente', 'badge-warning'], 'paid' => ['Pago', 'badge-success'], 'cancelled' => ['Cancelado', 'badge-secondary']];
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

<script>
function copyRefLink() {
    var input = document.getElementById('refLink');
    var btn = document.getElementById('copyBtn');
    input.select();
    input.setSelectionRange(0, 99999);
    try { navigator.clipboard.writeText(input.value); } catch (e) { document.execCommand('copy'); }
    var original = btn.innerHTML;
    btn.classList.add('btn-copied');
    btn.textContent = 'Copiado!';
    setTimeout(function(){ btn.classList.remove('btn-copied'); btn.innerHTML = original; }, 1800);
}
</script>
