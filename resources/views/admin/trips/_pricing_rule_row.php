<?php
/**
 * Linha de regra de preço (tela Preços).
 * Variáveis: $rule, $ri (índice), $pid (pacote), $pkg, $catNames, $ruleTypes, $weekdays, $months, $fmt.
 */
$n = 'pricing_rules[' . $ri . ']';
?>
<div class="card-inner pricing-rule-item">
    <input type="hidden" name="<?= $n ?>[package_id]" value="<?= e((string)$pid) ?>">
    <div class="form-row" style="grid-template-columns: 1.3fr 1.3fr 2.4fr 0.9fr 0.9fr; gap:12px; align-items:end;">
        <div class="form-group" style="margin:0">
            <label>Faixa etária</label>
            <select name="<?= $n ?>[category_id]" class="form-control pr-cat">
                <?php foreach (($pkg['categories'] ?? []) as $cat): $cid = (int)$cat['traveler_category_id']; ?>
                <option value="<?= $cid ?>" <?= (int)$rule['category_id'] === $cid ? 'selected' : '' ?>><?= e($catNames[$cid] ?? ($cat['category_name'] ?? '')) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>Quando</label>
            <select name="<?= $n ?>[rule_type]" class="form-control pr-type">
                <?php foreach ($ruleTypes as $key => $label): ?>
                <option value="<?= $key ?>" <?= $rule['rule_type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>Qual dia</label>
            <div data-for="weekday" class="pr-days">
                <?php foreach ($weekdays as $k => $wn): ?>
                <label class="pr-day"><input type="checkbox" name="<?= $n ?>[weekdays][]" value="<?= $k ?>" <?= in_array((int)$k, (array)$rule['weekdays'], true) ? 'checked' : '' ?>><?= $wn ?></label>
                <?php endforeach; ?>
            </div>
            <div data-for="specific"><input type="date" name="<?= $n ?>[date]" value="<?= e((string)$rule['date']) ?>" class="form-control"></div>
            <div data-for="holiday monthly" style="display:flex;gap:6px;align-items:center;">
                <input type="number" min="1" max="31" name="<?= $n ?>[day]" value="<?= e((string)$rule['day']) ?>" class="form-control" placeholder="Dia" style="max-width:90px;">
                <span data-for="holiday" style="font-size:12px;color:#64748b;">de</span>
            </div>
            <div data-for="holiday annual">
                <select name="<?= $n ?>[month]" class="form-control">
                    <option value="">Mês...</option>
                    <?php foreach ($months as $mk => $mn): ?>
                    <option value="<?= $mk ?>" <?= (int)$rule['month'] === $mk ? 'selected' : '' ?>><?= $mn ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group" style="margin:0">
            <label>Preço (USD)</label>
            <input type="number" step="0.01" min="0" name="<?= $n ?>[price]" value="<?= e($fmt($rule['price'])) ?>" class="form-control" required>
        </div>
        <div class="form-group" style="margin:0">
            <label>Promo</label>
            <input type="number" step="0.01" min="0" name="<?= $n ?>[sale_price]" value="<?= e($fmt($rule['sale_price'])) ?>" class="form-control">
        </div>
    </div>
    <div class="form-row" style="grid-template-columns: 1fr auto; gap:12px; margin-top:8px;">
        <div class="form-group" style="margin:0">
            <input type="text" name="<?= $n ?>[label]" value="<?= e((string)$rule['label']) ?>" class="form-control" placeholder="Nome da regra, só para vocês (ex.: Fim de semana, Natal)">
        </div>
        <div class="form-group" style="margin:0;display:flex;align-items:flex-end;">
            <button type="button" class="btn btn-sm btn-danger repeater-remove">Remover</button>
        </div>
    </div>
</div>
