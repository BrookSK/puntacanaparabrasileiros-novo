<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('affiliate-nav', ['active' => 'dashboard']) ?>

            <main class="aff-main">
                <!-- Header -->
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Dashboard</h1>
                        <p class="aff-page-subtitle">Visão geral do seu desempenho como afiliado</p>
                    </div>
                    <div class="aff-period-selector">
                        <span class="aff-period-badge">Últimos 30 dias</span>
                        <span class="aff-period-range"><?= date('d \d\e M', strtotime('-30 days')) ?> - <?= date('d \d\e M, Y') ?></span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="aff-stats-grid">
                    <div class="aff-stat-card">
                        <div class="aff-stat-icon aff-stat-icon--blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= number_format((int)($affiliate['total_visits'] ?? 0)) ?></span>
                            <span class="aff-stat-label">Visitas</span>
                        </div>
                        <a href="/painel-afiliado/visitas" class="aff-stat-action">Ver todas</a>
                    </div>
                    <div class="aff-stat-card">
                        <div class="aff-stat-icon aff-stat-icon--orange">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= (int)($affiliate['total_referrals'] ?? 0) ?></span>
                            <span class="aff-stat-label">Comissões</span>
                        </div>
                        <a href="/painel-afiliado/comissoes" class="aff-stat-action">Ver todas</a>
                    </div>
                    <div class="aff-stat-card">
                        <div class="aff-stat-icon aff-stat-icon--green">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        </div>
                        <div class="aff-stat-content">
                            <span class="aff-stat-value"><?= money((float)($affiliate['total_earnings'] ?? 0)) ?></span>
                            <span class="aff-stat-label">Ganhos Totais</span>
                        </div>
                        <a href="/painel-afiliado/comissoes" class="aff-stat-action">Detalhes</a>
                    </div>
                </div>

                <!-- Chart -->
                <div class="aff-chart-card">
                    <div class="aff-chart-header">
                        <h3 class="aff-card-title">Desempenho</h3>
                        <div class="aff-chart-legend">
                            <span class="aff-legend-item"><span class="aff-legend-dot" style="background:#3b82f6"></span>Visitas</span>
                            <span class="aff-legend-item"><span class="aff-legend-dot" style="background:#f59e0b"></span>Comissões</span>
                            <span class="aff-legend-item"><span class="aff-legend-dot" style="background:#10b981"></span>Ganhos</span>
                        </div>
                    </div>
                    <div class="aff-chart-body">
                        <canvas id="affiliateChart" height="220"></canvas>
                    </div>
                </div>

                <!-- Bottom Grid -->
                <div class="aff-bottom-grid">
                    <!-- All Time Stats -->
                    <div class="aff-card">
                        <h3 class="aff-card-title">Todos os tempos</h3>
                        <div class="aff-mini-stats">
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= number_format((int)($affiliate['total_visits'] ?? 0)) ?></span>
                                <span class="aff-mini-stat-label">Visitas</span>
                            </div>
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= (int)($affiliate['total_referrals'] ?? 0) ?></span>
                                <span class="aff-mini-stat-label">Comissões</span>
                            </div>
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= money((float)($affiliate['total_paid'] ?? 0)) ?></span>
                                <span class="aff-mini-stat-label">Ganhos Pagos</span>
                            </div>
                            <div class="aff-mini-stat">
                                <span class="aff-mini-stat-value"><?= money((float)($affiliate['total_earnings'] ?? 0) - (float)($affiliate['total_paid'] ?? 0)) ?></span>
                                <span class="aff-mini-stat-label">Não-Pagos</span>
                            </div>
                        </div>
                    </div>

                    <!-- Program Details -->
                    <div class="aff-card">
                        <h3 class="aff-card-title">Detalhes do Programa</h3>
                        <div class="aff-program-details">
                            <div class="aff-program-item">
                                <span class="aff-program-label">Taxa de Comissão</span>
                                <span class="aff-program-value"><?= number_format((float)($affiliate['commission_rate'] ?? 20), 0) ?>%</span>
                            </div>
                            <div class="aff-program-item">
                                <span class="aff-program-label">Duração do Cookie</span>
                                <span class="aff-program-value"><?= (int)($affiliate['cookie_days'] ?? 30) ?> dias</span>
                            </div>
                            <div class="aff-program-item">
                                <span class="aff-program-label">Método de Pagamento</span>
                                <span class="aff-program-value">PIX</span>
                            </div>
                            <div class="aff-program-item">
                                <span class="aff-program-label">Status</span>
                                <span class="badge badge-success">Ativo</span>
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
    var ctx = document.getElementById('affiliateChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels ?? []) ?>,
            datasets: [
                { label: 'Visitas', data: <?= json_encode($chartVisits ?? array_fill(0, 30, 0)) ?>, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.05)', tension: 0.4, fill: true },
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
