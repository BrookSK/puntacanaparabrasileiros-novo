<?php
$priorityLabels = ['low' => 'Baixa', 'medium' => 'Média', 'high' => 'Alta', 'urgent' => 'Urgente'];
$priorityColors = ['low' => 'secondary', 'medium' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
$syncLabels = ['synced' => 'Sincronizado', 'pending' => 'Pendente', 'failed' => 'Falhou'];
$syncColors = ['synced' => 'success', 'pending' => 'warning', 'failed' => 'danger'];
$p = $ticket['priority'] ?? 'medium';
$s = $ticket['sync_status'] ?? 'pending';
?>

<div class="card-header">
    <h2><?= e($ticket['title']) ?></h2>
    <a href="/admin/suporte" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Suporte
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-icon admin-card-icon-blue">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 11-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
        </div>
        <div>
            <h3>Detalhes da Demanda</h3>
            <p class="admin-card-subtitle">Ref. externa: <?= e($ticket['external_ref'] ?? '-') ?></p>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-4">
            <label>Prioridade</label>
            <div><span class="badge badge-<?= $priorityColors[$p] ?? 'secondary' ?>"><?= $priorityLabels[$p] ?? $p ?></span></div>
        </div>
        <div class="form-group col-4">
            <label>Categoria</label>
            <div><?= e($ticket['category'] ?? '-') ?: '-' ?></div>
        </div>
        <div class="form-group col-4">
            <label>Status de sincronização</label>
            <div><span class="badge badge-<?= $syncColors[$s] ?? 'secondary' ?>"><?= $syncLabels[$s] ?? $s ?></span></div>
        </div>
    </div>

    <div class="form-group">
        <label>Descrição</label>
        <div style="white-space:pre-wrap;"><?= e($ticket['description'] ?? '') ?></div>
    </div>

    <div class="form-row">
        <div class="form-group col-6">
            <label>Solicitante</label>
            <div><?= e($ticket['requester_name'] ?? '-') ?: '-' ?></div>
        </div>
        <div class="form-group col-6">
            <label>Empresa</label>
            <div><?= e($ticket['requester_company'] ?? '-') ?: '-' ?></div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-6">
            <label>Criada em</label>
            <div><?= !empty($ticket['created_at']) ? date('d/m/Y H:i', strtotime((string)$ticket['created_at'])) : '-' ?></div>
        </div>
        <div class="form-group col-6">
            <label>Sincronizada em</label>
            <div><?= !empty($ticket['synced_at']) ? date('d/m/Y H:i', strtotime((string)$ticket['synced_at'])) : '-' ?></div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-icon admin-card-icon-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </div>
        <div>
            <h3>Registro no LRV</h3>
            <p class="admin-card-subtitle">Dados retornados pelo helpdeskON</p>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-4">
            <label>ID no LRV</label>
            <div><?= !empty($ticket['lrv_id']) ? (int) $ticket['lrv_id'] : '-' ?></div>
        </div>
        <div class="form-group col-4">
            <label>Nº do chamado</label>
            <div><?= !empty($ticket['lrv_client_ticket_number']) ? '#' . (int) $ticket['lrv_client_ticket_number'] : '-' ?></div>
        </div>
        <div class="form-group col-4">
            <label>Status no LRV</label>
            <div><?= e($ticket['lrv_status'] ?? '-') ?: '-' ?></div>
        </div>
    </div>

    <?php if ($s === 'failed' && !empty($ticket['sync_error'])): ?>
    <div class="form-group">
        <label>Último erro de envio</label>
        <div class="text-danger" style="white-space:pre-wrap;"><?= e((string)$ticket['sync_error']) ?></div>
    </div>
    <?php endif; ?>
</div>

<div class="form-actions">
    <a href="/admin/suporte" class="btn btn-outline">Voltar</a>
    <?php if ($s !== 'synced'): ?>
    <form method="POST" action="/admin/suporte/<?= (int)$ticket['id'] ?>/reenviar" class="inline-form" onsubmit="return confirm('Reenviar esta demanda ao LRV?')">
        <?= csrf_field() ?>
        <button class="btn btn-primary">Reenviar ao LRV</button>
    </form>
    <?php endif; ?>
    <form method="POST" action="/admin/suporte/<?= (int)$ticket['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir esta demanda? Esta ação não pode ser desfeita.')">
        <?= csrf_field() ?>
        <button class="btn btn-danger">Excluir</button>
    </form>
</div>
