<div class="card-header">
    <h2>Solicitação de Afiliação</h2>
    <a href="/admin/afiliados?tab=solicitacoes" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar
    </a>
</div>

<div style="max-width:800px;">
    <!-- Info do solicitante -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <h3><?= e($request['first_name'] . ' ' . $request['last_name']) ?></h3>
                <p class="admin-card-subtitle">Solicitação recebida em <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></p>
            </div>
        </div>

        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
                <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Nome Completo</label>
                <p style="margin:4px 0 16px;font-size:14px;"><?= e($request['first_name'] . ' ' . $request['last_name']) ?></p>

                <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Email</label>
                <p style="margin:4px 0 16px;font-size:14px;"><a href="mailto:<?= e($request['email']) ?>"><?= e($request['email']) ?></a></p>

                <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">WhatsApp</label>
                <p style="margin:4px 0 16px;font-size:14px;"><?= phone_with_flag($request['phone'] ?? '') ?></p>
            </div>
            <div>
                <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Seguidores</label>
                <p style="margin:4px 0 16px;font-size:14px;"><span class="badge badge-info"><?= e($request['followers_count'] ?? '-') ?></span></p>

                <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Nicho</label>
                <p style="margin:4px 0 16px;font-size:14px;"><?= e(ucfirst($request['niche'] ?? '-')) ?></p>

                <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Tipo de Conteúdo</label>
                <p style="margin:4px 0 16px;font-size:14px;"><?= e(ucfirst($request['content_type'] ?? '-')) ?></p>

                <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Site / Redes Sociais</label>
                <p style="margin:4px 0 16px;font-size:14px;"><?= e($request['website'] ?? '-') ?></p>
            </div>
        </div>

        <?php if (!empty($request['social_links'])): ?>
        <div style="margin-top:8px;">
            <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Links das Redes Sociais</label>
            <p style="margin:4px 0 16px;font-size:14px;"><?= e($request['social_links']) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($request['how_found'])): ?>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Como Conheceu</label>
            <p style="margin:4px 0 16px;font-size:14px;"><?= e($request['how_found']) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($request['promotion_strategy'])): ?>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Estratégia de Divulgação</label>
            <p style="margin:4px 0 16px;font-size:14px;line-height:1.6;"><?= nl2br(e($request['promotion_strategy'])) ?></p>
        </div>
        <?php endif; ?>

        <div style="margin-top:8px;">
            <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">PIX</label>
            <p style="margin:4px 0 16px;font-size:14px;"><?= e($request['pix'] ?? '-') ?></p>
        </div>

        <?php if (!empty($request['payment_email'])): ?>
        <div>
            <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;">Email de Pagamento</label>
            <p style="margin:4px 0 16px;font-size:14px;"><?= e($request['payment_email']) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Ações -->
    <?php if ($request['status'] === 'pending'): ?>
    <div class="admin-card" style="margin-top:20px;">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-orange">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <h3>Decisão</h3>
                <p class="admin-card-subtitle">Aprovar ou recusar esta solicitação</p>
            </div>
        </div>

        <div class="form-group">
            <label>Observações do Admin (opcional)</label>
            <textarea id="adminNotes" class="form-control" rows="3" placeholder="Motivo da aprovação/recusa..."></textarea>
        </div>

        <div style="display:flex;gap:12px;">
            <form method="POST" action="/admin/afiliados/solicitacao/<?= (int)$request['id'] ?>/aprovar" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="admin_notes" id="approveNotes">
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('approveNotes').value=document.getElementById('adminNotes').value;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Aprovar Afiliação
                </button>
            </form>
            <form method="POST" action="/admin/afiliados/solicitacao/<?= (int)$request['id'] ?>/recusar" class="inline-form" onsubmit="return confirm('Tem certeza que deseja recusar esta solicitação?')">
                <?= csrf_field() ?>
                <input type="hidden" name="admin_notes" id="rejectNotes">
                <button type="submit" class="btn btn-danger" onclick="document.getElementById('rejectNotes').value=document.getElementById('adminNotes').value;">
                    Recusar Solicitação
                </button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="admin-card" style="margin-top:20px;padding:20px;">
        <span class="badge badge-<?= $request['status'] === 'approved' ? 'success' : 'danger' ?>" style="font-size:13px;padding:6px 14px;">
            <?= $request['status'] === 'approved' ? 'Aprovado' : 'Recusado' ?>
            em <?= date('d/m/Y H:i', strtotime($request['status'] === 'approved' ? $request['approved_at'] : $request['rejected_at'])) ?>
        </span>
        <?php if (!empty($request['admin_notes'])): ?>
        <p style="margin-top:10px;font-size:13px;color:#64748b;">Obs: <?= e($request['admin_notes']) ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
