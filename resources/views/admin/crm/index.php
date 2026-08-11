<div class="admin-page-header">
    <h2>CRM</h2>
    <div>
        <a href="/whatsapp/chat" class="btn btn-outline">💬 Chat</a>
        <a href="/crm/dashboard" class="btn btn-outline">📊 Dashboard</a>
        <a href="/crm/commissions" class="btn btn-outline">💰 Comissões</a>
        <button class="btn btn-primary" onclick="document.getElementById('modal-new-board').style.display='flex'">+ Novo Board</button>
    </div>
</div>

<div class="crm-boards-grid">
    <?php if (empty($boards)): ?>
    <div class="empty-state">
        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        <p>Nenhum board CRM criado ainda.</p>
        <button class="btn btn-primary" onclick="document.getElementById('modal-new-board').style.display='flex'">Criar primeiro board</button>
    </div>
    <?php else: ?>
    <?php foreach ($boards as $board): ?>
    <a href="/crm/board/<?= $board['id'] ?>" class="crm-board-card">
        <h3><?= e($board['name']) ?></h3>
        <span class="badge badge-info"><?= (int)($board['card_count'] ?? 0) ?> cards</span>
        <?php if ($board['description']): ?>
        <p><?= e(truncate($board['description'], 80)) ?></p>
        <?php endif; ?>
        <small>Criado por <?= e($board['creator_name'] ?? 'Sistema') ?> · <?= time_ago($board['created_at']) ?></small>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Novo Board -->
<div class="modal-overlay" id="modal-new-board" style="display:none;">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Novo Board CRM</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">&times;</button>
        </div>
        <form onsubmit="return createBoard(event)">
            <div class="form-group">
                <label>Nome *</label>
                <input type="text" id="boardName" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea id="boardDesc" class="form-control" rows="2"></textarea>
            </div>
            <p class="form-help">Colunas padrão serão criadas automaticamente: Novo Lead, Contato Feito, Em Negociação, Fechado, Perdido.</p>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Board</button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
async function createBoard(e) {
    e.preventDefault();
    const res = await fetch('/crm/createBoard', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify({name: document.getElementById('boardName').value, description: document.getElementById('boardDesc').value})
    });
    const json = await res.json();
    if (json.success) window.location.href = '/crm/board/' + json.id;
    else alert(json.error || 'Erro');
    return false;
}
</script>
