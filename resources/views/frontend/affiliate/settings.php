<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('affiliate-nav', ['active' => 'configuracoes']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Configurações</h1>
                        <p class="aff-page-subtitle">Gerencie seus dados pessoais e informações de pagamento</p>
                    </div>
                </div>

                <form method="POST" action="/painel-afiliado/configuracoes">
                    <?= csrf_field() ?>

                    <!-- Dados pessoais -->
                    <div class="aff-card">
                        <div class="aff-card-header">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-green)" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <h3 class="aff-card-title">Dados Pessoais</h3>
                        </div>
                        <div class="aff-form-grid">
                            <div class="form-group">
                                <label>WhatsApp ou Telefone *</label>
                                <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Site, Instagram ou TikTok</label>
                                <input type="text" name="website" class="form-control" value="<?= e($affiliateNotes['website'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Quantidade de Seguidores *</label>
                                <input type="text" name="followers_count" class="form-control" value="<?= e($affiliateNotes['followers_count'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Nicho *</label>
                                <select name="niche" class="form-control" required>
                                    <option value="">Selecione</option>
                                    <option value="viagens" <?= ($affiliateNotes['niche'] ?? '') === 'viagens' ? 'selected' : '' ?>>Viagens e Turismo</option>
                                    <option value="lifestyle" <?= ($affiliateNotes['niche'] ?? '') === 'lifestyle' ? 'selected' : '' ?>>Lifestyle</option>
                                    <option value="influencia" <?= ($affiliateNotes['niche'] ?? '') === 'influencia' ? 'selected' : '' ?>>Influência Digital</option>
                                    <option value="familia" <?= ($affiliateNotes['niche'] ?? '') === 'familia' ? 'selected' : '' ?>>Família</option>
                                    <option value="outro" <?= ($affiliateNotes['niche'] ?? '') === 'outro' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tipo de Conteúdo *</label>
                                <select name="content_type" class="form-control" required>
                                    <option value="">Selecione</option>
                                    <option value="reels" <?= ($affiliateNotes['content_type'] ?? '') === 'reels' ? 'selected' : '' ?>>Reels / Vídeos Curtos</option>
                                    <option value="youtube" <?= ($affiliateNotes['content_type'] ?? '') === 'youtube' ? 'selected' : '' ?>>Canal no YouTube</option>
                                    <option value="blog" <?= ($affiliateNotes['content_type'] ?? '') === 'blog' ? 'selected' : '' ?>>Blog / Artigos</option>
                                    <option value="misto" <?= ($affiliateNotes['content_type'] ?? '') === 'misto' ? 'selected' : '' ?>>Misto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dados de pagamento -->
                    <div class="aff-card">
                        <div class="aff-card-header">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-green)" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            <h3 class="aff-card-title">Dados de Pagamento</h3>
                        </div>
                        <div class="aff-form-grid">
                            <div class="form-group">
                                <label>PIX *</label>
                                <input type="text" name="pix" class="form-control" value="<?= e($affiliateNotes['pix'] ?? '') ?>" required placeholder="CPF, email ou chave aleatória">
                                <small style="font-size:11px;color:var(--gray);margin-top:4px;display:block;">Chave PIX para recebimento de comissões</small>
                            </div>
                            <div class="form-group">
                                <label>E-Mail de Pagamento</label>
                                <input type="email" name="payment_email" class="form-control" value="<?= e($affiliate['payment_email'] ?? '') ?>" placeholder="Para receber pagamentos via PayPal">
                            </div>
                        </div>
                        <h4 style="font-size:13px;color:#64748b;margin:12px 0 8px;font-weight:600;">Dados Bancários (para TED)</h4>
                        <div class="aff-form-grid">
                            <div class="form-group">
                                <label>Banco *</label>
                                <input type="text" name="bank_name" class="form-control" value="<?= e($affiliateNotes['bank_name'] ?? '') ?>" required placeholder="Ex: Banco do Brasil, Nubank, Itaú...">
                            </div>
                            <div class="form-group">
                                <label>Agência *</label>
                                <input type="text" name="bank_agency" class="form-control" value="<?= e($affiliateNotes['bank_agency'] ?? '') ?>" required placeholder="Ex: 0001">
                            </div>
                        </div>
                        <div class="aff-form-grid">
                            <div class="form-group">
                                <label>Conta *</label>
                                <input type="text" name="bank_account" class="form-control" value="<?= e($affiliateNotes['bank_account'] ?? '') ?>" required placeholder="Ex: 12345-6">
                            </div>
                            <div class="form-group">
                                <label>Tipo de Conta *</label>
                                <select name="bank_account_type" class="form-control" required>
                                    <option value="corrente" <?= ($affiliateNotes['bank_account_type'] ?? '') === 'corrente' ? 'selected' : '' ?>>Conta Corrente</option>
                                    <option value="poupanca" <?= ($affiliateNotes['bank_account_type'] ?? '') === 'poupanca' ? 'selected' : '' ?>>Conta Poupança</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:24px;">
                        <button type="submit" class="btn btn-primary btn-lg">Salvar Configurações</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</section>
