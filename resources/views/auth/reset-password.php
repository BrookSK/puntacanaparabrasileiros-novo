<h3>Nova Senha</h3>
<form method="POST" action="/resetar-senha" class="auth-form">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= e($token ?? '') ?>">
    <div class="form-group">
        <label>Nova Senha</label>
        <input type="password" name="password" class="form-control" required minlength="6">
    </div>
    <div class="form-group">
        <label>Confirmar Nova Senha</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Alterar Senha</button>
</form>
