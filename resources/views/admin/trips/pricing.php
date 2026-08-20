<?php
$ruleTypes = [
    'weekday' => 'Dia da Semana',
    'monthly' => 'Mensal',
    'yearly' => 'Anual',
    'holiday' => 'Feriado',
    'specific_date' => 'Data Específica',
    'default' => 'Padrão',
];
$weekdays = ['0' => 'Domingo', '1' => 'Segunda', '2' => 'Terça', '3' => 'Quarta', '4' => 'Quinta', '5' => 'Sexta', '6' => 'Sábado'];
?>

<div class="card-header">
    <div>
        <h2><?= e($trip['title']) ?></h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Defina regras de preço dinâmico por pacote e categoria de viajante.</p>
    </div>
    <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar" class="btn btn-outline">&larr; Voltar ao Passeio</a>
</div>

<?php if (empty($packages)): ?>
<div class="admin-card">
    <p class="text-muted">Este passeio ainda não possui pacotes cadastrados. <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar">Crie um pacote primeiro</a>.</p>
</div>
<?php else: ?>

<div class="admin-card" style="padding:16px 20px;margin-bottom:20px;background:#f8fafc;">
    <p style="margin:0;font-size:13px;color:#475569;">
        <strong>Prioridade de preço:</strong> Data Específica → Feriado → Dia da Semana → Mensal → Anual → Padrão
    </p>
</div>

<form method="POST" action="/admin/passeios/<?= (int)$trip['id'] ?>/precos" class="admin-form">
    <?= csrf_field() ?>

    <?php $ruleIndex = 0; ?>
    <?php foreach ($packages as $pkg): ?>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-green">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div>
                <h3>Pacote: <?= e($pkg['title']) ?></h3>
                <p class="admin-card-subtitle">Regras de preço para este pacote</p>
            </div>
        </div>

        <?php if (empty($pkg['categories'])): ?>
        <p class="text-muted">Nenhuma categoria de viajante vinculada a este pacote. <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar">Vincule categorias primeiro.</a></p>
        <?php else: ?>

        <div class="pricing-rules-list" id="rules-pkg-<?= (int)$pkg['id'] ?>">
            <?php if (!empty($pkg['day_pricing'])): ?>
            <?php foreach ($pkg['day_pricing'] as $rule): ?>
            <div class="card-inner pricing-rule-item">
                <input type="hidden" name="pricing_rules[<?= $ruleIndex ?>][package_id]" value="<?= (int)$pkg['id'] ?>">
                <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap:14px;">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Categoria</label>
                        <select name="pricing_rules[<?= $ruleIndex ?>][category_id]" class="form-control">
                            <?php foreach ($pkg['categories'] as $cat): ?>
                            <option value="<?= (int)$cat['traveler_category_id'] ?>" <?= (int)$rule['traveler_category_id'] === (int)$cat['traveler_category_id'] ? 'selected' : '' ?>>
                                <?php
                                $catName = '';
                                foreach ($travelerCategories as $tc) {
                                    if ((int)$tc['id'] === (int)$cat['traveler_category_id']) { $catName = $tc['name']; break; }
                                }
                                echo e($catName);
                                ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Tipo de Regra</label>
                        <select name="pricing_rules[<?= $ruleIndex ?>][rule_type]" class="form-control">
                            <?php foreach ($ruleTypes as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $rule['rule_type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Chave (dia/data)</label>
                        <input type="text" name="pricing_rules[<?= $ruleIndex ?>][day_key]" value="<?= e($rule['day_key']) ?>" class="form-control" placeholder="Ex: 1, 01/01">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Preço (USD)</label>
                        <input type="number" name="pricing_rules[<?= $ruleIndex ?>][price]" value="<?= e($rule['price']) ?>" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Preço Promo</label>
                        <input type="number" name="pricing_rules[<?= $ruleIndex ?>][sale_price]" value="<?= e($rule['sale_price'] ?? '') ?>" class="form-control" step="0.01" min="0">
                    </div>
                </div>
                <div class="form-row" style="grid-template-columns: 1fr auto; gap:14px; margin-top:10px;">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Label</label>
                        <input type="text" name="pricing_rules[<?= $ruleIndex ?>][label]" value="<?= e($rule['label'] ?? '') ?>" class="form-control" placeholder="Ex: Alta temporada, Natal">
                    </div>
                    <div class="form-group" style="margin-bottom:0;display:flex;align-items:flex-end;">
                        <button type="button" class="btn btn-sm btn-danger repeater-remove">Remover</button>
                    </div>
                </div>
            </div>
            <?php $ruleIndex++; endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline add-pricing-rule" style="margin-top:12px;"
            data-package-id="<?= (int)$pkg['id'] ?>"
            data-categories='<?= e(json_encode($pkg['categories'])) ?>'>
            + Adicionar Regra de Preço
        </button>

        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div class="form-actions" style="border-top:none;padding-top:0;">
        <button type="submit" class="btn btn-primary">Salvar Regras de Preço</button>
        <a href="/admin/passeios" class="btn btn-outline">Cancelar</a>
    </div>
</form>
<?php endif; ?>

<script>
let ruleIndex = <?= $ruleIndex ?>;
const ruleTypes = <?= json_encode($ruleTypes) ?>;
const travelerCategories = <?= json_encode($travelerCategories) ?>;

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('repeater-remove')) {
        e.target.closest('.pricing-rule-item').remove();
    }

    if (e.target.classList.contains('add-pricing-rule')) {
        const packageId = e.target.dataset.packageId;
        const categories = JSON.parse(e.target.dataset.categories);
        const list = document.getElementById('rules-pkg-' + packageId);

        let catOptions = '';
        categories.forEach(cat => {
            const tc = travelerCategories.find(t => t.id == cat.traveler_category_id);
            if (tc) catOptions += `<option value="${cat.traveler_category_id}">${tc.name}</option>`;
        });

        let typeOptions = '';
        for (const [key, label] of Object.entries(ruleTypes)) {
            typeOptions += `<option value="${key}">${label}</option>`;
        }

        const div = document.createElement('div');
        div.className = 'card-inner pricing-rule-item';
        div.innerHTML = `
            <input type="hidden" name="pricing_rules[${ruleIndex}][package_id]" value="${packageId}">
            <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap:14px;">
                <div class="form-group" style="margin-bottom:0"><label>Categoria</label><select name="pricing_rules[${ruleIndex}][category_id]" class="form-control">${catOptions}</select></div>
                <div class="form-group" style="margin-bottom:0"><label>Tipo de Regra</label><select name="pricing_rules[${ruleIndex}][rule_type]" class="form-control">${typeOptions}</select></div>
                <div class="form-group" style="margin-bottom:0"><label>Chave (dia/data)</label><input type="text" name="pricing_rules[${ruleIndex}][day_key]" class="form-control" placeholder="Ex: 1, 01/01"></div>
                <div class="form-group" style="margin-bottom:0"><label>Preço (USD)</label><input type="number" name="pricing_rules[${ruleIndex}][price]" class="form-control" step="0.01" min="0" required></div>
                <div class="form-group" style="margin-bottom:0"><label>Preço Promo</label><input type="number" name="pricing_rules[${ruleIndex}][sale_price]" class="form-control" step="0.01" min="0"></div>
            </div>
            <div class="form-row" style="grid-template-columns: 1fr auto; gap:14px; margin-top:10px;">
                <div class="form-group" style="margin-bottom:0"><label>Label</label><input type="text" name="pricing_rules[${ruleIndex}][label]" class="form-control" placeholder="Ex: Alta temporada, Natal"></div>
                <div class="form-group" style="margin-bottom:0;display:flex;align-items:flex-end;"><button type="button" class="btn btn-sm btn-danger repeater-remove">Remover</button></div>
            </div>
        `;
        list.appendChild(div);
        ruleIndex++;
    }
});
</script>
