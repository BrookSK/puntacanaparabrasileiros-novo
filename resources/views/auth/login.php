<h3>Entrar na sua conta</h3>
<p class="auth-desc">Acesse sua conta para gerenciar reservas, transfers e muito mais.</p>

<form method="POST" action="/login" class="auth-form">
    <?= csrf_field() ?>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" placeholder="seu@email.com" required autofocus>
        <?php if (has_error('email')): ?><span class="invalid-feedback"><?= e(error('email')) ?></span><?php endif; ?>
    </div>
    <div class="form-group">
        <label>Senha</label>
        <input type="password" name="password" class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" placeholder="••••••••" required>
        <?php if (has_error('password')): ?><span class="invalid-feedback"><?= e(error('password')) ?></span><?php endif; ?>
    </div>
    <div class="form-row form-row-between">
        <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> Lembrar-me</label>
        <a href="/esqueci-senha" class="link-sm">Esqueci a senha</a>
    </div>
    <button type="submit" class="btn btn-auth-primary">Entrar</button>
</form>
<div class="auth-divider"><span>ou</span></div>
<div class="auth-alt">
    <p>Não tem conta? <a href="/registrar"><strong>Criar conta grátis</strong></a></p>
</div>
