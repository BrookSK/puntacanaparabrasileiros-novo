<!-- Hero -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Acesse sua Conta de Afiliado</h1>
            <p>Bem-vindo de volta! Faça login para acessar seu painel exclusivo de afiliado, gerenciar seus links de indicação, visualizar relatórios de desempenho e acompanhar seus ganhos.</p>
        </div>
    </div>
</section>

<!-- Formulário de Login -->
<section class="section section-affiliate-register">
    <div class="container">
        <div class="affiliate-register-card">
            <h2>Login do Afiliado</h2>
            <p class="affiliate-register-desc">Informe suas credenciais para acessar o painel.</p>

            <form method="POST" action="/login" class="affiliate-register-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" placeholder="seu@email.com" required autofocus>
                    <?php if (has_error('email')): ?><span class="invalid-feedback"><?= e(error('email')) ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Senha <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" placeholder="••••••••" required>
                    <?php if (has_error('password')): ?><span class="invalid-feedback"><?= e(error('password')) ?></span><?php endif; ?>
                </div>

                <div class="form-row form-row-between" style="margin-bottom:20px">
                    <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> Lembrar-me</label>
                    <a href="/esqueci-senha" style="font-size:13px;color:var(--accent)">Esqueci minha senha</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Entrar</button>
            </form>

            <div style="text-align:center;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0">
                <p style="font-size:14px;color:var(--gray)">Ainda não é afiliado?</p>
                <a href="/cadastro-afiliado" class="btn btn-secondary" style="margin-top:10px">Cadastre-se Agora &rarr;</a>
            </div>
        </div>
    </div>
</section>
