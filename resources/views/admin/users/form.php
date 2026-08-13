<?php
$isEdit = !empty($user);
$action = $isEdit ? '/admin/usuarios/' . $user['id'] . '/editar' : '/admin/usuarios/criar';
$old = flash('old') ?? [];
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Usuário' : 'Novo Usuário' ?></h2>
    <a href="/admin/usuarios" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Usuários
    </a>
</div>

<form method="POST" action="<?= $action ?>" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <div>
            <!-- Dados Pessoais -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <h3>Dados Pessoais</h3>
                        <p class="admin-card-subtitle">Informações do usuário</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Nome <span class="required">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="<?= e($user['first_name'] ?? $old['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group col-6">
                        <label>Sobrenome</label>
                        <input type="text" name="last_name" class="form-control" value="<?= e($user['last_name'] ?? $old['last_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>E-mail <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? $old['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group col-6">
                        <label>Telefone</label>
                        <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? $old['phone'] ?? '') ?>" placeholder="+55 11 99999-9999" data-phone-country>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>País</label>
                        <input type="text" name="country" class="form-control" value="<?= e($user['country'] ?? $old['country'] ?? 'BR') ?>">
                    </div>
                    <div class="form-group col-6">
                        <label><?= $isEdit ? 'Nova Senha (deixe em branco para manter)' : 'Senha *' ?></label>
                        <input type="password" name="password" class="form-control" <?= !$isEdit ? 'required' : '' ?> minlength="6" placeholder="<?= $isEdit ? '••••••••' : 'Mínimo 6 caracteres' ?>">
                    </div>
                </div>
            </div>
        </div>

        <div>
            <!-- Configurações -->
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg>
                    <h3>Configurações</h3>
                </div>

                <div class="summary-card-body">
                    <div class="form-group">
                        <label>Perfil / Role</label>
                        <select name="role" class="form-control">
                            <option value="customer" <?= ($user['role'] ?? $old['role'] ?? 'customer') === 'customer' ? 'selected' : '' ?>>Cliente</option>
                            <option value="affiliate" <?= ($user['role'] ?? $old['role'] ?? '') === 'affiliate' ? 'selected' : '' ?>>Afiliado</option>
                            <option value="admin" <?= ($user['role'] ?? $old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="superadmin" <?= ($user['role'] ?? $old['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                            <option value="banned" <?= ($user['status'] ?? '') === 'banned' ? 'selected' : '' ?>>Banido</option>
                        </select>
                    </div>

                    <?php if ($isEdit): ?>
                    <div class="summary-card-note">
                        <div class="summary-note-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </div>
                        <div>
                            <strong>Informação</strong>
                            <p>Cadastrado em: <?= !empty($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : '-' ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $isEdit ? 'Salvar Alterações' : 'Criar Usuário' ?>
                    </button>
                    <a href="/admin/usuarios" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
