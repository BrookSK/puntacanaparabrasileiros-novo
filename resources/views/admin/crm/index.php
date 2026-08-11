<div class="admin-page-header">
    <h2>CRM</h2>
    <div>
        <a href="/whatsapp/chat" class="btn btn-outline"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg> Chat</a>
        <a href="/crm/dashboard" class="btn btn-outline"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg> Dashboard</a>
        <a href="/crm/commissions" class="btn btn-outline"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg> Comissões</a>
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
