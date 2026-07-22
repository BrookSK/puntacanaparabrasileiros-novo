<h3>Criar sua conta</h3>
<p class="auth-desc">Cadastre-se para reservar passeios, gerenciar transfers e acessar sua lista de desejos.</p>

<form method="POST" action="/registrar" class="auth-form">
    <?= csrf_field() ?>
    <div class="form-row">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" name="first_name" class="form-control <?= has_error('first_name') ? 'is-invalid' : '' ?>" value="<?= old('first_name') ?>" placeholder="Seu nome" required>
            <?php if (has_error('first_name')): ?><span class="invalid-feedback"><?= e(error('first_name')) ?></span><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Sobrenome *</label>
            <input type="text" name="last_name" class="form-control <?= has_error('last_name') ? 'is-invalid' : '' ?>" value="<?= old('last_name') ?>" placeholder="Seu sobrenome" required>
        </div>
    </div>
    <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" placeholder="seu@email.com" required>
        <?php if (has_error('email')): ?><span class="invalid-feedback"><?= e(error('email')) ?></span><?php endif; ?>
    </div>
    <div class="form-group">
        <label>Telefone/WhatsApp</label>
        <input type="tel" name="phone" class="form-control" value="<?= old('phone') ?>" placeholder="+55 11 99999-9999">
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Senha *</label>
            <input type="password" name="password" class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" placeholder="Mínimo 6 caracteres" required minlength="6">
            <?php if (has_error('password')): ?><span class="invalid-feedback"><?= e(error('password')) ?></span><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Confirmar Senha *</label>
            <input type="password" name="password_confirmation" class="form-control <?= has_error('password_confirmation') ? 'is-invalid' : '' ?>" placeholder="Repita a senha" required>
        </div>
    </div>
    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" required>
            Li e aceito os <a href="/pagina/termos" target="_blank">termos e condições</a>
        </label>
    </div>
    <button type="submit" class="btn btn-auth-primary">Criar Conta</button>
</form>
<div class="auth-divider"><span>ou</span></div>
<div class="auth-alt">
    <p>Já tem conta? <a href="/login"><strong>Entrar</strong></a></p>
</div>
