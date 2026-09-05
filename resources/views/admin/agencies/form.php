<?php
$isEdit = !empty($agency);
$action = $isEdit ? '/admin/agencias/' . $agency['id'] . '/editar' : '/admin/agencias/criar';
$status = $agency['status'] ?? 'active';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Agência' : 'Nova Agência' ?></h2>
    <a href="/admin/agencias" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Agências
    </a>
</div>

<form method="POST" action="<?= $action ?>" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Principal -->
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                    </div>
                    <div>
                        <h3>Dados da Empresa</h3>
                        <p class="admin-card-subtitle">Informações da agência parceira</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Razão Social <span class="required">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="<?= e($agency['company_name'] ?? '') ?>" placeholder="Ex: Viagens Sol e Mar LTDA" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Nome Fantasia</label>
                        <input type="text" name="trade_name" class="form-control" value="<?= e($agency['trade_name'] ?? '') ?>" placeholder="Ex: Sol e Mar Turismo">
                    </div>
                    <div class="form-group col-6">
                        <label>CNPJ</label>
                        <input type="text" name="cnpj" class="form-control" value="<?= e($agency['cnpj'] ?? '') ?>" placeholder="00.000.000/0000-00">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Responsável / Contato</label>
                        <input type="text" name="contact_name" class="form-control" value="<?= e($agency['contact_name'] ?? '') ?>" placeholder="Nome do responsável">
                    </div>
                    <div class="form-group col-6">
                        <label>Telefone / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($agency['phone'] ?? '') ?>" placeholder="Ex: 5511999999999">
                    </div>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?= e($agency['email'] ?? '') ?>" placeholder="contato@agencia.com">
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Endereço</label>
                        <input type="text" name="address" class="form-control" value="<?= e($agency['address'] ?? '') ?>">
                    </div>
                    <div class="form-group col-6">
                        <label>Cidade</label>
                        <input type="text" name="city" class="form-control" value="<?= e($agency['city'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>País</label>
                    <input type="text" name="country" class="form-control" value="<?= e($agency['country'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Dados Bancários / PIX</label>
                    <textarea name="bank_info" class="form-control" rows="3" placeholder="Banco, agência, conta, PIX..."><?= e($agency['bank_info'] ?? '') ?></textarea>
                    <small class="form-hint">Usado para o repasse das comissões.</small>
                </div>

                <div class="form-group">
                    <label>Observações internas</label>
                    <textarea name="notes" class="form-control" rows="2"><?= e($agency['notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div>
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg>
                    <h3>Comissão e Acesso</h3>
                </div>

                <div class="summary-card-body">
                    <div class="form-group">
                        <label>Taxa de Comissão (%) <span class="required">*</span></label>
                        <input type="number" name="commission_rate" class="form-control" value="<?= e($agency['commission_rate'] ?? '10') ?>" step="0.01" min="0" max="100" required>
                        <small class="form-hint">Percentual sobre o total de cada venda atribuída à agência.</small>
                    </div>

                    <div class="form-group">
                        <label>Código de Indicação <span class="required">*</span></label>
                        <input type="text" name="ref_code" class="form-control" value="<?= e($agency['ref_code'] ?? $suggestedRef ?? '') ?>" style="text-transform:uppercase;">
                        <small class="form-hint">Usado no link de indicação: <code>?ag=CÓDIGO</code></small>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Ativa</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inativa</option>
                        </select>
                        <small class="form-hint">Agências inativas não recebem novas comissões.</small>
                    </div>
                </div>

                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $isEdit ? 'Salvar Alterações' : 'Cadastrar Agência' ?>
                    </button>
                    <a href="/admin/agencias" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
