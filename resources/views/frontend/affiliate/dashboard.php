<!-- Hero -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Painel do Afiliado</h1>
            <p>Aqui você tem uma visão completa das suas indicações, estatísticas de vendas e ganhos acumulados. Gerencie seus links, acompanhe resultados em tempo real e maximize seus lucros com as ferramentas do seu painel de afiliado.</p>
        </div>
    </div>
</section>

<section class="section section-affiliate-panel">
    <div class="container">
        <!-- Tabs de navegação -->
        <?= partial('affiliate-nav', ['active' => 'dashboard']) ?>

        <!-- Dashboard Content -->
        <div class="affiliate-panel-content">
            <!-- Período -->
            <div class="affiliate-period">
                <span class="period-badge">Últimos 30 dias</span>
                <span class="period-range"><?= date('d/m/Y', strtotime('-30 days')) ?> – <?= date('d/m/Y') ?></span>
            </div>

            <!-- Stats Cards -->
            <div class="affiliate-stats-row">
                <div class="affiliate-stat-box">
                    <span class="stat-box-label">Visitas</span>
                    <span class="stat-box-value"><?= (int)($affiliate['total_visits'] ?? 0) ?></span>
                    <a href="/painel-afiliado/visitas" class="stat-box-link">Ver todas as visitas</a>
                </div>
                <div class="affiliate-stat-box">
                    <span class="stat-box-label">Comissões</span>
                    <span class="stat-box-value"><?= (int)($affiliate['total_referrals'] ?? 0) ?></span>
                    <a href="/painel-afiliado/comissoes" class="stat-box-link">Ver todas as comissões</a>
                </div>
                <div class="affiliate-stat-box">
                    <span class="stat-box-label">Ganhos</span>
                    <span class="stat-box-value"><?= money((float)($affiliate['total_earnings'] ?? 0)) ?></span>
                    <a href="/painel-afiliado/comissoes" class="stat-box-link">Ver todas as comissões</a>
                </div>
            </div>

            <!-- Gráfico placeholder -->
            <div class="affiliate-chart-card">
                <div class="chart-header">
                    <div class="chart-legend">
                        <span class="legend-item"><span class="legend-dot legend-blue"></span>Visitas</span>
                        <span class="legend-item"><span class="legend-dot legend-orange"></span>Comissões</span>
                        <span class="legend-item"><span class="legend-dot legend-green"></span>Ganhos</span>
                    </div>
                    <select class="form-control" style="width:auto;font-size:12px;padding:6px 10px">
                        <option>Diária</option>
                        <option>Semanal</option>
                        <option>Mensal</option>
                    </select>
                </div>
                <div class="chart-placeholder">
                    <canvas id="affiliateChart" height="200"></canvas>
                </div>
            </div>

            <!-- Todos os tempos -->
            <h3 class="affiliate-section-title">Todos os tempos</h3>
            <div class="affiliate-stats-row affiliate-stats-4">
                <div class="affiliate-stat-box-sm">
                    <span class="stat-box-label">Visitas</span>
                    <span class="stat-box-value"><?= (int)($affiliate['total_visits'] ?? 0) ?></span>
                </div>
                <div class="affiliate-stat-box-sm">
                    <span class="stat-box-label">Comissões</span>
                    <span class="stat-box-value"><?= (int)($affiliate['total_referrals'] ?? 0) ?></span>
                </div>
                <div class="affiliate-stat-box-sm">
                    <span class="stat-box-label">Ganhos Pagos</span>
                    <span class="stat-box-value"><?= money((float)($affiliate['total_paid'] ?? 0)) ?></span>
                </div>
                <div class="affiliate-stat-box-sm">
                    <span class="stat-box-label">Os Ganhos Não-Pagos</span>
                    <span class="stat-box-value"><?= money((float)($affiliate['total_earnings'] ?? 0) - (float)($affiliate['total_paid'] ?? 0)) ?></span>
                </div>
            </div>

            <!-- Detalhes do programa -->
            <h3 class="affiliate-section-title">Detalhes do programa</h3>
            <div class="affiliate-stats-row">
                <div class="affiliate-stat-box-sm">
                    <span class="stat-box-label">Taxa De Comissão</span>
                    <span class="stat-box-value-sm">Venda taxa: <?= money((float)($affiliate['commission_rate'] ?? 20)) ?>%</span>
                </div>
                <div class="affiliate-stat-box-sm">
                    <span class="stat-box-label">Duração Do Cookie</span>
                    <span class="stat-box-value-sm"><?= (int)($affiliate['cookie_days'] ?? 30) ?> dias</span>
                </div>
            </div>
        </div>
    </div>
</section>
