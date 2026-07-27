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
        <h2>Preços por Dia: <?= e($trip['title']) ?></h2>
        <p class="text-muted">Defina regras de preço dinâmico por pacote e categoria de viajante.</p>
    </div>
    <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar" class="btn btn-sm btn-outline">&larr; Voltar ao Passeio</a>
</div>

<?php if (empty($packages)): ?>
<div class="alert alert-info">
    Este passeio ainda não possui pacotes cadastrados. <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar">Crie um pacote primeiro</a>.
</div>
<?php else: ?>

<div class="pricing-info">
    <strong>Prioridade de preço:</strong> Data Específica &gt; Feriado &gt; Dia da Semana &gt; Mensal &gt; Anual &gt; Padrão
</div>

<form method="POST" action="/admin/passeios/<?= (int)$trip['id'] ?>/precos/salvar" class="admin-form">
    <?= csrf_field() ?>

    <?php $ruleIndex = 0; ?>
    <?php foreach ($packages as $pkg): ?>
    <fieldset class="form-section">
        <legend>Pacote: <?= e($pkg['title']) ?></legend>

        <?php if (empty($pkg['categories'])): ?>
        <p class="text-muted">Nenhuma categoria de viajante vinculada a este pacote.</p>
        <?php else: ?>

        <!-- Regras existentes -->
        <div class="pricing-rules-list" id="rules-pkg-<?= (int)$pkg['id'] ?>">
            <?php if (!empty($pkg['day_pricing'])): ?>
            <?php foreach ($pkg['day_pricing'] as $rule): ?>
            <div class="pricing-rule-item card-inner">
                <input type="hidden" name="pricing_rules[<?= $ruleIndex ?>][package_id]" value="<?= (int)$pkg['id'] ?>">
                <div class="form-row">
                    <div class="form-group">
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
                    <div class="form-group">
                        <label>Tipo de Regra</label>
                        <select name="pricing_rules[<?= $ruleIndex ?>][rule_type]" class="form-control rule-type-select">
                            <?php foreach ($ruleTypes as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $rule['rule_type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chave (dia/data)</label>
                        <input type="text" name="pricing_rules[<?= $ruleIndex ?>][day_key]" value="<?= e($rule['day_key']) ?>" class="form-control" placeholder="Ex: 1, 2025-01-01">
                    </div>
                    <div class="form-group">
                        <label>Preço (USD)</label>
                        <input type="number" name="pricing_rules[<?= $ruleIndex ?>][price]" value="<?= e($rule['price']) ?>" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Preço Promo</label>
                        <input type="number" name="pricing_rules[<?= $ruleIndex ?>][sale_price]" value="<?= e($rule['sale_price'] ?? '') ?>" class="form-control" step="0.01" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group flex-2">
                        <label>Label</label>
                        <input type="text" name="pricing_rules[<?= $ruleIndex ?>][label]" value="<?= e($rule['label'] ?? '') ?>" class="form-control" placeholder="Ex: Alta temporada">
                    </div>
                    <div class="form-group" style="align-self: flex-end;">
                        <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
                    </div>
                </div>
            </div>
            <?php $ruleIndex++; endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline add-pricing-rule"
            data-package-id="<?= (int)$pkg['id'] ?>"
            data-categories='<?= e(json_encode($pkg['categories'])) ?>'>
            + Adicionar Regra de Preço
        </button>

        <?php endif; ?>
    </fieldset>
    <?php endforeach; ?>

    <div class="form-actions">
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
        div.className = 'pricing-rule-item card-inner';
        div.innerHTML = `
            <input type="hidden" name="pricing_rules[${ruleIndex}][package_id]" value="${packageId}">
            <div class="form-row">
                <div class="form-group"><label>Categoria</label><select name="pricing_rules[${ruleIndex}][category_id]" class="form-control">${catOptions}</select></div>
                <div class="form-group"><label>Tipo de Regra</label><select name="pricing_rules[${ruleIndex}][rule_type]" class="form-control">${typeOptions}</select></div>
                <div class="form-group"><label>Chave (dia/data)</label><input type="text" name="pricing_rules[${ruleIndex}][day_key]" class="form-control" placeholder="Ex: 1, 2025-01-01"></div>
                <div class="form-group"><label>Preço (USD)</label><input type="number" name="pricing_rules[${ruleIndex}][price]" class="form-control" step="0.01" min="0" required></div>
                <div class="form-group"><label>Preço Promo</label><input type="number" name="pricing_rules[${ruleIndex}][sale_price]" class="form-control" step="0.01" min="0"></div>
            </div>
            <div class="form-row">
                <div class="form-group flex-2"><label>Label</label><input type="text" name="pricing_rules[${ruleIndex}][label]" class="form-control" placeholder="Ex: Alta temporada"></div>
                <div class="form-group" style="align-self: flex-end;"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button></div>
            </div>
        `;
        list.appendChild(div);
        ruleIndex++;
    }
});
</script>

<style>
.pricing-info { background: #f0f8ff; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border-left: 3px solid var(--accent); }
.pricing-rule-item { margin-bottom: 12px; }
</style>
