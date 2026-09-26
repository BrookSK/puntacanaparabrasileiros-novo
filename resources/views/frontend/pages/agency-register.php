<?php
$errors = $session->getFlash('errors') ?? [];
$old = $session->getFlash('old') ?? [];
$success = $session->getFlash('success');
$error = $session->getFlash('error');
?>

<!-- Hero Agência -->
<section class="section section-affiliate-hero">
    <div class="container">
        <div class="affiliate-hero-grid">
            <!-- Esquerda: Texto -->
            <div class="affiliate-hero-content">
                <h1>Torne sua Agência uma <span class="text-highlight-green">Parceira Oficial</span></h1>
                <p>Cadastre sua agência e ofereça as experiências da Punta Cana para Brasileiros aos seus clientes, com comissões atrativas e todo o suporte da nossa equipe.</p>
                <div class="affiliate-hero-actions">
                    <a href="#form-cadastro-agencia" class="btn btn-secondary">Quero ser Parceira &rarr;</a>
                    <a href="/login-agencia" class="btn btn-outline">Já sou Parceira</a>
                </div>
                <div class="affiliate-hero-stats">
                    <span class="affiliate-stat">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                        Agências Parceiras
                    </span>
                    <span class="affiliate-stat">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        Comissões Atrativas
                    </span>
                </div>
            </div>

            <!-- Direita: Cards de benefícios -->
            <div class="affiliate-hero-cards">
                <div class="affiliate-benefit-card benefit-card-green">
                    <div class="benefit-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <h4>Comissões</h4>
                    <p>Por cada reserva realizada</p>
                </div>
                <div class="affiliate-benefit-card benefit-card-blue">
                    <div class="benefit-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 17V9"/><path d="M12 17V5"/><path d="M17 17v-3"/></svg>
                    </div>
                    <h4>Painel da Agência</h4>
                    <p>Acompanhe em tempo real</p>
                </div>
                <div class="affiliate-benefit-card benefit-card-yellow">
                    <div class="benefit-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8V4"/><path d="M12 16v4"/><rect x="7" y="12" width="10" height="8" rx="1"/></svg>
                    </div>
                    <h4>Materiais</h4>
                    <p>Criativos exclusivos</p>
                </div>
                <div class="affiliate-benefit-card benefit-card-darkblue">
                    <div class="benefit-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <h4>Suporte Dedicado</h4>
                    <p>Atendimento para parceiros</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Plataformas / Diferenciais -->
<section class="section-affiliate-platforms">
    <div class="container">
        <p class="platforms-subtitle">Ideal para agências de viagem e operadores de turismo</p>
        <div class="platforms-list">
            <span class="platform-pill">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                Agências de Viagem
            </span>
            <span class="platform-pill">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 010 20 15 15 0 010-20"/></svg>
                Operadores de Turismo
            </span>
            <span class="platform-pill">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d4a005" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>
                Consultores de Viagem
            </span>
        </div>
    </div>
</section>

<section class="auth-page" id="form-cadastro-agencia" style="max-width:640px;margin:40px auto;padding:0 20px;">
    <div style="text-align:center;margin-bottom:24px;">
        <h1 style="font-size:26px;color:#1e293b;margin-bottom:8px;">Cadastre sua Agência</h1>
        <p style="color:#64748b;font-size:15px;">Torne-se uma agência parceira da Punta Cana para Brasileiros. Preencha os dados abaixo — nossa equipe analisará e liberará seu acesso ao painel.</p>
    </div>

    <?php if ($success): ?>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:14px 16px;margin-bottom:18px;font-size:14px;"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:14px 16px;margin-bottom:18px;font-size:14px;"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/cadastro-agencia" class="auth-form" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Razão Social <span style="color:#dc2626;">*</span></label>
            <input type="text" name="company_name" value="<?= e($old['company_name'] ?? '') ?>" class="form-control" required style="width:100%;padding:11px 14px;border:1px solid <?= isset($errors['company_name']) ? '#dc2626' : '#e2e8f0' ?>;border-radius:8px;font-size:14px;">
            <?php if (isset($errors['company_name'])): ?><small style="color:#dc2626;"><?= e($errors['company_name']) ?></small><?php endif; ?>
        </div>

        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Nome Fantasia</label>
                <input type="text" name="trade_name" value="<?= e($old['trade_name'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">CNPJ</label>
                <input type="text" name="cnpj" value="<?= e($old['cnpj'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Nome do Contato <span style="color:#dc2626;">*</span></label>
            <input type="text" name="contact_name" value="<?= e($old['contact_name'] ?? '') ?>" class="form-control" required style="width:100%;padding:11px 14px;border:1px solid <?= isset($errors['contact_name']) ? '#dc2626' : '#e2e8f0' ?>;border-radius:8px;font-size:14px;">
            <?php if (isset($errors['contact_name'])): ?><small style="color:#dc2626;"><?= e($errors['contact_name']) ?></small><?php endif; ?>
        </div>

        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">E-mail <span style="color:#dc2626;">*</span></label>
                <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" class="form-control" required style="width:100%;padding:11px 14px;border:1px solid <?= isset($errors['email']) ? '#dc2626' : '#e2e8f0' ?>;border-radius:8px;font-size:14px;">
                <?php if (isset($errors['email'])): ?><small style="color:#dc2626;"><?= e($errors['email']) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">WhatsApp/Telefone <span style="color:#dc2626;">*</span></label>
                <input type="text" name="phone" value="<?= e($old['phone'] ?? '') ?>" class="form-control" required style="width:100%;padding:11px 14px;border:1px solid <?= isset($errors['phone']) ? '#dc2626' : '#e2e8f0' ?>;border-radius:8px;font-size:14px;">
                <?php if (isset($errors['phone'])): ?><small style="color:#dc2626;"><?= e($errors['phone']) ?></small><?php endif; ?>
            </div>
        </div>

        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Cidade</label>
                <input type="text" name="city" value="<?= e($old['city'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">País</label>
                <input type="text" name="country" value="<?= e($old['country'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
            </div>
        </div>

        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Senha <span style="color:#dc2626;">*</span></label>
                <input type="password" name="password" class="form-control" required style="width:100%;padding:11px 14px;border:1px solid <?= isset($errors['password']) ? '#dc2626' : '#e2e8f0' ?>;border-radius:8px;font-size:14px;">
                <?php if (isset($errors['password'])): ?><small style="color:#dc2626;"><?= e($errors['password']) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Confirmar Senha <span style="color:#dc2626;">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required style="width:100%;padding:11px 14px;border:1px solid <?= isset($errors['password_confirmation']) ? '#dc2626' : '#e2e8f0' ?>;border-radius:8px;font-size:14px;">
                <?php if (isset($errors['password_confirmation'])): ?><small style="color:#dc2626;"><?= e($errors['password_confirmation']) ?></small><?php endif; ?>
            </div>
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Mensagem (opcional)</label>
            <textarea name="message" rows="3" class="form-control" placeholder="Conte um pouco sobre a sua agência" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;resize:vertical;"><?= e($old['message'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;background:#0077b6;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">Enviar Solicitação</button>

        <p style="text-align:center;margin-top:16px;font-size:13px;color:#64748b;">
            Já é uma agência parceira? <a href="/login-agencia" style="color:#0077b6;font-weight:600;">Acesse seu painel</a>
        </p>
    </form>
</section>
