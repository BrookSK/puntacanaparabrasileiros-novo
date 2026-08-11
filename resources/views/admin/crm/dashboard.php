<div class="admin-page-header">
    <h2>CRM — Dashboard</h2>
    <div>
        <a href="/crm" class="btn btn-outline btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg> Boards</a>
        <a href="/crm/commissions" class="btn btn-outline btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg> Comissões</a>
    </div>
</div>

<!-- Contadores -->
<div class="stats-grid stats-5">
    <div class="stat-card"><div class="stat-value"><?= $stats['total'] ?></div><div class="stat-label">Leads no CRM</div></div>
    <div class="stat-card stat-purple"><div class="stat-value"><?= $stats['withLabel'] ?></div><div class="stat-label">Com Etiqueta</div></div>
    <div class="stat-card stat-warning"><div class="stat-value"><?= $stats['open'] ?></div><div class="stat-label">Em Aberto</div></div>
    <div class="stat-card stat-success"><div class="stat-value"><?= $stats['converted'] ?></div><div class="stat-label">Convertidos</div></div>
    <div class="stat-card stat-danger"><div class="stat-value"><?= $stats['lost'] ?></div><div class="stat-label">Perdidos</div></div>
</div>

<!-- Valores -->
<div class="stats-grid stats-5" style="margin-top:16px;">
    <div class="stat-card"><div class="stat-value">R$ <?= number_format($stats['valueTotal'], 2, ',', '.') ?></div><div class="stat-label">Valor Cotado</div></div>
    <div class="stat-card stat-success"><div class="stat-value">R$ <?= number_format($stats['valueConverted'], 2, ',', '.') ?></div><div class="stat-label">Valor Convertido</div></div>
    <div class="stat-card stat-danger"><div class="stat-value">R$ <?= number_format($stats['valueLost'], 2, ',', '.') ?></div><div class="stat-label">Valor Perdido</div></div>
    <div class="stat-card stat-purple"><div class="stat-value">R$ <?= number_format($stats['valueRecovery'], 2, ',', '.') ?></div><div class="stat-label">Recuperação/Agendado</div></div>
    <div class="stat-card stat-info"><div class="stat-value">R$ <?= number_format($stats['ticketMedio'], 2, ',', '.') ?></div><div class="stat-label">Ticket Médio</div></div>
</div>

<!-- Gráficos -->
<div class="dashboard-charts" style="margin-top:24px;">
    <div class="admin-card chart-pie-wrapper">
        <h4>Distribuição</h4>
        <canvas id="chartPie" width="300" height="300"></canvas>
    </div>
    <div class="admin-card chart-line-wrapper">
        <h4>Evolução (6 meses)</h4>
        <canvas id="chartLine" width="600" height="300"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
// Pie Chart
new Chart(document.getElementById('chartPie'), {
    type: 'pie',
    data: {
        labels: ['Em Aberto', 'Convertidos', 'Perdidos'],
        datasets: [{
            data: [<?= $stats['open'] ?>, <?= $stats['converted'] ?>, <?= $stats['lost'] ?>],
            backgroundColor: ['#ff9800', '#2e7d32', '#c62828'],
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Line Chart
const evolution = <?= json_encode($evolution) ?>;
const months = evolution.map(e => e.month);
const converted = evolution.map(e => parseInt(e.converted));
const lost = evolution.map(e => parseInt(e.lost));

new Chart(document.getElementById('chartLine'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            { label: 'Convertidos', data: converted, borderColor: '#2e7d32', backgroundColor: 'rgba(46,125,50,0.1)', fill: true, tension: 0.3 },
            { label: 'Perdidos', data: lost, borderColor: '#c62828', backgroundColor: 'rgba(198,40,40,0.1)', fill: true, tension: 0.3 },
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
</script>
