<?php
$errors = $session->getFlash('errors') ?? [];
$old = $session->getFlash('old') ?? [];
$success = $session->getFlash('success');
$error = $session->getFlash('error');
?>
<section class="auth-page" style="max-width:640px;margin:40px auto;padding:0 20px;">
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
