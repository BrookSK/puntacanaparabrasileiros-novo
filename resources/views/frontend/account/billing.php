<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Informações de Cobrança</h2>
        <p class="account-subtitle">Estas informações serão usadas como padrão em seus próximos checkouts.</p>

        <div class="account-card-form">
            <form method="POST" action="/minha-conta/cobranca">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="billing_first_name" class="form-control" value="<?= e($user['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Sobrenome *</label>
                        <input type="text" name="billing_last_name" class="form-control" value="<?= e($user['last_name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="billing_email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Telefone / WhatsApp *</label>
                    <input type="tel" name="billing_phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="+55 11 99999-9999">
                </div>

                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" name="billing_address" class="form-control" value="<?= e($user['address'] ?? '') ?>" placeholder="Rua, número, complemento">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Cidade</label>
                        <input type="text" name="billing_city" class="form-control" value="<?= e($user['city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>País</label>
                        <select name="billing_country" class="form-control">
                            <option value="BR" <?= ($user['country'] ?? '') === 'BR' ? 'selected' : '' ?>>Brasil</option>
                            <option value="US" <?= ($user['country'] ?? '') === 'US' ? 'selected' : '' ?>>Estados Unidos</option>
                            <option value="PT" <?= ($user['country'] ?? '') === 'PT' ? 'selected' : '' ?>>Portugal</option>
                            <option value="AR" <?= ($user['country'] ?? '') === 'AR' ? 'selected' : '' ?>>Argentina</option>
                            <option value="CO" <?= ($user['country'] ?? '') === 'CO' ? 'selected' : '' ?>>Colômbia</option>
                            <option value="CL" <?= ($user['country'] ?? '') === 'CL' ? 'selected' : '' ?>>Chile</option>
                            <option value="DO" <?= ($user['country'] ?? '') === 'DO' ? 'selected' : '' ?>>República Dominicana</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Salvar Informações</button>
            </form>
        </div>
    </div>
</div>
