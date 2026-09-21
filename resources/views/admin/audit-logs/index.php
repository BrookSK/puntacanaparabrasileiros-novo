<?php
use App\Models\AuditLog;
?>

<!-- Estatísticas Rápidas -->
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-value"><?= number_format($stats['total'] ?? 0, 0, ',', '.') ?></div>
        <div class="stat-label">Total de Registros</div>
    </div>
    <div class="stat-card">
        <?php 
        $successCount = 0;
        foreach (($stats['by_result'] ?? []) as $r) {
            if ($r['result'] === 'success') $successCount = (int) $r['count'];
        }
        ?>
        <div class="stat-value" style="color: var(--success)"><?= number_format($successCount, 0, ',', '.') ?></div>
        <div class="stat-label">Ações com Sucesso</div>
    </div>
    <div class="stat-card">
        <?php 
        $failCount = 0;
        foreach (($stats['by_result'] ?? []) as $r) {
            if ($r['result'] === 'failure') $failCount = (int) $r['count'];
        }
        ?>
        <div class="stat-value" style="color: var(--danger)"><?= number_format($failCount, 0, ',', '.') ?></div>
        <div class="stat-label">Falhas</div>
    </div>
</div>

<!-- Filtros -->
<div class="card-header">
    <div class="header-actions">
        <a href="/admin/audit-logs/export?<?= http_build_query($filters) ?>" class="btn btn-outline" title="Exportar CSV">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Exportar
        </a>
    </div>
    <form method="GET" class="filter-form" style="flex-wrap: wrap; gap: 0.5rem;">
        <select name="user_id" class="form-control" style="min-width: 180px;">
            <option value="">Todos os Usuários</option>
            <?php foreach ($distinctUsers as $u): ?>
            <option value="<?= (int) $u['user_id'] ?>" <?= ($filters['user_id'] ?? '') == $u['user_id'] ? 'selected' : '' ?>>
                <?= e($u['user_name'] ?? $u['user_email']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <select name="action" class="form-control" style="min-width: 150px;">
            <option value="">Todas as Ações</option>
            <?php foreach ($distinctActions as $a): ?>
            <option value="<?= e($a['action']) ?>" <?= ($filters['action'] ?? '') === $a['action'] ? 'selected' : '' ?>>
                <?= e(AuditLog::translateAction($a['action'])) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <select name="entity_type" class="form-control" style="min-width: 150px;">
            <option value="">Todos os Tipos</option>
            <?php foreach ($distinctEntityTypes as $et): ?>
            <option value="<?= e($et['entity_type']) ?>" <?= ($filters['entity_type'] ?? '') === $et['entity_type'] ? 'selected' : '' ?>>
                <?= e(AuditLog::translateEntityType($et['entity_type'])) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <input type="date" name="date_from" class="form-control" value="<?= e($filters['date_from'] ?? '') ?>" placeholder="Data inicial" title="Data inicial" style="width: 140px;">
        <input type="date" name="date_to" class="form-control" value="<?= e($filters['date_to'] ?? '') ?>" placeholder="Data final" title="Data final" style="width: 140px;">
        
        <input type="text" name="busca" class="form-control" placeholder="Buscar..." value="<?= e($filters['busca'] ?? '') ?>" style="min-width: 150px;">
        
        <button type="submit" class="btn btn-outline">Filtrar</button>
        <?php if (array_filter($filters)): ?>
        <a href="/admin/audit-logs" class="btn btn-sm" style="color: var(--text-muted);">Limpar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabela de Logs -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width: 140px;">Data/Hora</th>
                <th style="width: 160px;">Usuário</th>
                <th style="width: 120px;">Ação</th>
                <th style="width: 100px;">Tipo</th>
                <th>Descrição</th>
                <th style="width: 80px;">Resultado</th>
                <th style="width: 60px;">Detalhes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs['items'])): ?>
            <tr>
                <td colspan="7" class="text-center" style="padding: 2rem; color: var(--text-muted);">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem; opacity: 0.5;">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <br>Nenhum registro de auditoria encontrado.
                    <?php if (array_filter($filters)): ?>
                    <br><small>Tente ajustar os filtros.</small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($logs['items'] as $log): ?>
            <tr>
                <td>
                    <small style="color: var(--text-muted);">
                        <?= date('d/m/Y', strtotime($log['created_at'])) ?>
                    </small>
                    <br>
                    <strong><?= date('H:i:s', strtotime($log['created_at'])) ?></strong>
                </td>
                <td>
                    <?php if ($log['user_name']): ?>
                        <strong><?= e($log['user_name']) ?></strong>
                        <?php if ($log['user_email']): ?>
                        <br><small style="color: var(--text-muted);"><?= e($log['user_email']) ?></small>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color: var(--text-muted);">Sistema</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $actionColors = [
                        'create' => 'success',
                        'update' => 'info',
                        'delete' => 'danger',
                        'login' => 'success',
                        'login_failed' => 'warning',
                        'logout' => 'secondary',
                        'status_change' => 'info',
                        'approve' => 'success',
                        'reject' => 'danger',
                        'settings_update' => 'info',
                    ];
                    $actionColor = $actionColors[$log['action']] ?? 'secondary';
                    ?>
                    <span class="badge badge-<?= $actionColor ?>"><?= e(AuditLog::translateAction($log['action'])) ?></span>
                </td>
                <td>
                    <?php if ($log['entity_type']): ?>
                        <span style="font-size: 0.85em;"><?= e(AuditLog::translateEntityType($log['entity_type'])) ?></span>
                        <?php if ($log['entity_id']): ?>
                        <br><small style="color: var(--text-muted);">#<?= (int) $log['entity_id'] ?></small>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color: var(--text-muted);">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($log['entity_name']): ?>
                        <strong><?= e(mb_strlen($log['entity_name']) > 40 ? mb_substr($log['entity_name'], 0, 40) . '...' : $log['entity_name']) ?></strong>
                        <br>
                    <?php endif; ?>
                    <span style="font-size: 0.9em; color: var(--text-muted);">
                        <?= e(mb_strlen($log['description'] ?? '') > 80 ? mb_substr($log['description'], 0, 80) . '...' : ($log['description'] ?? '-')) ?>
                    </span>
                </td>
                <td>
                    <?php
                    $resultColors = ['success' => 'success', 'failure' => 'danger', 'warning' => 'warning'];
                    $resultIcons = [
                        'success' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
                        'failure' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
                        'warning' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                    ];
                    ?>
                    <span class="badge badge-<?= $resultColors[$log['result']] ?? 'secondary' ?>" title="<?= e(AuditLog::translateResult($log['result'])) ?>">
                        <?= $resultIcons[$log['result']] ?? '' ?>
                    </span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline" onclick="showLogDetails(<?= (int) $log['id'] ?>)" title="Ver detalhes">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Paginação -->
<?php if (!empty($logs['total_pages']) && $logs['total_pages'] > 1): ?>
<div class="pagination" style="margin-top: 1rem;">
    <?php
    $totalPages = $logs['total_pages'];
    $currentPage = $logs['current_page'];
    $queryParams = $filters;
    
    // Primeira página
    if ($currentPage > 2): ?>
    <a href="?<?= http_build_query(array_merge($queryParams, ['page' => 1])) ?>" class="pagination-btn" title="Primeira página">&laquo;</a>
    <?php endif; ?>
    
    <?php
    // Páginas ao redor da atual
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    for ($p = $start; $p <= $end; $p++): ?>
    <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $p])) ?>" 
       class="pagination-btn <?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
    
    <?php if ($currentPage < $totalPages - 1): ?>
    <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $totalPages])) ?>" class="pagination-btn" title="Última página">&raquo;</a>
    <?php endif; ?>
    
    <span style="margin-left: 1rem; color: var(--text-muted); font-size: 0.9em;">
        Página <?= $currentPage ?> de <?= $totalPages ?> 
        (<?= number_format($logs['total'], 0, ',', '.') ?> registros)
    </span>
</div>
<?php endif; ?>

<!-- Modal para Detalhes -->
<div id="logDetailsModal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeLogDetails()"></div>
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Detalhes do Log</h3>
            <button type="button" class="modal-close" onclick="closeLogDetails()">&times;</button>
        </div>
        <div class="modal-body" id="logDetailsContent">
            <div class="loading" style="text-align: center; padding: 2rem;">
                Carregando...
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}
.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}
.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--primary);
}
.stat-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}
.table-responsive {
    overflow-x: auto;
}
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}
.modal-content {
    position: relative;
    background: var(--card-bg);
    border-radius: 8px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}
.modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-muted);
    line-height: 1;
}
.modal-close:hover {
    color: var(--danger);
}
.modal-body {
    padding: 1.5rem;
}
.detail-row {
    display: flex;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}
.detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.detail-label {
    flex: 0 0 140px;
    font-weight: 600;
    color: var(--text-muted);
}
.detail-value {
    flex: 1;
}
.changes-block {
    background: var(--bg);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 1rem;
    margin-top: 0.5rem;
    font-family: monospace;
    font-size: 0.85rem;
    white-space: pre-wrap;
    word-break: break-all;
    max-height: 300px;
    overflow-y: auto;
}
</style>

<script>
function showLogDetails(logId) {
    document.getElementById('logDetailsModal').style.display = 'flex';
    document.getElementById('logDetailsContent').innerHTML = '<div class="loading" style="text-align: center; padding: 2rem;">Carregando...</div>';
    
    fetch('/admin/audit-logs/' + logId, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('logDetailsContent').innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
            return;
        }
        
        const log = data.log;
        let changesHtml = '-';
        if (log.changes_decoded) {
            changesHtml = '<div class="changes-block">' + JSON.stringify(log.changes_decoded, null, 2) + '</div>';
        }
        
        const html = `
            <div class="detail-row">
                <div class="detail-label">ID</div>
                <div class="detail-value">#${log.id}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Data/Hora</div>
                <div class="detail-value">${formatDate(log.created_at)}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Usuário</div>
                <div class="detail-value">${log.user_name || '-'} ${log.user_email ? '<br><small style="color: var(--text-muted);">' + log.user_email + '</small>' : ''}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Ação</div>
                <div class="detail-value"><span class="badge badge-info">${log.action_label}</span></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Tipo de Entidade</div>
                <div class="detail-value">${log.entity_type_label}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">ID da Entidade</div>
                <div class="detail-value">${log.entity_id || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Nome da Entidade</div>
                <div class="detail-value">${log.entity_name || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Descrição</div>
                <div class="detail-value">${log.description || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Resultado</div>
                <div class="detail-value"><span class="badge badge-${log.result === 'success' ? 'success' : (log.result === 'failure' ? 'danger' : 'warning')}">${log.result_label}</span></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Endereço IP</div>
                <div class="detail-value">${log.ip_address || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">User Agent</div>
                <div class="detail-value"><small style="word-break: break-all;">${log.user_agent || '-'}</small></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Alterações</div>
                <div class="detail-value">${changesHtml}</div>
            </div>
        `;
        
        document.getElementById('logDetailsContent').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('logDetailsContent').innerHTML = '<div class="alert alert-danger">Erro ao carregar detalhes.</div>';
    });
}

function closeLogDetails() {
    document.getElementById('logDetailsModal').style.display = 'none';
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('pt-BR') + ' às ' + d.toLocaleTimeString('pt-BR');
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogDetails();
    }
});
</script>
