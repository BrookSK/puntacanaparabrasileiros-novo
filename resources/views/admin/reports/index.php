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

    <!-- País (mapa-múndi) -->
    <div class="admin-card" style="padding:20px;">
        <h3 style="font-size:15px;margin-bottom:16px;">Vendas por País</h3>
        <?php if (empty($countryMapData)): ?>
        <p class="text-muted" style="text-align:center;padding:30px;">Sem dados de país no período. Os países aparecem no mapa conforme as reservas informam o país.</p>
        <?php else: ?>
        <div style="height:280px;"><canvas id="chartCountryMap"></canvas></div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:10px;font-size:12px;color:#64748b;flex-wrap:wrap;">
            <span style="width:14px;height:14px;background:#6ee7b7;border-radius:3px;display:inline-block;"></span> Poucas vendas
            <span style="width:14px;height:14px;background:#047857;border-radius:3px;display:inline-block;margin-left:4px;"></span> Muitas vendas
            <span style="width:14px;height:14px;background:#e5e7eb;border-radius:3px;display:inline-block;margin-left:12px;"></span> Sem vendas
        </div>
        <?php endif; ?>
    </div>
</div>

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
// Tabela de conversão ISO numérico (world-atlas) -> alpha-2 (nossos dados)
const numericToAlpha2 = {
    '076':'BR','840':'US','032':'AR','152':'CL','170':'CO','604':'PE','858':'UY','600':'PY','068':'BO','218':'EC','862':'VE',
    '620':'PT','724':'ES','250':'FR','276':'DE','380':'IT','826':'GB','372':'IE','528':'NL','056':'BE','756':'CH','040':'AT',
    '752':'SE','578':'NO','208':'DK','246':'FI','616':'PL','203':'CZ','348':'HU','300':'GR','642':'RO',
    '124':'CA','484':'MX','630':'PR','214':'DO','388':'JM','192':'CU',
    '036':'AU','554':'NZ','392':'JP','410':'KR','156':'CN','356':'IN','710':'ZA','818':'EG','504':'MA'
};

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

    // País (mapa-múndi choropleth)
    const mapEl = document.getElementById('chartCountryMap');
    if (mapEl) {
        // Mapa: código alpha-2 => {revenue, bookings}
        const salesByCode = {};
        <?php foreach ($countryMapData as $cm): ?>
        salesByCode['<?= e($cm['code']) ?>'] = { revenue: <?= $cm['revenue'] ?>, bookings: <?= $cm['bookings'] ?> };
        <?php endforeach; ?>

        // Resolver a função topojson.feature de onde estiver disponível
        const topojsonFeature = (window.topojson && window.topojson.feature)
            || (window.ChartGeo && window.ChartGeo.topojson && window.ChartGeo.topojson.feature)
            || null;

        if (!topojsonFeature) {
            console.error('[Relatórios] topojson não carregado', {ChartGeo: typeof window.ChartGeo, topojson: typeof window.topojson});
            mapEl.parentElement.innerHTML = '<p style="text-align:center;color:#94a3b8;padding:30px;">Mapa indisponível (biblioteca não carregada).</p>';
            return;
        }

        // TopoJSON embutido (sem fetch externo)
        const worldTopology = <?= !empty($worldTopoJson) ? $worldTopoJson : 'null' ?>;

        function renderMap(topology) {
            try {
                const countries = topojsonFeature(topology, topology.objects.countries).features;

                // Mapeamento de código numérico ISO (do atlas) para alpha-2
                // O world-atlas usa id numérico ISO 3166-1. Usamos o nome como fallback de match.
                const data = countries.map((feature) => {
                    // O world-atlas usa id numérico ISO. Normalizar para 3 dígitos com zero à esquerda.
                    const idPadded = String(feature.id).padStart(3, '0');
                    const iso2 = numericToAlpha2[idPadded] || numericToAlpha2[String(feature.id)] || null;
                    const sale = iso2 ? salesByCode[iso2] : null;
                    return {
                        feature: feature,
                        value: sale ? sale.revenue : 0,
                        bookings: sale ? sale.bookings : 0,
                    };
                });

                // Determinar intensidade de verde por volume (mais vendas = verde mais escuro)
                const maxRevenue = Math.max(1, ...data.map(d => d.value));
                const colorFor = (d) => {
                    if (!d || d.value <= 0) return '#e5e7eb'; // cinza claro (sem vendas)
                    const ratio = d.value / maxRevenue; // 0..1
                    // Verde Punta Cana: mais claro (pouca venda) -> mais escuro (muita venda)
                    // #6ee7b7 (claro) -> #047857 (escuro)
                    const r = Math.round(110 - ratio * 106);   // 110 -> 4
                    const g = Math.round(231 - ratio * 111);   // 231 -> 120
                    const b = Math.round(183 - ratio * 96);    // 183 -> 87
                    return `rgb(${r}, ${g}, ${b})`;
                };

                new Chart(mapEl.getContext('2d'), {
                    type: 'choropleth',
                    data: {
                        labels: countries.map(d => d.properties.name),
                        datasets: [{
                            label: 'Vendas por país',
                            data: data,
                            outline: countries,
                            backgroundColor: (ctx) => colorFor(ctx.raw),
                            borderColor: '#ffffff',
                            borderWidth: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        showOutline: true,
                        showGraticule: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (c) => {
                                        const d = c.raw;
                                        if (!d || d.value <= 0) return d.feature.properties.name + ': sem vendas';
                                        return d.feature.properties.name + ': ' + brl(d.value) + ' (' + d.bookings + ' reserva(s))';
                                    }
                                }
                            }
                        },
                        scales: {
                            projection: { axis: 'x', projection: 'equalEarth' },
                            color: { display: false }
                        }
                    }
                });
            } catch (err) {
                console.error('[Relatórios] Erro ao renderizar mapa:', err);
                mapEl.parentElement.innerHTML = '<p style="text-align:center;color:#94a3b8;padding:30px;">Não foi possível renderizar o mapa.</p>';
            }
        }

        // Usar topojson embutido; se não houver, tentar buscar do servidor
        if (worldTopology) {
            renderMap(worldTopology);
        } else {
            fetch('/assets/data/countries-110m.json')
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(renderMap)
                .catch((err) => {
                    console.error('[Relatórios] Erro ao carregar mapa:', err);
                    mapEl.parentElement.innerHTML = '<p style="text-align:center;color:#94a3b8;padding:30px;">Não foi possível carregar o mapa.</p>';
                });
        }
    }
});
</script>
