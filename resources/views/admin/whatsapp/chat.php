<?php if (!empty($noInstance)): ?>
<div class="empty-state">
    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
    <p>Nenhuma instância WhatsApp conectada.</p>
    <p style="font-size:13px;">Configure e conecte uma instância para começar a usar o chat.</p>
    <a href="/whatsapp" class="btn btn-primary"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg> Configurar Instância</a>
</div>
<?php return; endif; ?>

<div class="wpp-chat-wrapper" id="chatApp"
     data-instance-id="<?= e((string)($instance['id'] ?? '')) ?>"
     data-contact-id="<?= e((string)($contactId ?? '')) ?>"
     data-user-id="<?= e((string)($currentUser['id'] ?? '')) ?>"
     data-user-name="<?= e($currentUser['first_name'] ?? '') ?>">

    <!-- COLUNA ESQUERDA: Lista de Contatos -->
    <aside class="wpp-sidebar" id="wppSidebar">
        <div class="wpp-sidebar-header">
            <h3><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg> Chat</h3>
            <div class="wpp-sidebar-btns">
                <button onclick="syncGroups()" title="Sincronizar grupos"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg></button>
                <button onclick="openQuickReplies()" title="Respostas rápidas"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></button>
                <a href="/whatsapp" title="Instâncias"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.6V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg></a>
                <a href="/crm" title="CRM"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></a>
            </div>
        </div>

        <button class="wpp-btn-new-chat" onclick="openNewConversation()">+ Iniciar conversa</button>

        <div class="wpp-search">
            <input type="text" id="searchContacts" placeholder="Buscar contato ou grupo..." oninput="filterContacts()">
        </div>

        <div class="wpp-filters">
            <select id="filterAssigned" onchange="filterContacts()">
                <option value="">Todos</option>
                <option value="none">Sem dono</option>
                <?php foreach ($teamMembers as $m): ?>
                <option value="<?= $m['id'] ?>"><?= e($m['first_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterLabel" onchange="filterContacts()">
                <option value="">Etiqueta</option>
                <?php foreach ($labels as $l): ?>
                <option value="<?= $l['id'] ?>"><?= e($l['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterStatus" onchange="filterContacts()">
                <option value="">Status</option>
                <option value="novo">Novo</option>
                <option value="em_atendimento">Em atendimento</option>
                <option value="aguardando">Aguardando</option>
                <option value="concluido">Concluído</option>
            </select>
        </div>

        <div class="wpp-tabs">
            <button class="wpp-tab active" data-tab="contacts" onclick="switchTab('contacts')">
                Contatos <span id="countContacts" class="tab-count">0</span>
            </button>
            <button class="wpp-tab" data-tab="groups" onclick="switchTab('groups')">
                Grupos <span id="countGroups" class="tab-count">0</span>
            </button>
        </div>

        <div class="wpp-contact-list" id="contactList">
            <!-- Preenchido via JS -->
        </div>
    </aside>

    <!-- COLUNA CENTRAL: Área de Chat -->
    <main class="wpp-main" id="wppMain">
        <div class="wpp-empty-chat" id="emptyChat">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="1.5"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
            <p>Selecione um contato para iniciar</p>
        </div>

        <div class="wpp-chat-active" id="chatActive" style="display:none;">
            <!-- Header do chat -->
            <div class="wpp-chat-header" id="chatHeader">
                <button class="wpp-back-btn" onclick="closeChat()">←</button>
                <div class="wpp-chat-header-info" onclick="toggleDetails()">
                    <div class="wpp-avatar" id="chatAvatar"></div>
                    <div>
                        <strong id="chatName">—</strong>
                        <small id="chatPhone"></small>
                    </div>
                </div>
                <div class="wpp-chat-header-actions">
                    <label class="wpp-sign-toggle" title="Assinar mensagens com seu nome">
                        <input type="checkbox" id="signToggle"> Assinar
                    </label>
                    <select id="serviceStatusSelect" onchange="updateServiceStatus()">
                        <option value="novo">Novo</option>
                        <option value="em_atendimento">Em atendimento</option>
                        <option value="aguardando">Aguardando</option>
                        <option value="concluido">Concluído</option>
                    </select>
                    <button onclick="toggleDetails()" title="Detalhes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></button>
                </div>
            </div>

            <!-- Mensagens -->
            <div class="wpp-messages" id="messagesContainer">
                <div class="wpp-messages-inner" id="messagesList">
                    <!-- Preenchido via JS -->
                </div>
            </div>

            <!-- Input de mensagem -->
            <div class="wpp-input-area">
                <div class="wpp-media-stage" id="mediaStage" style="display:none;">
                    <span id="mediaPreview"></span>
                    <span id="mediaName"></span>
                    <span id="mediaSize"></span>
                    <button onclick="cancelMedia()">×</button>
                </div>
                <div class="wpp-input-bar">
                    <button class="wpp-emoji-btn" onclick="toggleEmojiPicker()"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></button>
                    <button class="wpp-attach-btn" onclick="document.getElementById('fileInput').click()"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg></button>
                    <input type="file" id="fileInput" style="display:none" onchange="stageFile(this)">
                    <textarea id="messageInput" placeholder="Digite uma mensagem..." rows="1"></textarea>
                    <button class="wpp-send-btn" onclick="sendMessage()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg></button>
                </div>
                <div class="wpp-emoji-picker" id="emojiPicker" style="display:none;"></div>
                <div class="wpp-quick-reply-dropdown" id="quickReplyDropdown" style="display:none;"></div>
            </div>
        </div>
    </main>

    <!-- COLUNA DIREITA: Detalhes do Contato -->
    <aside class="wpp-details" id="wppDetails" style="display:none;">
        <div class="wpp-details-header">
            <h4>Detalhes</h4>
            <button onclick="toggleDetails()">×</button>
        </div>
        <div class="wpp-details-body">
            <div class="wpp-details-avatar" id="detailAvatar"></div>
            <div class="form-group">
                <label>Nome</label>
                <input type="text" id="detailName" class="form-control">
            </div>
            <div class="form-group">
                <label>Atribuído a</label>
                <select id="detailAssigned" class="form-control">
                    <option value="">Ninguém</option>
                    <?php foreach ($teamMembers as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Observações internas</label>
                <textarea id="detailNotes" class="form-control" rows="3"></textarea>
            </div>
            <button class="btn btn-primary btn-block" onclick="saveContactDetails()">Salvar</button>

            <hr>
            <h5>Etiquetas</h5>
            <div id="detailLabels" class="wpp-labels-list"></div>
            <div class="wpp-add-label">
                <select id="addLabelSelect" class="form-control">
                    <option value="">Selecionar etiqueta...</option>
                    <?php foreach ($labels as $l): ?>
                    <option value="<?= $l['id'] ?>" data-color="<?= e($l['color']) ?>"><?= e($l['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-outline" onclick="addLabel()">+</button>
                <button class="btn btn-sm btn-outline" onclick="openCreateLabel()">Nova</button>
            </div>

            <hr>
            <button class="btn btn-outline btn-block" onclick="openBriefing()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Briefing comercial</button>

            <hr>
            <h5>CRM</h5>
            <select id="crmBoardSelect" class="form-control" onchange="loadCrmColumns()">
                <option value="">Selecionar board...</option>
            </select>
            <select id="crmColumnSelect" class="form-control" style="margin-top:8px;" disabled>
                <option value="">Selecionar coluna...</option>
            </select>
            <button class="btn btn-sm btn-primary" style="margin-top:8px;" onclick="addToCrm()">Adicionar ao CRM</button>

            <hr>
            <?php if (is_superadmin()): ?>
            <button class="btn btn-danger btn-block btn-sm" onclick="deleteContactPermanently()">Excluir contato permanentemente</button>
            <?php endif; ?>
        </div>
    </aside>
</div>

<!-- Lightbox de Imagem -->
<div class="wpp-lightbox" id="img-lightbox" onclick="closeLightbox(event)">
    <button class="wpp-lightbox-close" onclick="closeLightbox(event)">×</button>
    <img id="img-lightbox-img" src="" alt="Imagem">
</div>

<!-- Modal: Nova Conversa -->
<div class="modal-overlay" id="modal-new-conversation" style="display:none;">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Iniciar Conversa</h3>
            <button class="modal-close" onclick="closeModal('modal-new-conversation')">&times;</button>
        </div>
        <form onsubmit="return startConversation(event)">
            <div class="form-group">
                <label>Número (com DDD) *</label>
                <input type="text" id="newConvPhone" class="form-control" placeholder="5517999999999" required>
            </div>
            <div class="form-group">
                <label>Nome (opcional)</label>
                <input type="text" id="newConvName" class="form-control" placeholder="Nome do contato">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-new-conversation')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Iniciar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Briefing Comercial -->
<div class="modal-overlay" id="modal-briefing" style="display:none;">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3>Briefing Comercial</h3>
            <button class="modal-close" onclick="closeModal('modal-briefing')">&times;</button>
        </div>
        <form onsubmit="return saveBriefing(event)">
            <div class="modal-body-scroll">
                <div class="form-row">
                    <div class="form-group"><label>Necessidade do lead</label><textarea id="bf-need" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label>Principal dor/problema</label><textarea id="bf-pain" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Solução atual</label><textarea id="bf-solution" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label>Objetivo esperado</label><textarea id="bf-goal" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Urgência</label>
                        <select id="bf-urgency" class="form-control">
                            <option value="">—</option>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                            <option value="Urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Faixa de investimento (R$)</label><input type="text" id="bf-investment" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nível de decisão</label>
                        <select id="bf-decision" class="form-control">
                            <option value="">—</option>
                            <option value="Decisor">Decisor</option>
                            <option value="Influenciador">Influenciador</option>
                            <option value="Usuário">Usuário</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Sem influência">Sem influência</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Temperatura do lead</label>
                        <select id="bf-temperature" class="form-control">
                            <option value="">—</option>
                            <option value="frio">Frio</option>
                            <option value="morno">Morno</option>
                            <option value="quente">Quente</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Data do próximo contato</label><input type="date" id="bf-next-date" class="form-control"></div>
                    <div class="form-group"><label>Principal objeção</label><textarea id="bf-objection" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="form-group"><label>Próximo passo combinado</label><textarea id="bf-next-step" class="form-control" rows="2"></textarea></div>
                <div class="form-group"><label>Observações importantes</label><textarea id="bf-notes" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-briefing')">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar Briefing</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Respostas Rápidas -->
<div class="modal-overlay" id="modal-quick-replies" style="display:none;">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> Respostas Rápidas</h3>
            <button class="modal-close" onclick="closeModal('modal-quick-replies')">&times;</button>
        </div>
        <div class="modal-body-scroll">
            <details class="qr-form-toggle">
                <summary>+ Nova resposta rápida</summary>
                <form onsubmit="return saveQuickReply(event)" id="form-quick-reply">
                    <div class="form-row">
                        <div class="form-group"><label>Atalho (sem /)</label><input type="text" id="qr-shortcut" class="form-control" placeholder="bomdia" required></div>
                        <div class="form-group"><label>Anexo (opcional)</label><input type="file" id="qr-attachment" class="form-control"></div>
                    </div>
                    <div class="form-group"><label>Mensagem</label><textarea id="qr-message" class="form-control" rows="3"></textarea></div>
                    <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                </form>
            </details>
            <hr>
            <div id="quickRepliesList"></div>
        </div>
    </div>
</div>

<!-- Modal: Criar Etiqueta -->
<div class="modal-overlay" id="modal-create-label" style="display:none;">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Nova Etiqueta</h3>
            <button class="modal-close" onclick="closeModal('modal-create-label')">&times;</button>
        </div>
        <form onsubmit="return createNewLabel(event)">
            <div class="form-group"><label>Nome</label><input type="text" id="newLabelName" class="form-control" required></div>
            <div class="form-group"><label>Cor</label><input type="color" id="newLabelColor" class="form-control" value="#6c757d"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Criar</button>
            </div>
        </form>
    </div>
</div>
