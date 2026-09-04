<?php
$isEdit = !empty($coupon);
$action = $isEdit ? '/admin/cupons/' . $coupon['id'] . '/editar' : '/admin/cupons/criar';
$type = $coupon['type'] ?? 'percentage';
$activeChecked = $isEdit ? ((int)($coupon['active'] ?? 0) === 1) : true;
$fmtDate = fn($v) => !empty($v) ? date('Y-m-d\TH:i', strtotime($v)) : '';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Cupom' : 'Novo Cupom' ?></h2>
    <a href="/admin/cupons" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Cupons
    </a>
</div>

<form method="POST" action="<?= $action ?>" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Principal -->
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
                    </div>
                    <div>
                        <h3>Dados do Cupom</h3>
                        <p class="admin-card-subtitle">Código e regras do desconto</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Código do Cupom <span class="required">*</span></label>
                    <input type="text" name="code" class="form-control" value="<?= e($coupon['code'] ?? '') ?>" placeholder="Ex: VERAO10" style="text-transform:uppercase;" required>
                    <small class="form-hint">Letras, números, hífen ou underscore. É o que o cliente digita no checkout.</small>
                </div>

                <div class="form-group">
                    <label>Descrição (interna)</label>
                    <input type="text" name="description" class="form-control" value="<?= e($coupon['description'] ?? '') ?>" placeholder="Ex: Campanha de verão nas redes sociais">
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Tipo de Desconto <span class="required">*</span></label>
                        <select name="type" class="form-control" id="couponType">
                            <option value="percentage" <?= $type === 'percentage' ? 'selected' : '' ?>>Percentual (%)</option>
                            <option value="fixed" <?= $type === 'fixed' ? 'selected' : '' ?>>Valor fixo (US$)</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label>Valor <span class="required">*</span></label>
                        <input type="number" name="value" class="form-control" value="<?= e($coupon['value'] ?? '') ?>" step="0.01" min="0" placeholder="Ex: 10" required>
                        <small class="form-hint" id="valueHint"><?= $type === 'percentage' ? 'Percentual de 0 a 100.' : 'Valor em dólares.' ?></small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Vincular a um Afiliado</label>
                    <select name="affiliate_id" class="form-control">
                        <option value="0">— Cupom geral (sem afiliado) —</option>
                        <?php foreach ($affiliates as $aff): ?>
                        <option value="<?= (int)$aff['id'] ?>" <?= (int)($coupon['affiliate_id'] ?? 0) === (int)$aff['id'] ? 'selected' : '' ?>>
                            <?= e(trim($aff['first_name'] . ' ' . $aff['last_name'])) ?> (<?= e($aff['email']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint">Se vincular, a venda com este cupom gera comissão para o afiliado escolhido.</small>
                </div>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div>
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg>
                    <h3>Regras e Validade</h3>
                </div>

                <div class="summary-card-body">
                    <div class="form-group">
                        <label>Pedido mínimo (US$)</label>
                        <input type="number" name="min_order" class="form-control" value="<?= e($coupon['min_order'] ?? '') ?>" step="0.01" min="0" placeholder="Opcional">
                        <small class="form-hint">Deixe vazio para não exigir mínimo.</small>
                    </div>

                    <div class="form-group">
                        <label>Limite de usos</label>
                        <input type="number" name="max_uses" class="form-control" value="<?= e($coupon['max_uses'] ?? '') ?>" min="1" placeholder="Ilimitado">
                        <small class="form-hint">Quantas vezes o cupom pode ser usado no total.</small>
                    </div>

                    <div class="form-group">
                        <label>Início da validade</label>
                        <input type="datetime-local" name="starts_at" class="form-control" value="<?= $fmtDate($coupon['starts_at'] ?? null) ?>">
                    </div>

                    <div class="form-group">
                        <label>Expira em</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="<?= $fmtDate($coupon['expires_at'] ?? null) ?>">
                        <small class="form-hint">Deixe vazio para não expirar.</small>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                            <input type="checkbox" name="active" value="1" <?= $activeChecked ? 'checked' : '' ?>>
                            Cupom ativo
                        </label>
                    </div>

                    <?php if ($isEdit): ?>
                    <div class="form-group">
                        <small class="form-hint">Usos até agora: <strong><?= (int)($coupon['used_count'] ?? 0) ?></strong></small>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $isEdit ? 'Salvar Alterações' : 'Criar Cupom' ?>
                    </button>
                    <a href="/admin/cupons" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function(){
    var typeSel = document.getElementById('couponType');
    var hint = document.getElementById('valueHint');
    if (typeSel && hint) {
        typeSel.addEventListener('change', function(){
            hint.textContent = this.value === 'percentage' ? 'Percentual de 0 a 100.' : 'Valor em dólares.';
        });
    }
})();
</script>
