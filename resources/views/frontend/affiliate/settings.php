<section class="passeios-hero"><div class="container"><div class="passeios-hero-content"><h1>Painel do Afiliado</h1></div></div></section>
<section class="section section-affiliate-panel">
    <div class="container">
        <?= partial('affiliate-nav', ['active' => 'configuracoes']) ?>
        <div class="affiliate-panel-content">
            <div class="affiliate-card">
                <form method="POST" action="/painel-afiliado/configuracoes">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>WhatsApp ou Telefone *</label>
                        <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>E-Mail de Pagamento</label>
                        <input type="email" name="payment_email" class="form-control" value="<?= e($affiliate['payment_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>PIX *</label>
                        <input type="text" name="pix" class="form-control" value="<?= e($affiliateNotes['pix'] ?? '') ?>" required>
                        <small class="form-help">Para pagamentos via pix</small>
                    </div>
                    <div class="form-group">
                        <label>Site, Instagram ou TikTok</label>
                        <input type="text" name="website" class="form-control" value="<?= e($affiliateNotes['website'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Quantidade de seguidores no Instagram ou TikTok *</label>
                        <input type="text" name="followers_count" class="form-control" value="<?= e($affiliateNotes['followers_count'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Qual o seu nicho? *</label>
                        <select name="niche" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="viagens" <?= ($affiliateNotes['niche'] ?? '') === 'viagens' ? 'selected' : '' ?>>Viagens e Turismo</option>
                            <option value="lifestyle" <?= ($affiliateNotes['niche'] ?? '') === 'lifestyle' ? 'selected' : '' ?>>Lifestyle</option>
                            <option value="influencia" <?= ($affiliateNotes['niche'] ?? '') === 'influencia' ? 'selected' : '' ?>>Influência digital / lifestyle no Caribe</option>
                            <option value="familia" <?= ($affiliateNotes['niche'] ?? '') === 'familia' ? 'selected' : '' ?>>Família</option>
                            <option value="outro" <?= ($affiliateNotes['niche'] ?? '') === 'outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Qual o seu tipo de conteúdo? *</label>
                        <select name="content_type" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="reels" <?= ($affiliateNotes['content_type'] ?? '') === 'reels' ? 'selected' : '' ?>>Reels / Vídeos Curtos</option>
                            <option value="youtube" <?= ($affiliateNotes['content_type'] ?? '') === 'youtube' ? 'selected' : '' ?>>Canal no YouTube</option>
                            <option value="blog" <?= ($affiliateNotes['content_type'] ?? '') === 'blog' ? 'selected' : '' ?>>Blog / Artigos</option>
                            <option value="misto" <?= ($affiliateNotes['content_type'] ?? '') === 'misto' ? 'selected' : '' ?>>Misto</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</section>
