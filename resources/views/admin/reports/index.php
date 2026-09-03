<?php
// Preparar dados para os gráficos (JSON)
$timelineLabels = array_map(fn($r) => date('d/m', strtotime($r['date'])), $timeline);
$timelineRevenue = array_map(fn($r) => (float) $r['revenue'], $timeline);
$timelineBookings = array_map(fn($r) => (int) $r['bookings'], $timeline);

// Dados de país para o mapa: {code: 'BR', revenue: 123, bookings: 4}
$countryMapData = [];
foreach ($byCountry as $r) {
    $code = strtoupper(trim($r['country']));
    // Ignorar "Não informado" e códigos inválidos no mapa
    if (strlen($code) === 2) {
        $countryMapData[] = [
            'code' => $code,
            'revenue' => (float) $r['revenue'],
            'bookings' => (int) $r['bookings'],
        ];
    }
}

$originLabels = array_map(fn($r) => $r['origin'], $byOrigin);
$originData = array_map(fn($r) => (float) $r['revenue'], $byOrigin);
?>

<div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
    <div>
        <h2>Relatórios</h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Visão geral de vendas, origem dos clientes e campanhas.</p>
    </div>
    <form method="GET" action="/admin/relatorios" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:2px;">De</label>
            <input type="date" name="from" value="<?= e($from) ?>" class="form-control" style="padding:8px;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:2px;">Até</label>
            <input type="date" name="to" value="<?= e($to) ?>" class="form-control" style="padding:8px;">
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
</div>

<!-- KPIs -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    <div class="admin-card" style="padding:20px;">
        <div style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;">Total de Reservas</div>
        <div style="font-size:28px;font-weight:700;color:#1e293b;margin-top:6px;"><?= (int) $summary['total_bookings'] ?></div>
    </div>
    <div class="admin-card" style="padding:20px;">
        <div style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;">Receita Total</div>
        <div style="font-size:28px;font-weight:700;color:#059669;margin-top:6px;"><?= money((float) $summary['total_revenue']) ?></div>
    </div>
    <div class="admin-card" style="padding:20px;">
        <div style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;">Recebido</div>
        <div style="font-size:28px;font-weight:700;color:#2563eb;margin-top:6px;"><?= money((float) $summary['total_paid']) ?></div>
    </div>
    <div class="admin-card" style="padding:20px;">
        <div style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;">Ticket Médio</div>
        <div style="font-size:28px;font-weight:700;color:#7c3aed;margin-top:6px;"><?= money((float) $summary['avg_ticket']) ?></div>
    </div>
</div>

<!-- Volume de vendas (linha) -->
<div class="admin-card" style="padding:20px;margin-bottom:20px;">
    <h3 style="font-size:15px;margin-bottom:16px;">Volume de Vendas no Período</h3>
    <?php if (empty($timeline)): ?>
    <p class="text-muted" style="text-align:center;padding:30px;">Sem dados no período selecionado.</p>
    <?php else: ?>
    <div style="height:280px;"><canvas id="chartTimeline"></canvas></div>
    <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <!-- Origem dos clientes (pizza) -->
    <div class="admin-card" style="padding:20px;">
        <h3 style="font-size:15px;margin-bottom:16px;">Origem dos Clientes</h3>
        <?php if (empty($byOrigin)): ?>
        <p class="text-muted" style="text-align:center;padding:30px;">Sem dados.</p>
        <?php else: ?>
        <div style="height:260px;"><canvas id="chartOrigin"></canvas></div>
        <?php endif; ?>
    </div>

    <!-- País (mapa-múndi SVG inline) -->
    <div class="admin-card" style="padding:20px;">
        <h3 style="font-size:15px;margin-bottom:16px;">Vendas por País</h3>
        <?php if (empty($countryMapData)): ?>
        <p class="text-muted" style="text-align:center;padding:30px;">Sem dados de país no período. Os países aparecem no mapa conforme as reservas informam o país.</p>
        <?php else: ?>
        <div id="worldMapWrap" style="width:100%;">
            <?php
            $svgFile = BASE_PATH . '/resources/views/components/world-map.svg';
            if (is_file($svgFile)) {
                echo file_get_contents($svgFile);
            } else {
                echo '<p class="text-muted" style="text-align:center;padding:30px;">Mapa não disponível.</p>';
            }
            ?>
        </div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:12px;font-size:12px;color:#64748b;flex-wrap:wrap;">
            <span style="width:14px;height:14px;background:#a7f3d0;border-radius:3px;display:inline-block;"></span> Poucas vendas
            <span style="width:14px;height:14px;background:#047857;border-radius:3px;display:inline-block;margin-left:4px;"></span> Muitas vendas
            <span style="width:14px;height:14px;background:#e5e7eb;border-radius:3px;display:inline-block;margin-left:12px;"></span> Sem vendas
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
#worldMapWrap svg { width: 100%; height: auto; max-height: 320px; }
#worldMapWrap svg path { fill: #e5e7eb; stroke: #fff; stroke-width: 0.5; transition: fill .2s; cursor: default; }
#worldMapWrap svg path.has-sales { cursor: pointer; }
</style>

<!-- Tabelas -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <!-- Cidades -->
    <div class="admin-card" style="padding:20px;">
        <h3 style="font-size:15px;margin-bottom:12px;">Cidades dos Clientes</h3>
        <table class="table" style="width:100%;">
            <thead><tr><th>Cidade</th><th>País</th><th style="text-align:center;">Reservas</th><th style="text-align:right;">Receita</th></tr></thead>
            <tbody>
                <?php if (empty($byCity)): ?>
                <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Sem dados.</td></tr>
                <?php else: foreach ($byCity as $c): ?>
                <tr>
                    <td><?= e($c['city']) ?></td>
                    <td><?= e($c['country'] ?: '—') ?></td>
                    <td style="text-align:center;"><?= (int) $c['bookings'] ?></td>
                    <td style="text-align:right;font-weight:600;"><?= money((float) $c['revenue']) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Passeios mais vendidos -->
    <div class="admin-card" style="padding:20px;">
        <h3 style="font-size:15px;margin-bottom:12px;">Passeios Mais Vendidos</h3>
        <table class="table" style="width:100%;">
            <thead><tr><th>Passeio</th><th style="text-align:center;">Vendas</th><th style="text-align:right;">Receita</th></tr></thead>
            <tbody>
                <?php if (empty($topTrips)): ?>
                <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:20px;">Sem dados.</td></tr>
                <?php else: foreach ($topTrips as $t): ?>
                <tr>
                    <td><?= e($t['title']) ?></td>
                    <td style="text-align:center;"><?= (int) $t['sales'] ?></td>
                    <td style="text-align:right;font-weight:600;"><?= money((float) $t['revenue']) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Campanhas de tráfego pago -->
<div class="admin-card" style="padding:20px;margin-bottom:20px;">
    <h3 style="font-size:15px;margin-bottom:4px;">Campanhas de Tráfego Pago</h3>
    <p class="text-muted" style="font-size:12px;margin-bottom:14px;">Vendas atribuídas a links com parâmetros UTM (Meta Ads, Google Ads, etc.). Use links como <code>?utm_source=facebook&utm_medium=cpc&utm_campaign=verao2026</code> nos seus anúncios.</p>
    <table class="table" style="width:100%;">
        <thead><tr><th>Origem</th><th>Mídia</th><th>Campanha</th><th style="text-align:center;">Reservas</th><th style="text-align:right;">Receita</th></tr></thead>
        <tbody>
            <?php if (empty($byCampaign)): ?>
            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:20px;">Nenhuma venda de campanha rastreada no período. Configure links com parâmetros UTM nos seus anúncios.</td></tr>
            <?php else: foreach ($byCampaign as $c): ?>
            <tr>
                <td><?= e($c['source']) ?></td>
                <td><?= e($c['medium']) ?></td>
                <td><?= e($c['campaign']) ?></td>
                <td style="text-align:center;"><?= (int) $c['bookings'] ?></td>
                <td style="text-align:right;font-weight:600;"><?= money((float) $c['revenue']) ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Métodos de pagamento -->
<div class="admin-card" style="padding:20px;">
    <h3 style="font-size:15px;margin-bottom:12px;">Vendas por Método de Pagamento</h3>
    <table class="table" style="width:100%;">
        <thead><tr><th>Método</th><th style="text-align:center;">Pagamentos</th><th style="text-align:right;">Total Recebido</th></tr></thead>
        <tbody>
            <?php
            $gatewayLabels = ['paypal' => 'PayPal', 'stripe' => 'Cartão (Stripe)', 'pix' => 'PIX', 'pagbank' => 'PagBank', 'manual' => 'Manual', 'free' => 'Cortesia', 'simulate' => 'Simulado'];
            if (empty($byGateway)): ?>
            <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:20px;">Sem pagamentos concluídos no período.</td></tr>
            <?php else: foreach ($byGateway as $g): ?>
            <tr>
                <td><?= e($gatewayLabels[$g['gateway']] ?? $g['gateway']) ?></td>
                <td style="text-align:center;"><?= (int) $g['payments'] ?></td>
                <td style="text-align:right;font-weight:600;"><?= money((float) $g['total']) ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    const brl = (v) => '$' + Number(v).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const palette = ['#2563eb','#059669','#7c3aed','#db2777','#ea580c','#0891b2','#ca8a04','#dc2626'];

    // Timeline
    const tEl = document.getElementById('chartTimeline');
    if (tEl) {
        new Chart(tEl, {
            type: 'line',
            data: {
                labels: <?= json_encode($timelineLabels) ?>,
                datasets: [
                    {
                        label: 'Receita ($)',
                        data: <?= json_encode($timelineRevenue) ?>,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5,150,105,0.1)',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Reservas',
                        data: <?= json_encode($timelineBookings) ?>,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        tension: 0.3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { type: 'linear', position: 'left', title: { display: true, text: 'Receita ($)' } },
                    y1: { type: 'linear', position: 'right', title: { display: true, text: 'Reservas' }, grid: { drawOnChartArea: false }, ticks: { precision: 0 } }
                }
            }
        });
    }

    // Origem (pizza)
    const oEl = document.getElementById('chartOrigin');
    if (oEl) {
        new Chart(oEl, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($originLabels) ?>,
                datasets: [{ data: <?= json_encode($originData) ?>, backgroundColor: palette }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: (c) => c.label + ': ' + brl(c.raw) } } }
            }
        });
    }

    // País (mapa-múndi SVG inline) — pinta os países que venderam
    const mapWrap = document.getElementById('worldMapWrap');
    if (mapWrap) {
        const salesByCode = {};
        let maxRev = 1;
        <?php foreach ($countryMapData as $cm): ?>
        salesByCode['<?= strtolower(e($cm['code'])) ?>'] = { revenue: <?= $cm['revenue'] ?>, bookings: <?= $cm['bookings'] ?> };
        <?php if ($cm['revenue'] > 0): ?>if (<?= $cm['revenue'] ?> > maxRev) maxRev = <?= $cm['revenue'] ?>;<?php endif; ?>
        <?php endforeach; ?>

        const greenFor = (revenue) => {
            const ratio = Math.min(1, revenue / maxRev);
            // interpolar #a7f3d0 (claro) -> #047857 (escuro)
            const r = Math.round(167 - ratio * 163); // 167 -> 4
            const g = Math.round(243 - ratio * 123); // 243 -> 120
            const b = Math.round(208 - ratio * 121); // 208 -> 87
            return `rgb(${r}, ${g}, ${b})`;
        };

        Object.keys(salesByCode).forEach((code) => {
            // No SVG os países têm id em minúsculo (ex: "br")
            const el = mapWrap.querySelector('#' + CSS.escape(code));
            const targets = el ? [el] : [];
            // Alguns países são grupos <g> com vários paths
            targets.forEach((t) => {
                const paths = t.tagName.toLowerCase() === 'path' ? [t] : t.querySelectorAll('path');
                const nodes = paths.length ? paths : [t];
                nodes.forEach((p) => {
                    p.style.fill = greenFor(salesByCode[code].revenue);
                    p.classList.add('has-sales');
                    const rev = salesByCode[code].revenue;
                    const bk = salesByCode[code].bookings;
                    p.setAttribute('data-tooltip', code.toUpperCase() + ': $' + rev.toFixed(2) + ' (' + bk + ' reserva(s))');
                    // Tooltip nativo
                    let title = p.querySelector('title');
                    if (!title) { title = document.createElementNS('http://www.w3.org/2000/svg', 'title'); p.appendChild(title); }
                    title.textContent = code.toUpperCase() + ': $' + rev.toFixed(2) + ' (' + bk + ' reserva(s))';
                });
            });
        });
    }
});
</script>
