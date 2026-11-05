<?php
$ruleTypes = [
    'weekday' => 'Dia(s) da semana',
    'specific' => 'Uma data específica',
    'holiday' => 'Data fixa todo ano (feriado)',
    'monthly' => 'Dia do mês (todo mês)',
    'annual' => 'Mês inteiro (todo ano)',
];
$weekdays = ['0' => 'Dom', '1' => 'Seg', '2' => 'Ter', '3' => 'Qua', '4' => 'Qui', '5' => 'Sex', '6' => 'Sáb'];
$months = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$catNames = [];
foreach ($travelerCategories as $tc) {
    $catNames[(int)$tc['id']] = $tc['name'] . (!empty($tc['age_group']) ? ' (' . $tc['age_group'] . ')' : '');
}
$fmt = static fn($v) => $v === null || $v === '' ? '' : rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.');
?>

<div class="card-header">
    <div>
        <h2>Preços — <?= e($trip['title']) ?></h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Preço normal de cada faixa etária e preços diferentes por dia da semana ou data.</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="/admin/passeios/<?= (int)$trip['id'] ?>/disponibilidade" class="btn btn-outline">Dias e Horários</a>
        <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar" class="btn btn-outline">&larr; Voltar ao Passeio</a>
    </div>
</div>

<?php if (empty($packages)): ?>
<div class="admin-card">
    <p class="text-muted">Este passeio ainda não possui pacotes cadastrados. <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar">Crie um pacote primeiro</a>.</p>
</div>
<?php else: ?>

<div class="admin-card" style="padding:14px 18px;margin-bottom:20px;background:#f8fafc;">
    <ul style="margin:0;padding-left:18px;font-size:13px;color:#334155;">
        <li><strong>Preço normal</strong>: vale para todos os dias. Coloque <strong>0</strong> para bebê/criança sem cobrança: o site mostra <strong>Cortesia</strong> e o cliente consegue incluir na reserva.</li>
        <li><strong>Preço diferente por dia</strong>: ex. Coco Bongo mais caro sexta e sábado → adicione uma regra "Dia(s) da semana", marque Sex e Sáb e coloque o preço. Nos outros dias vale o preço normal.</li>
        <li>Se duas regras valerem no mesmo dia, ganha a mais específica: Data específica → Feriado → Dia da semana → Dia do mês → Mês → Preço normal.</li>
        <li>Preço promocional (opcional) aparece riscando o preço cheio.</li>
    </ul>
</div>

<form method="POST" action="/admin/passeios/<?= (int)$trip['id'] ?>/precos" class="admin-form" id="pricingForm">
    <?= csrf_field() ?>

    <?php $ruleIndex = 0; ?>
    <?php foreach ($packages as $pkg): $pid = (int)$pkg['id']; ?>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-green">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div>
                <h3>Pacote: <?= e($pkg['title']) ?></h3>
                <p class="admin-card-subtitle">Preços em dólar (USD)</p>
            </div>
        </div>

        <?php if (empty($pkg['categories'])): ?>
        <p class="text-muted">Nenhuma faixa etária vinculada a este pacote. <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar">Marque as faixas no card "Pacotes" do passeio.</a></p>
        <?php else: ?>

        <h4 style="font-size:14px;margin:4px 0 10px;">Preço normal</h4>
        <table class="table" style="margin-bottom:18px;">
            <thead><tr><th>Faixa etária</th><th style="width:160px;">Preço (USD)</th><th style="width:160px;">Promo (opcional)</th><th>No site</th></tr></thead>
            <tbody>
                <?php foreach ($pkg['categories'] as $cat): $cid = (int)$cat['traveler_category_id']; ?>
                <tr>
                    <td><?= e($catNames[$cid] ?? ($cat['category_name'] ?? '')) ?></td>
                    <td><input type="number" step="0.01" min="0" name="base[<?= $pid ?>][<?= $cid ?>][price]" value="<?= e($fmt($cat['price'])) ?>" class="form-control pr-base" required></td>
                    <td><input type="number" step="0.01" min="0" name="base[<?= $pid ?>][<?= $cid ?>][sale_price]" value="<?= e($fmt($cat['sale_price'] ?? '')) ?>" class="form-control"></td>
                    <td><span class="pr-preview" style="font-weight:600;"></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4 style="font-size:14px;margin:4px 0 10px;">Preços diferentes por dia / data</h4>
        <div class="pricing-rules-list" id="rules-pkg-<?= $pid ?>">
            <?php foreach ($pkg['rules'] as $rule): $ri = $ruleIndex++; ?>
            <?php include __DIR__ . '/_pricing_rule_row.php'; ?>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline add-pricing-rule" style="margin-top:8px;" data-package-id="<?= $pid ?>">
            + Adicionar preço diferente
        </button>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div class="form-actions" style="border-top:none;padding-top:0;">
        <button type="submit" class="btn btn-primary">Salvar preços</button>
        <a href="/admin/passeios" class="btn btn-outline">Cancelar</a>
    </div>
</form>

<!-- Modelo de linha (usado pelo botão "+ Adicionar") -->
<template id="ruleTemplate">
    <?php $rule = ['category_id' => 0, 'rule_type' => 'weekday', 'price' => '', 'sale_price' => '', 'label' => '', 'weekdays' => [], 'date' => '', 'day' => '', 'month' => '']; $ri = '__IDX__'; $pid = '__PKG__'; $pkg = ['categories' => []]; $templateMode = true; ?>
    <?php include __DIR__ . '/_pricing_rule_row.php'; ?>
</template>
<?php endif; ?>

<style>
.pr-days { display:flex; flex-wrap:wrap; gap:6px; }
.pr-day { display:inline-flex; align-items:center; gap:4px; padding:6px 9px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:12px; cursor:pointer; background:#fff; }
.pr-day input { margin:0; }
.pr-day:has(input:checked) { border-color:#1B6F00; background:#f0fdf4; font-weight:600; }
.pricing-rule-item { margin-bottom:10px; }
@media (max-width: 900px) { #pricingForm .form-row { grid-template-columns: 1fr !important; } }
</style>

<script>
(function () {
    var ruleIndex = <?= (int)$ruleIndex ?>;
    var packageCats = <?= json_encode(array_combine(
        array_map(fn($p) => (int)$p['id'], $packages ?: []),
        array_map(fn($p) => array_map(fn($c) => ['id' => (int)$c['traveler_category_id'], 'name' => $catNames[(int)$c['traveler_category_id']] ?? ($c['category_name'] ?? '')], $p['categories'] ?? []), $packages ?: [])
    ) ?: new stdClass(), JSON_UNESCAPED_UNICODE) ?>;

    function syncType(row) {
        var type = row.querySelector('.pr-type').value;
        row.querySelectorAll('[data-for]').forEach(function (el) {
            var show = el.getAttribute('data-for').split(' ').indexOf(type) !== -1;
            el.style.display = show ? '' : 'none';
            el.querySelectorAll('input,select').forEach(function (i) { i.disabled = !show; });
        });
    }

    function preview(input) {
        var row = input.closest('tr');
        var out = row.querySelector('.pr-preview');
        var v = input.value.trim();
        if (v === '') { out.textContent = ''; return; }
        var n = parseFloat(v.replace(',', '.'));
        out.textContent = n > 0 ? '$' + n.toFixed(2) : 'Cortesia';
        out.style.color = n > 0 ? '#1e293b' : '#1B6F00';
    }

    document.querySelectorAll('.pricing-rule-item').forEach(syncType);
    document.querySelectorAll('.pr-base').forEach(function (i) { preview(i); i.addEventListener('input', function () { preview(i); }); });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('pr-type')) syncType(e.target.closest('.pricing-rule-item'));
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('repeater-remove')) {
            e.target.closest('.pricing-rule-item').remove();
        }
        if (e.target.classList.contains('add-pricing-rule')) {
            var pid = e.target.dataset.packageId;
            var tpl = document.getElementById('ruleTemplate').innerHTML
                .replace(/__IDX__/g, String(ruleIndex++))
                .replace(/__PKG__/g, pid);
            var wrap = document.createElement('div');
            wrap.innerHTML = tpl.trim();
            var row = wrap.firstElementChild;
            var sel = row.querySelector('.pr-cat');
            (packageCats[pid] || []).forEach(function (c) {
                var o = document.createElement('option'); o.value = c.id; o.textContent = c.name; sel.appendChild(o);
            });
            document.getElementById('rules-pkg-' + pid).appendChild(row);
            syncType(row);
        }
    });
})();
</script>
