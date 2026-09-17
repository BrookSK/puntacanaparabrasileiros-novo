<div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
    <h2>Solicitação de Agência</h2>
    <a href="/admin/agencias" class="btn btn-outline">Voltar</a>
</div>

<div class="admin-card" style="max-width:640px;">
    <table class="table-info" style="width:100%;">
        <tr><td style="width:180px;color:#64748b;">Razão social</td><td><strong><?= e($request['company_name']) ?></strong></td></tr>
        <tr><td style="color:#64748b;">Nome fantasia</td><td><?= e($request['trade_name'] ?: '—') ?></td></tr>
        <tr><td style="color:#64748b;">CNPJ</td><td><?= e($request['cnpj'] ?: '—') ?></td></tr>
        <tr><td style="color:#64748b;">Contato</td><td><?= e($request['contact_name'] ?: '—') ?></td></tr>
        <tr><td style="color:#64748b;">E-mail</td><td><?= e($request['email']) ?></td></tr>
        <tr><td style="color:#64748b;">Telefone</td><td><?= e($request['phone'] ?: '—') ?></td></tr>
        <tr><td style="color:#64748b;">Cidade / País</td><td><?= e(trim(($request['city'] ?? '') . ' / ' . ($request['country'] ?? ''), ' /')) ?: '—' ?></td></tr>
        <tr><td style="color:#64748b;">Mensagem</td><td><?= nl2br(e($request['message'] ?? '—')) ?></td></tr>
        <tr><td style="color:#64748b;">Status</td><td><span class="badge badge-info"><?= e($request['status']) ?></span></td></tr>
        <tr><td style="color:#64748b;">Data</td><td><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></td></tr>
    </table>

    <?php if ($request['status'] === 'pending'): ?>
    <div style="display:flex;gap:10px;align-items:center;margin-top:22px;flex-wrap:wrap;">
        <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$request['id'] ?>/aprovar" onsubmit="return confirm('Aprovar esta agência? Será criado o acesso ao painel.')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success">Aprovar</button>
        </form>
        <button type="button" class="btn btn-danger" onclick="document.getElementById('agencyRejectModal').style.display='flex'">Recusar</button>
        <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$request['id'] ?>/excluir" onsubmit="return confirm('Excluir esta solicitação permanentemente?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline">Excluir</button>
        </form>
    </div>
    <?php else: ?>
        <?php if (!empty($request['admin_notes'])): ?>
        <div style="margin-top:20px;padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
            <strong style="font-size:13px;color:#64748b;">Observações do admin:</strong>
            <p style="margin:6px 0 0;font-size:14px;"><?= nl2br(e($request['admin_notes'])) ?></p>
        </div>
        <?php endif; ?>
        <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$request['id'] ?>/excluir" onsubmit="return confirm('Excluir esta solicitação permanentemente?')" style="margin-top:16px;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline">Excluir solicitação</button>
        </form>
    <?php endif; ?>
</div>

<?php if ($request['status'] === 'pending'): ?>
<!-- Modal de recusa: o motivo só aparece aqui -->
<div id="agencyRejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:440px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <h3 style="margin:0 0 12px;font-size:18px;color:#1a1a1a;">Recusar solicitação</h3>
        <p style="font-size:14px;color:#64748b;margin-bottom:14px;">Informe o motivo da recusa de <strong><?= e($request['company_name']) ?></strong>. A agência será notificada por e-mail e WhatsApp.</p>
        <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$request['id'] ?>/recusar">
            <?= csrf_field() ?>
            <textarea name="block_reason" rows="3" required class="form-control" placeholder="Motivo da recusa" style="width:100%;padding:10px;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:14px;"></textarea>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('agencyRejectModal').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-danger">Confirmar recusa</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
