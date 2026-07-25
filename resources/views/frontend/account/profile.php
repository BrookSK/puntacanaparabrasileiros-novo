<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Detalhes da Conta</h2>
        <p class="account-subtitle">Gerencie suas informações pessoais e segurança da conta.</p>

        <!-- Dados Pessoais -->
        <div class="account-card-form">
            <h3 class="account-card-title">Informações Pessoais</h3>
            <form method="POST" action="/minha-conta/perfil">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="first_name" class="form-control" value="<?= e($user['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Sobrenome *</label>
                        <input type="text" name="last_name" class="form-control" value="<?= e($user['last_name']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control form-control-disabled" value="<?= e($user['email']) ?>" disabled>
                    <small class="form-help">O email não pode ser alterado.</small>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Telefone / WhatsApp</label>
                        <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="+55 11 99999-9999">
                    </div>
                    <div class="form-group">
                        <label>País</label>
                        <select name="country" class="form-control">
                            <option value="">Selecione</option>
                            <option value="BR" <?= ($user['country'] ?? '') === 'BR' ? 'selected' : '' ?>>Brasil</option>
                            <option value="US" <?= ($user['country'] ?? '') === 'US' ? 'selected' : '' ?>>Estados Unidos</option>
                            <option value="PT" <?= ($user['country'] ?? '') === 'PT' ? 'selected' : '' ?>>Portugal</option>
                            <option value="AR" <?= ($user['country'] ?? '') === 'AR' ? 'selected' : '' ?>>Argentina</option>
                            <option value="CO" <?= ($user['country'] ?? '') === 'CO' ? 'selected' : '' ?>>Colômbia</option>
                            <option value="DO" <?= ($user['country'] ?? '') === 'DO' ? 'selected' : '' ?>>República Dominicana</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Rua / Avenida</label>
                    <input type="text" name="address" class="form-control" value="<?= e($user['address'] ?? '') ?>" placeholder="Ex: Rua das Flores, 123">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" name="address_complement" class="form-control" value="<?= e($user['address_complement'] ?? '') ?>" placeholder="Apto, bloco, sala...">
                    </div>
                    <div class="form-group">
                        <label>Bairro</label>
                        <input type="text" name="address_neighborhood" class="form-control" value="<?= e($user['address_neighborhood'] ?? '') ?>" placeholder="Seu bairro">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cidade</label>
                        <input type="text" name="city" class="form-control" value="<?= e($user['city'] ?? '') ?>" placeholder="Sua cidade">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <input type="text" name="state" class="form-control" value="<?= e($user['state'] ?? '') ?>" placeholder="Ex: São Paulo">
                    </div>
                </div>
                <div class="form-group">
                    <label>CEP / Código Postal</label>
                    <input type="text" name="zip_code" class="form-control" value="<?= e($user['zip_code'] ?? '') ?>" placeholder="Ex: 01001-000">
                </div>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </form>
        </div>

        <!-- Alterar Senha -->
        <div class="account-card-form" style="margin-top:24px">
            <h3 class="account-card-title">Alterar Senha</h3>
            <form method="POST" action="/minha-conta/perfil/senha">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Senha Atual *</label>
                    <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nova Senha *</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Senha *</label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repita a nova senha" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Alterar Senha</button>
            </form>
        </div>

        <!-- Informações da Conta -->
        <div class="account-card-form account-card-info" style="margin-top:24px">
            <h3 class="account-card-title">Informações da Conta</h3>
            <div class="account-info-grid">
                <div class="account-info-item">
                    <span class="info-label">Membro desde</span>
                    <span class="info-value"><?= format_date($user['created_at'] ?? '') ?></span>
                </div>
                <div class="account-info-item">
                    <span class="info-label">Último login</span>
                    <span class="info-value"><?= $user['last_login_at'] ? format_datetime($user['last_login_at']) : 'Agora' ?></span>
                </div>
                <div class="account-info-item">
                    <span class="info-label">Tipo de conta</span>
                    <span class="info-value"><?= ucfirst($user['role'] ?? 'customer') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
