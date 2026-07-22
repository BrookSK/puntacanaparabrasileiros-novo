<!-- Hero -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Cadastre-se como Afiliado</h1>
            <p>Junte-se ao nosso programa de afiliados e tenha acesso a um painel completo para acompanhar suas vendas e comissões. O cadastro é rápido, gratuito e dá acesso a todas as ferramentas que você precisa para crescer com a gente.</p>
        </div>
    </div>
</section>

<!-- Formulário de Cadastro -->
<section class="section section-affiliate-register">
    <div class="container">
        <div class="affiliate-register-card">
            <h2>Preencha seus dados</h2>
            <p class="affiliate-register-desc">Campos marcados com <span class="required">*</span> são obrigatórios.</p>

            <form method="POST" action="/cadastro-afiliado" class="affiliate-register-form">
                <?= csrf_field() ?>

                <!-- Dados de Acesso -->
                <h3 class="form-section-title">Dados de Acesso</h3>
                <div class="form-group">
                    <label>Nome de usuário <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control <?= has_error('username') ? 'is-invalid' : '' ?>" value="<?= old('username') ?>" placeholder="Seu nome de usuário" required>
                    <?php if (has_error('username')): ?><span class="invalid-feedback"><?= e(error('username')) ?></span><?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Seu nome <span class="required">*</span></label>
                        <input type="text" name="first_name" class="form-control <?= has_error('first_name') ? 'is-invalid' : '' ?>" value="<?= old('first_name') ?>" required>
                        <?php if (has_error('first_name')): ?><span class="invalid-feedback"><?= e(error('first_name')) ?></span><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Sobrenome <span class="required">*</span></label>
                        <input type="text" name="last_name" class="form-control <?= has_error('last_name') ? 'is-invalid' : '' ?>" value="<?= old('last_name') ?>" required>
                        <?php if (has_error('last_name')): ?><span class="invalid-feedback"><?= e(error('last_name')) ?></span><?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>WhatsApp ou Telefone <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-control <?= has_error('phone') ? 'is-invalid' : '' ?>" value="<?= old('phone') ?>" placeholder="+55 11 99999-9999" required>
                        <?php if (has_error('phone')): ?><span class="invalid-feedback"><?= e(error('phone')) ?></span><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" placeholder="seu@email.com" required>
                        <?php if (has_error('email')): ?><span class="invalid-feedback"><?= e(error('email')) ?></span><?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Senha <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" placeholder="Mínimo 6 caracteres" required minlength="6">
                        <?php if (has_error('password')): ?><span class="invalid-feedback"><?= e(error('password')) ?></span><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Confirme a senha novamente <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control <?= has_error('password_confirmation') ? 'is-invalid' : '' ?>" placeholder="Repita a senha" required>
                        <?php if (has_error('password_confirmation')): ?><span class="invalid-feedback"><?= e(error('password_confirmation')) ?></span><?php endif; ?>
                    </div>
                </div>

                <!-- Dados de Pagamento -->
                <h3 class="form-section-title">Dados de Pagamento</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>E-Mail de Pagamento</label>
                        <input type="email" name="payment_email" class="form-control" value="<?= old('payment_email') ?>" placeholder="Email para receber pagamentos (PayPal)">
                    </div>
                    <div class="form-group">
                        <label>PIX <span class="required">*</span></label>
                        <input type="text" name="pix" class="form-control <?= has_error('pix') ? 'is-invalid' : '' ?>" value="<?= old('pix') ?>" placeholder="CPF, email ou chave aleatória" required>
                        <?php if (has_error('pix')): ?><span class="invalid-feedback"><?= e(error('pix')) ?></span><?php endif; ?>
                    </div>
                </div>

                <!-- Perfil de Conteúdo -->
                <h3 class="form-section-title">Seu Perfil de Conteúdo</h3>
                <div class="form-group">
                    <label>Site, Instagram ou TikTok</label>
                    <input type="text" name="website" class="form-control" value="<?= old('website') ?>" placeholder="@seuperfil ou https://seusite.com">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantidade de seguidores no Instagram ou TikTok <span class="required">*</span></label>
                        <select name="followers_count" class="form-control <?= has_error('followers_count') ? 'is-invalid' : '' ?>" required>
                            <option value="">Selecione</option>
                            <option value="5000-10000" <?= old('followers_count') === '5000-10000' ? 'selected' : '' ?>>5.000 - 10.000</option>
                            <option value="10000-50000" <?= old('followers_count') === '10000-50000' ? 'selected' : '' ?>>10.000 - 50.000</option>
                            <option value="50000-100000" <?= old('followers_count') === '50000-100000' ? 'selected' : '' ?>>50.000 - 100.000</option>
                            <option value="100000-500000" <?= old('followers_count') === '100000-500000' ? 'selected' : '' ?>>100.000 - 500.000</option>
                            <option value="500000+" <?= old('followers_count') === '500000+' ? 'selected' : '' ?>>500.000+</option>
                        </select>
                        <?php if (has_error('followers_count')): ?><span class="invalid-feedback"><?= e(error('followers_count')) ?></span><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Qual o seu nicho? <span class="required">*</span></label>
                        <select name="niche" class="form-control <?= has_error('niche') ? 'is-invalid' : '' ?>" required>
                            <option value="">Selecione</option>
                            <option value="viagens" <?= old('niche') === 'viagens' ? 'selected' : '' ?>>Viagens e Turismo</option>
                            <option value="lifestyle" <?= old('niche') === 'lifestyle' ? 'selected' : '' ?>>Lifestyle</option>
                            <option value="familia" <?= old('niche') === 'familia' ? 'selected' : '' ?>>Família</option>
                            <option value="aventura" <?= old('niche') === 'aventura' ? 'selected' : '' ?>>Aventura e Esportes</option>
                            <option value="luxo" <?= old('niche') === 'luxo' ? 'selected' : '' ?>>Luxo e Experiências</option>
                            <option value="outro" <?= old('niche') === 'outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                        <?php if (has_error('niche')): ?><span class="invalid-feedback"><?= e(error('niche')) ?></span><?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Qual o seu tipo de conteúdo? <span class="required">*</span></label>
                    <select name="content_type" class="form-control <?= has_error('content_type') ? 'is-invalid' : '' ?>" required>
                        <option value="">Selecione</option>
                        <option value="reels" <?= old('content_type') === 'reels' ? 'selected' : '' ?>>Reels / Vídeos Curtos</option>
                        <option value="youtube" <?= old('content_type') === 'youtube' ? 'selected' : '' ?>>Vídeos YouTube</option>
                        <option value="stories" <?= old('content_type') === 'stories' ? 'selected' : '' ?>>Stories / Diários</option>
                        <option value="blog" <?= old('content_type') === 'blog' ? 'selected' : '' ?>>Blog / Artigos</option>
                        <option value="fotos" <?= old('content_type') === 'fotos' ? 'selected' : '' ?>>Fotos e Carrosséis</option>
                        <option value="misto" <?= old('content_type') === 'misto' ? 'selected' : '' ?>>Misto / Vários formatos</option>
                    </select>
                    <?php if (has_error('content_type')): ?><span class="invalid-feedback"><?= e(error('content_type')) ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Como você vai nos promover?</label>
                    <textarea name="promotion_strategy" class="form-control" rows="4" placeholder="Descreva brevemente como pretende divulgar nossos passeios e transfers para sua audiência..."><?= old('promotion_strategy') ?></textarea>
                </div>

                <!-- Termos -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" required>
                        Li e aceito os <a href="/termos-afiliados" target="_blank">Termos e Condições do Programa de Afiliados</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Cadastrar como Afiliado</button>
            </form>
        </div>
    </div>
</section>
