<h3>Recuperar Senha</h3>
<p class="auth-desc">Informe seu email para receber um link de recuperação.</p>
<form method="POST" action="/esqueci-senha" class="auth-form">
    <?= csrf_field() ?>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Enviar Link</button>
</form>
<div class="auth-alt"><p><a href="/login">Voltar para Login</a></p></div>
