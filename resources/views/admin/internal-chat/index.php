<?php /** @var array $currentUser; @var array $teamMembers; @var int|null $openConversationId */ ?>

<div class="ic-wrapper">
    <!-- Sidebar: lista de conversas -->
    <aside class="ic-sidebar">
        <div class="ic-sidebar-head">
            <h3>Conversas</h3>
            <div class="ic-new-actions">
                <button type="button" class="ic-btn-icon" id="icNewDirect" title="Nova conversa">＋</button>
                <button type="button" class="ic-btn-icon" id="icNewGroup" title="Novo grupo">👥</button>
            </div>
        </div>
        <div class="ic-conv-list" id="icConvList">
            <div class="ic-empty">Carregando...</div>
        </div>
    </aside>

    <!-- Painel de mensagens -->
    <section class="ic-chat" id="icChat">
        <div class="ic-chat-empty" id="icChatEmpty">
            <div>
                <div style="font-size:42px;margin-bottom:10px;">💬</div>
                <p>Selecione uma conversa ou inicie uma nova.</p>
            </div>
        </div>

        <div class="ic-chat-active" id="icChatActive" style="display:none;">
            <header class="ic-chat-head">
                <div>
                    <strong id="icChatTitle"></strong>
                    <span class="ic-chat-sub" id="icChatSub"></span>
                </div>
                <button type="button" class="ic-btn-icon" id="icAddParticipant" title="Adicionar participante" style="display:none;">＋👤</button>
            </header>

            <div class="ic-messages" id="icMessages"></div>

            <form class="ic-composer" id="icComposer">
                <input type="text" id="icInput" placeholder="Escreva uma mensagem..." autocomplete="off">
                <button type="submit" class="ic-send">Enviar</button>
            </form>
        </div>
    </section>
</div>

<!-- Modal: nova conversa direta -->
<div class="ic-modal-overlay" id="icModalDirect">
    <div class="ic-modal">
        <h3>Nova conversa</h3>
        <p>Escolha um membro da equipe:</p>
        <select id="icDirectUser" class="form-control">
            <option value="">— Selecione —</option>
            <?php foreach ($teamMembers as $m): ?>
            <option value="<?= (int)$m['id'] ?>"><?= e(trim($m['first_name'] . ' ' . $m['last_name'])) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="ic-modal-actions">
            <button type="button" class="btn btn-outline" data-close-modal>Cancelar</button>
            <button type="button" class="btn btn-primary" id="icDirectConfirm">Iniciar</button>
        </div>
    </div>
</div>

<!-- Modal: novo grupo -->
<div class="ic-modal-overlay" id="icModalGroup">
    <div class="ic-modal">
        <h3>Novo grupo</h3>
        <label class="ic-label">Nome do grupo</label>
        <input type="text" id="icGroupTitle" class="form-control" placeholder="Ex: Atendimento VIP">
        <label class="ic-label" style="margin-top:12px;">Participantes</label>
        <div class="ic-checklist">
            <?php foreach ($teamMembers as $m): ?>
            <label class="ic-check"><input type="checkbox" class="ic-group-member" value="<?= (int)$m['id'] ?>"> <?= e(trim($m['first_name'] . ' ' . $m['last_name'])) ?></label>
            <?php endforeach; ?>
        </div>
        <div class="ic-modal-actions">
            <button type="button" class="btn btn-outline" data-close-modal>Cancelar</button>
            <button type="button" class="btn btn-primary" id="icGroupConfirm">Criar grupo</button>
        </div>
    </div>
</div>

<!-- Modal: adicionar participante -->
<div class="ic-modal-overlay" id="icModalAdd">
    <div class="ic-modal">
        <h3>Adicionar participante</h3>
        <p>Traga outro membro para esta conversa:</p>
        <select id="icAddUser" class="form-control">
            <option value="">— Selecione —</option>
            <?php foreach ($teamMembers as $m): ?>
            <option value="<?= (int)$m['id'] ?>"><?= e(trim($m['first_name'] . ' ' . $m['last_name'])) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="ic-modal-actions">
            <button type="button" class="btn btn-outline" data-close-modal>Cancelar</button>
            <button type="button" class="btn btn-primary" id="icAddConfirm">Adicionar</button>
        </div>
    </div>
</div>

<style>
.ic-wrapper{display:flex;gap:0;height:calc(100vh - 160px);min-height:480px;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;background:#fff}
.ic-sidebar{width:300px;flex-shrink:0;border-right:1px solid #e5e7eb;display:flex;flex-direction:column;background:#fafafa}
.ic-sidebar-head{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #e5e7eb}
.ic-sidebar-head h3{margin:0;font-size:16px;color:#0f172a}
.ic-new-actions{display:flex;gap:6px}
.ic-btn-icon{background:#1B6F00;color:#fff;border:none;border-radius:8px;width:32px;height:32px;cursor:pointer;font-size:15px;line-height:1}
.ic-btn-icon:hover{background:#155700}
.ic-conv-list{flex:1;overflow-y:auto}
.ic-conv-item{display:flex;flex-direction:column;gap:2px;padding:12px 16px;border-bottom:1px solid #f0f0f0;cursor:pointer}
.ic-conv-item:hover{background:#f0fdf4}
.ic-conv-item.active{background:#dcfce7}
.ic-conv-top{display:flex;justify-content:space-between;align-items:center}
.ic-conv-name{font-weight:600;font-size:14px;color:#0f172a}
.ic-conv-badge{background:#1B6F00;color:#fff;border-radius:10px;font-size:11px;padding:1px 7px;min-width:18px;text-align:center}
.ic-conv-last{font-size:12px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:230px}
.ic-conv-tag{font-size:10px;color:#166534;background:#dcfce7;border-radius:4px;padding:1px 6px;display:inline-block;width:fit-content;margin-top:2px}
.ic-empty{padding:20px;text-align:center;color:#94a3b8;font-size:13px}
.ic-chat{flex:1;display:flex;flex-direction:column;min-width:0}
.ic-chat-empty{flex:1;display:flex;align-items:center;justify-content:center;color:#94a3b8;text-align:center}
.ic-chat-active{flex:1;display:flex;flex-direction:column;min-height:0}
.ic-chat-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #e5e7eb}
.ic-chat-head strong{display:block;font-size:15px;color:#0f172a}
.ic-chat-sub{font-size:12px;color:#64748b}
.ic-messages{flex:1;overflow-y:auto;padding:18px;background:#f7faf7;display:flex;flex-direction:column;gap:8px}
.ic-msg{max-width:70%;padding:8px 12px;border-radius:12px;font-size:14px;line-height:1.4;word-wrap:break-word}
.ic-msg.me{align-self:flex-end;background:#dcfce7;border-bottom-right-radius:3px}
.ic-msg.other{align-self:flex-start;background:#fff;border:1px solid #e5e7eb;border-bottom-left-radius:3px}
.ic-msg-author{font-size:11px;font-weight:600;color:#1B6F00;margin-bottom:2px}
.ic-msg-time{font-size:10px;color:#94a3b8;margin-top:2px;text-align:right}
.ic-msg-system{align-self:center;background:#eef2f7;color:#64748b;font-size:12px;padding:4px 12px;border-radius:20px;max-width:80%}
.ic-composer{display:flex;gap:8px;padding:12px 16px;border-top:1px solid #e5e7eb;background:#fff}
.ic-composer input{flex:1;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:22px;font-size:14px}
.ic-composer input:focus{outline:none;border-color:#1B6F00}
.ic-send{background:#1B6F00;color:#fff;border:none;border-radius:22px;padding:0 20px;font-weight:600;cursor:pointer}
.ic-send:hover{background:#155700}
.ic-modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);display:none;align-items:center;justify-content:center;z-index:9999;padding:16px}
.ic-modal-overlay.open{display:flex}
.ic-modal{background:#fff;border-radius:12px;max-width:420px;width:100%;padding:24px}
.ic-modal h3{margin:0 0 8px;font-size:18px}
.ic-modal p{margin:0 0 12px;font-size:13.5px;color:#64748b}
.ic-label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px}
.ic-checklist{max-height:220px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:8px}
.ic-check{display:flex;align-items:center;gap:8px;padding:6px 4px;font-size:14px;cursor:pointer}
.ic-modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:18px}
@media(max-width:720px){.ic-sidebar{width:120px}.ic-conv-last{display:none}}
</style>

<script>
(function(){
    var CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '<?= e(csrf_token()) ?>';
    var ME = <?= (int)$currentUser['id'] ?>;
    var STATE = { convId: null, convType: null, lastId: 0, seen: {}, pollTimer: null, listTimer: null };

    function esc(s){ var d=document.createElement('div'); d.textContent = s==null?'':s; return d.innerHTML; }
    function timeFmt(ts){ if(!ts) return ''; var d=new Date(ts.replace(' ','T')); return isNaN(d)?'':(d.getHours()+':'+String(d.getMinutes()).padStart(2,'0')); }

    // ── Lista de conversas ──
    function loadConversations(){
        fetch('/chat-interno/conversations', {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(data){ renderConversations(data.conversations||[]); })
            .catch(function(){});
    }
    function renderConversations(list){
        var el = document.getElementById('icConvList');
        if (!list.length){ el.innerHTML = '<div class="ic-empty">Nenhuma conversa ainda.<br>Inicie uma nova acima.</div>'; return; }
        var html = '';
        list.forEach(function(c){
            var last = c.last_message ? (c.last_message.message_type==='system' ? c.last_message.body : ((c.last_message.first_name?c.last_message.first_name+': ':'')+c.last_message.body)) : '';
            var tag = c.related_contact ? '<span class="ic-conv-tag">Cliente: '+esc(c.related_contact.contact_name||c.related_contact.push_name||c.related_contact.phone||'')+'</span>' : '';
            html += '<div class="ic-conv-item'+(c.id==STATE.convId?' active':'')+'" data-id="'+c.id+'" data-type="'+c.type+'">'
                 + '<div class="ic-conv-top"><span class="ic-conv-name">'+esc(c.display_title)+'</span>'
                 + (c.unread>0?'<span class="ic-conv-badge">'+c.unread+'</span>':'')+'</div>'
                 + '<span class="ic-conv-last">'+esc(last)+'</span>'
                 + tag
                 + '</div>';
        });
        el.innerHTML = html;
        el.querySelectorAll('.ic-conv-item').forEach(function(it){
            it.addEventListener('click', function(){ openConversation(parseInt(it.dataset.id), it.dataset.type); });
        });
    }

    // ── Abrir conversa ──
    function openConversation(id, type){
        STATE.convId = id; STATE.convType = type; STATE.lastId = 0; STATE.seen = {};
        document.getElementById('icChatEmpty').style.display = 'none';
        document.getElementById('icChatActive').style.display = 'flex';
        document.getElementById('icAddParticipant').style.display = (type==='group')?'block':'none';
        document.getElementById('icMessages').innerHTML = '';
        loadConversations();

        fetch('/chat-interno/messages/'+id, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(data){
                var parts = (data.participants||[]).map(function(p){ return esc((p.first_name||'')+' '+(p.last_name||'')); }).join(', ');
                var title = 'Conversa';
                var convEl = document.querySelector('.ic-conv-item[data-id="'+id+'"] .ic-conv-name');
                if (convEl) title = convEl.textContent;
                document.getElementById('icChatTitle').textContent = title;
                document.getElementById('icChatSub').textContent = parts;
                (data.messages||[]).forEach(appendMessage);
                scrollBottom();
                startPoll();
            });
    }

    function appendMessage(m){
        if (STATE.seen[m.id]) return;
        STATE.seen[m.id] = true;
        if (m.id > STATE.lastId) STATE.lastId = m.id;
        var box = document.getElementById('icMessages');
        var html;
        if (m.message_type === 'system'){
            html = '<div class="ic-msg-system">'+esc(m.body)+'</div>';
        } else {
            var mine = parseInt(m.user_id) === ME;
            html = '<div class="ic-msg '+(mine?'me':'other')+'">'
                 + (mine?'':'<div class="ic-msg-author">'+esc((m.first_name||'')+' '+(m.last_name||''))+'</div>')
                 + esc(m.body)
                 + '<div class="ic-msg-time">'+timeFmt(m.created_at)+'</div></div>';
        }
        box.insertAdjacentHTML('beforeend', html);
    }
    function scrollBottom(){ var b=document.getElementById('icMessages'); b.scrollTop=b.scrollHeight; }

    function startPoll(){
        clearInterval(STATE.pollTimer);
        STATE.pollTimer = setInterval(function(){
            if (!STATE.convId) return;
            fetch('/chat-interno/poll/'+STATE.convId+'?after_id='+STATE.lastId, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(function(r){return r.json();})
                .then(function(data){
                    if (data.messages && data.messages.length){ data.messages.forEach(appendMessage); scrollBottom(); }
                }).catch(function(){});
        }, 4000);
    }

    // ── Enviar ──
    document.getElementById('icComposer').addEventListener('submit', function(e){
        e.preventDefault();
        var input = document.getElementById('icInput');
        var text = input.value.trim();
        if (!text || !STATE.convId) return;
        input.value = '';
        fetch('/chat-interno/send', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
            body:'conversation_id='+STATE.convId+'&message='+encodeURIComponent(text)
        })
        .then(function(r){return r.json();})
        .then(function(){
            // Puxa a própria mensagem via poll imediato
            fetch('/chat-interno/poll/'+STATE.convId+'?after_id='+STATE.lastId, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(function(r){return r.json();})
                .then(function(data){ if(data.messages){ data.messages.forEach(appendMessage); scrollBottom(); } loadConversations(); });
        });
    });

    // ── Modais ──
    function openModal(id){ document.getElementById(id).classList.add('open'); }
    function closeModals(){ document.querySelectorAll('.ic-modal-overlay').forEach(function(m){ m.classList.remove('open'); }); }
    document.querySelectorAll('[data-close-modal]').forEach(function(b){ b.addEventListener('click', closeModals); });
    document.querySelectorAll('.ic-modal-overlay').forEach(function(o){ o.addEventListener('click', function(e){ if(e.target===o) closeModals(); }); });

    document.getElementById('icNewDirect').addEventListener('click', function(){ openModal('icModalDirect'); });
    document.getElementById('icNewGroup').addEventListener('click', function(){ openModal('icModalGroup'); });
    document.getElementById('icAddParticipant').addEventListener('click', function(){ openModal('icModalAdd'); });

    document.getElementById('icDirectConfirm').addEventListener('click', function(){
        var uid = document.getElementById('icDirectUser').value;
        if (!uid) return;
        fetch('/chat-interno/createConversation', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':CSRF},
            body:'type=direct&user_id='+encodeURIComponent(uid)
        }).then(function(r){return r.json();}).then(function(data){
            closeModals();
            if (data.success){ loadConversations(); openConversation(data.id, 'direct'); }
            else alert(data.error||'Erro.');
        });
    });

    document.getElementById('icGroupConfirm').addEventListener('click', function(){
        var title = document.getElementById('icGroupTitle').value.trim();
        var members = Array.prototype.slice.call(document.querySelectorAll('.ic-group-member:checked')).map(function(c){return c.value;});
        if (!title){ alert('Informe o nome do grupo.'); return; }
        if (!members.length){ alert('Selecione ao menos um participante.'); return; }
        var body = 'type=group&title='+encodeURIComponent(title);
        members.forEach(function(m){ body += '&members[]='+encodeURIComponent(m); });
        fetch('/chat-interno/createConversation', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':CSRF},
            body: body
        }).then(function(r){return r.json();}).then(function(data){
            closeModals();
            if (data.success){ loadConversations(); openConversation(data.id, 'group'); }
            else alert(data.error||'Erro.');
        });
    });

    document.getElementById('icAddConfirm').addEventListener('click', function(){
        var uid = document.getElementById('icAddUser').value;
        if (!uid || !STATE.convId) return;
        fetch('/chat-interno/addParticipant', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':CSRF},
            body:'conversation_id='+STATE.convId+'&user_id='+encodeURIComponent(uid)
        }).then(function(r){return r.json();}).then(function(data){
            closeModals();
            if (!data.success) alert(data.error||'Erro.');
        });
    });

    // ── Init ──
    loadConversations();
    STATE.listTimer = setInterval(loadConversations, 8000);
    <?php if (!empty($openConversationId)): ?>
    setTimeout(function(){ openConversation(<?= (int)$openConversationId ?>, 'group'); }, 400);
    <?php endif; ?>
})();
</script>
