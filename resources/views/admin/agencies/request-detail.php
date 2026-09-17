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
    <div style="display:flex;gap:8px;margin-top:20px;">
        <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$request['id'] ?>/aprovar" onsubmit="return confirm('Aprovar esta agência? Será criado o acesso ao painel.')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success">Aprovar</button>
        </form>
        <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$request['id'] ?>/recusar" onsubmit="return document.getElementById('rejReason').value.trim() !== '' || (alert('Informe o motivo da recusa.'), false)">
            <?= csrf_field() ?>
            <input type="text" id="rejReason" name="block_reason" placeholder="Motivo da recusa" class="form-control" style="display:inline-block;width:240px;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;">
            <button type="submit" class="btn btn-danger">Recusar</button>
        </form>
    </div>
    <?php elseif (!empty($request['admin_notes'])): ?>
    <div style="margin-top:20px;padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
        <strong style="font-size:13px;color:#64748b;">Observações do admin:</strong>
        <p style="margin:6px 0 0;font-size:14px;"><?= nl2br(e($request['admin_notes'])) ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$request['id'] ?>/excluir" onsubmit="return confirm('Excluir esta solicitação permanentemente?')" style="margin-top:16px;">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm btn-outline" style="color:#dc2626;">Excluir solicitação</button>
    </form>
</div>
