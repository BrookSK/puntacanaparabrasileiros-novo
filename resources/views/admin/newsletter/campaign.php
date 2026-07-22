<div class="admin-card">
    <h3>Criar Nova Campanha</h3>
    <p style="margin-bottom:20px;color:#666;font-size:13px">
        A campanha será enviada para <strong><?= (int)$activeCount ?></strong> inscritos ativos.
        Você pode usar as variáveis <code>{email}</code> e <code>{name}</code> no conteúdo.
    </p>

    <form method="POST" action="/admin/newsletter/campanhas/enviar" onsubmit="return confirm('Enviar campanha para <?= (int)$activeCount ?> inscritos? Esta ação não pode ser desfeita.')">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Assunto do Email *</label>
            <input type="text" name="subject" class="form-control" placeholder="Ex: Novidades e promoções de Punta Cana!" required>
        </div>
        <div class="form-group">
            <label>Conteúdo (HTML) *</label>
            <textarea name="body" class="form-control" rows="12" placeholder="Escreva o conteúdo do email aqui. Aceita HTML." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg">Enviar Campanha</button>
    </form>
</div>

<?php if (!empty($campaigns)): ?>
<div class="admin-card" style="margin-top:24px">
    <h3>Campanhas Anteriores</h3>
    <table class="table">
        <thead>
            <tr><th>Assunto</th><th>Enviados</th><th>Falhas</th><th>Status</th><th>Data</th></tr>
        </thead>
        <tbody>
        <?php foreach ($campaigns as $c): ?>
        <tr>
            <td><?= e($c['subject']) ?></td>
            <td><?= (int)$c['sent_count'] ?> / <?= (int)$c['recipients_count'] ?></td>
            <td><?= (int)$c['failed_count'] ?></td>
            <td><span class="badge badge-<?= $c['status'] === 'sent' ? 'success' : ($c['status'] === 'draft' ? 'secondary' : 'warning') ?>"><?= e($c['status']) ?></span></td>
            <td><?= $c['sent_at'] ? format_date($c['sent_at']) : '-' ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
