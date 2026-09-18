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

                <!-- Gráfico de Desempenho -->
                <div class="aff-chart-card">
                    <div class="aff-chart-header">
                        <h3 class="aff-card-title">Desempenho</h3>
                        <div class="aff-chart-legend">
                            <span class="aff-legend-item"><span class="aff-legend-dot" style="background:#f59e0b"></span>Comissões</span>
                            <span class="aff-legend-item"><span class="aff-legend-dot" style="background:#10b981"></span>Ganhos</span>
                        </div>
                    </div>
                    <div class="aff-chart-body">
                        <canvas id="agencyChart" height="220"></canvas>
                    </div>
                </div>

                <!-- Todos os tempos + Detalhes do Programa -->
                <div class="aff-bottom-grid">
                    <div class="aff-card">
                        <h3 class="aff-card-title">Todos os tempos</h3>
                        <div class="aff-mini-stats">
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= (int)($totalCommissionsCount ?? 0) ?></span>
                                <span class="aff-mini-stat-label">Comissões</span>
                            </div>
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= money((float)($agency['total_sales'] ?? 0)) ?></span>
                                <span class="aff-mini-stat-label">Total em Vendas</span>
                            </div>
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= money((float)($agency['total_paid'] ?? 0)) ?></span>
                                <span class="aff-mini-stat-label">Ganhos Pagos</span>
                            </div>
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= money((float)($agency['total_commission'] ?? 0) - (float)($agency['total_paid'] ?? 0)) ?></span>
                                <span class="aff-mini-stat-label">Não-Pagos</span>
                            </div>
                        </div>
                    </div>

                    <div class="aff-card">
                        <h3 class="aff-card-title">Detalhes da Parceria</h3>
                        <div class="aff-program-details">
                            <div class="aff-program-item">
                                <span class="aff-program-label">Taxa de Comissão</span>
                                <span class="aff-program-value"><?= rtrim(rtrim(number_format((float)($agency['commission_rate'] ?? 0), 2), '0'), '.') ?>%</span>
                            </div>
                            <div class="aff-program-item">
                                <span class="aff-program-label">Código da Agência</span>
                                <span class="aff-program-value"><?= e($agency['ref_code']) ?></span>
                            </div>
                            <div class="aff-program-item">
                                <span class="aff-program-label">Método de Pagamento</span>
                                <span class="aff-program-value">PIX / Transferência</span>
                            </div>
                            <div class="aff-program-item">
                                <span class="aff-program-label">Status</span>
                                <span class="badge badge-success"><?= ($agency['status'] ?? '') === 'active' ? 'Ativa' : 'Inativa' ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('agencyChart');
    if (!ctx || typeof Chart === 'undefined') return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels ?? []) ?>,
            datasets: [
                { label: 'Comissões', data: <?= json_encode($chartCommissions ?? array_fill(0, 30, 0)) ?>, borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.05)', tension: 0.4, fill: true },
                { label: 'Ganhos', data: <?= json_encode($chartEarnings ?? array_fill(0, 30, 0)) ?>, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.05)', tension: 0.4, fill: true }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 8 } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
});
</script>
