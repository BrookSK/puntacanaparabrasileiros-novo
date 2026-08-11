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
                <button onclick="syncGroups()" title="Sincronizar grupos">↻</button>
                <button onclick="openQuickReplies()" title="Respostas rápidas">⚡</button>
                <a href="/whatsapp" title="Instâncias">⚙️</a>
                <a href="/crm" title="CRM">📊</a>
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
                    <button onclick="toggleDetails()" title="Detalhes">👤</button>
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
                    <button class="wpp-emoji-btn" onclick="toggleEmojiPicker()">😊</button>
                    <button class="wpp-attach-btn" onclick="document.getElementById('fileInput').click()">📎</button>
                    <input type="file" id="fileInput" style="display:none" onchange="stageFile(this)">
                    <textarea id="messageInput" placeholder="Digite uma mensagem..." rows="1"></textarea>
                    <button class="wpp-send-btn" onclick="sendMessage()">➤</button>
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
            <button class="btn btn-outline btn-block" onclick="openBriefing()">📋 Briefing comercial</button>

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
            <h3>⚡ Respostas Rápidas</h3>
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
