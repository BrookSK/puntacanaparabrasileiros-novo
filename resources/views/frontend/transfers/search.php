<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Busque seu Transfer e Reserve Agora!</h1>
            <p>Transporte seguro e confortável para seu destino.</p>
        </div>
    </div>
</section>

<section class="section section-transfer-search">
    <div class="container">
        <!-- Formulário de Busca (caixa verde) -->
        <div class="transfer-search-box" id="transferSearchForm">
            <!-- Tabs -->
            <div class="transfer-tabs">
                <button class="transfer-tab active" data-tab="roundtrip" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
                    Ida e Volta
                </button>
                <button class="transfer-tab" data-tab="oneway" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    Somente Ida
                </button>
                <button class="transfer-tab" data-tab="multiple" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Múltiplos Transfers
                </button>
            </div>

            <!-- Form Ida e Volta / Somente Ida -->
            <div class="transfer-form-content" id="tabRoundtrip">
                <div class="transfer-form-row">
                    <div class="tf-field">
                        <label>ORIGEM</label>
                        <div class="tf-autocomplete">
                            <input type="text" class="tf-input tf-autocomplete-input" id="originInput" placeholder="Digite para buscar..." autocomplete="off">
                            <input type="hidden" name="origin_id" id="originSelect">
                            <div class="tf-autocomplete-list" id="originList"></div>
                        </div>
                    </div>
                    <div class="tf-field">
                        <label>DESTINO</label>
                        <div class="tf-autocomplete">
                            <input type="text" class="tf-input tf-autocomplete-input" id="destinationInput" placeholder="Digite para buscar..." autocomplete="off">
                            <input type="hidden" name="destination_id" id="destinationSelect">
                            <div class="tf-autocomplete-list" id="destinationList"></div>
                        </div>
                    </div>
                    <div class="tf-field">
                        <label>DATA CHEGADA</label>
                        <input type="date" name="arrival_date" id="arrivalDate" class="tf-input" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="tf-field tf-field-sm">
                        <label>HORA</label>
                        <input type="time" name="arrival_time" id="arrivalTime" class="tf-input">
                    </div>
                    <div class="tf-field departure-field" id="departureDate">
                        <label>DATA PARTIDA</label>
                        <input type="date" name="departure_date" class="tf-input" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="tf-field tf-field-sm departure-field" id="departureTime">
                        <label>HORA</label>
                        <input type="time" name="departure_time" class="tf-input">
                    </div>
                </div>
                <div class="transfer-form-row transfer-form-row-bottom">
                    <div class="tf-field tf-field-pax">
                        <label>PASSAGEIROS</label>
                        <div class="pax-dropdown-wrapper">
                            <button type="button" class="tf-input pax-dropdown-btn" id="paxDropdownBtn">
                                <span id="paxTotal">1</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                            </button>
                            <div class="pax-dropdown" id="paxDropdown">
                                <?php foreach ($passengerCategories as $pcat): ?>
                                <div class="pax-dropdown-row">
                                    <div><strong><?= e(mb_strtoupper($pcat['name'])) ?></strong><?php if (!empty($pcat['age_label'])): ?><span>(<?= e($pcat['age_label']) ?>)</span><?php endif; ?></div>
                                    <div class="pax-counter">
                                        <button type="button" class="pax-btn pax-btn-minus">-</button>
                                        <input type="number" name="<?= e($pcat['field_name']) ?>" id="transfer_<?= e($pcat['field_name']) ?>" value="<?= (int)$pcat['default_quantity'] ?>" min="0" max="50" class="pax-input-sm" readonly>
                                        <button type="button" class="pax-btn pax-btn-plus">+</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="tf-field">
                        <label>TIPO DE SERVIÇO</label>
                        <select name="service_type" id="serviceType" class="tf-input">
                            <option value="private">Privado</option>
                            <option value="shared">Compartilhado</option>
                        </select>
                    </div>
                </div>

                <button type="button" id="searchTransfersBtn" class="btn-buscar-transfer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    BUSCAR
                </button>
            </div>

            <!-- Form Múltiplos Transfers -->
            <div class="transfer-form-content" id="tabMultiple" style="display:none;">
                <div class="multiple-routes-container" id="multipleRoutesContainer">
                    <!-- Rota 1 -->
                    <div class="multiple-route-item" data-route="1">
                        <div class="multiple-route-header">
                            <span class="route-number">Rota 1</span>
                        </div>
                        <div class="transfer-form-row">
                            <div class="tf-field">
                                <label>ORIGEM</label>
                                <div class="tf-autocomplete">
                                    <input type="text" class="tf-input tf-autocomplete-input multi-origin-input" data-route="1" placeholder="Digite para buscar..." autocomplete="off">
                                    <input type="hidden" class="multi-origin" name="multi_origin_1">
                                    <div class="tf-autocomplete-list multi-origin-list"></div>
                                </div>
                            </div>
                            <div class="tf-field">
                                <label>DESTINO</label>
                                <div class="tf-autocomplete">
                                    <input type="text" class="tf-input tf-autocomplete-input multi-dest-input" data-route="1" placeholder="Digite para buscar..." autocomplete="off">
                                    <input type="hidden" class="multi-destination" name="multi_destination_1">
                                    <div class="tf-autocomplete-list multi-dest-list"></div>
                                </div>
                            </div>
                            <div class="tf-field">
                                <label>DATA</label>
                                <input type="date" name="multi_date_1" class="tf-input multi-date" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="tf-field tf-field-sm">
                                <label>HORA</label>
                                <input type="time" name="multi_time_1" class="tf-input multi-time">
                            </div>
                        </div>
                    </div>

                    <!-- Rota 2 -->
                    <div class="multiple-route-item" data-route="2">
                        <div class="multiple-route-header">
                            <span class="route-number">Rota 2</span>
                            <button type="button" class="btn-remove-route" onclick="removeRoute(this)" title="Remover rota">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="transfer-form-row">
                            <div class="tf-field">
                                <label>ORIGEM</label>
                                <div class="tf-autocomplete">
                                    <input type="text" class="tf-input tf-autocomplete-input multi-origin-input" data-route="2" placeholder="Digite para buscar..." autocomplete="off">
                                    <input type="hidden" class="multi-origin" name="multi_origin_2">
                                    <div class="tf-autocomplete-list multi-origin-list"></div>
                                </div>
                            </div>
                            <div class="tf-field">
                                <label>DESTINO</label>
                                <div class="tf-autocomplete">
                                    <input type="text" class="tf-input tf-autocomplete-input multi-dest-input" data-route="2" placeholder="Digite para buscar..." autocomplete="off">
                                    <input type="hidden" class="multi-destination" name="multi_destination_2">
                                    <div class="tf-autocomplete-list multi-dest-list"></div>
                                </div>
                            </div>
                            <div class="tf-field">
                                <label>DATA</label>
                                <input type="date" name="multi_date_2" class="tf-input multi-date" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="tf-field tf-field-sm">
                                <label>HORA</label>
                                <input type="time" name="multi_time_2" class="tf-input multi-time">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add-route" id="btnAddRoute" onclick="addRoute()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    ADICIONAR ROTA
                </button>

                <div class="transfer-form-row transfer-form-row-bottom">
                    <div class="tf-field tf-field-pax">
                        <label>PASSAGEIROS</label>
                        <div class="pax-dropdown-wrapper">
                            <button type="button" class="tf-input pax-dropdown-btn" id="paxDropdownBtnMulti">
                                <span id="paxTotalMulti">1</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                            </button>
                            <div class="pax-dropdown" id="paxDropdownMulti">
                                <?php foreach ($passengerCategories as $pcat): ?>
                                <div class="pax-dropdown-row">
                                    <div><strong><?= e(mb_strtoupper($pcat['name'])) ?></strong><?php if (!empty($pcat['age_label'])): ?><span>(<?= e($pcat['age_label']) ?>)</span><?php endif; ?></div>
                                    <div class="pax-counter">
                                        <button type="button" class="pax-btn pax-btn-minus">-</button>
                                        <input type="number" name="multi_<?= e($pcat['field_name']) ?>" id="multi_<?= e($pcat['field_name']) ?>" value="<?= (int)$pcat['default_quantity'] ?>" min="0" max="50" class="pax-input-sm" readonly>
                                        <button type="button" class="pax-btn pax-btn-plus">+</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="tf-field">
                        <label>TIPO DE SERVIÇO</label>
                        <select name="multi_service_type" id="multiServiceType" class="tf-input">
                            <option value="private">Privado</option>
                            <option value="shared">Compartilhado</option>
                        </select>
                    </div>
                </div>

                <button type="button" id="searchMultiTransfersBtn" class="btn-buscar-transfer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    BUSCAR
                </button>
            </div>
        </div>

        <!-- Resultados -->
        <div class="transfer-results-area" id="transferResults" style="display:none;">
            <div class="transfer-results-card">
                <!-- Seção ENTRADA -->
                <div class="transfer-section" id="transferArrivalSection">
                    <div class="transfer-section-header">
                        <div class="transfer-section-icon transfer-section-icon--arrival">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                        </div>
                        <div>
                            <h3 class="transfer-section-title">ENTRADA</h3>
                            <p class="transfer-section-route" id="arrivalRouteLabel"></p>
                        </div>
                    </div>
                    <div class="transfer-vehicles-grid" id="arrivalVehicles"></div>
                </div>

                <!-- Seção SAÍDA (visível apenas em ida e volta) -->
                <div class="transfer-section" id="transferDepartureSection" style="display:none;">
                    <div class="transfer-section-header">
                        <div class="transfer-section-icon transfer-section-icon--departure">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                        </div>
                        <div>
                            <h3 class="transfer-section-title">SAÍDA</h3>
                            <p class="transfer-section-route" id="departureRouteLabel"></p>
                        </div>
                    </div>
                    <div class="transfer-vehicles-grid" id="departureVehicles"></div>
                </div>

                <!-- Resumo da Seleção -->
                <div class="transfer-summary" id="transferSummary" style="display:none;">
                    <h4 class="transfer-summary-title">Resumo da sua seleção</h4>
                    <div class="transfer-summary-items" id="transferSummaryItems"></div>
                    <div class="transfer-summary-total">
                        <span>Total:</span>
                        <strong id="transferTotalValue">$0.00 USD</strong>
                    </div>
                    <div class="transfer-summary-actions">
                        <button type="button" class="btn btn-primary btn-lg" id="btnAddCart">Adicionar ao Carrinho</button>
                        <button type="button" class="btn btn-accent btn-lg" id="btnDirectCheckout">Ir para Checkout</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados Múltiplos Transfers -->
        <div class="transfer-results-area" id="transferMultiResults" style="display:none;">
            <div class="transfer-results-card">
                <div id="resultsList"></div>
                <div class="transfer-summary" id="transferTotalBar" style="display:none;">
                    <div class="transfer-summary-total">
                        <span>Total:</span>
                        <strong id="multiTotalValue">$0.00 USD</strong>
                    </div>
                    <div class="transfer-summary-actions">
                        <button type="button" class="btn btn-primary btn-lg" id="btnAddCartMulti">Adicionar ao Carrinho</button>
                        <button type="button" class="btn btn-accent btn-lg" id="btnDirectCheckoutMulti">Ir para Checkout</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div class="transfer-empty-state" id="transferEmptyState" style="display:none;">
            <div class="transfer-results-card">
                <div class="empty-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="1.5"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <h3 class="empty-title">Nenhum veículo disponível para todas as rotas</h3>
                <p class="empty-subtitle">Não encontramos um veículo que atenda todas as rotas selecionadas.</p>
                <div class="empty-suggestions">
                    <strong>Sugestões:</strong>
                    <ul>
                        <li>Tente buscar cada transfer separadamente (Somente Ida)</li>
                        <li>Reduza o número de passageiros</li>
                        <li>Entre em contato conosco para opções personalizadas</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div class="transfer-loading" id="transferLoading" style="display:none;">
            <div class="spinner"></div>
            <p>Buscando transfers disponíveis...</p>
        </div>
    </div>
</section>

<!-- Serviços de Transporte -->
<section class="section section-transfers-home">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">Serviços de transporte em Punta Cana</h2>
            <p class="section-subtitle">Reserve já o seu traslado para o seu hotel em Punta Cana e evite atrasos desnecessários no aeroporto na chegada. Viaje sem preocupações com o nosso serviço profissional de transporte privativo ou compartilhado, em veículos confortáveis com <strong>Wi-Fi gratuito</strong> e <strong>cadeiras para crianças</strong> disponíveis. Sempre pontual e seguro!</p>
        </div>

        <div class="transfers-home-grid">
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= asset('images/onibus.png') ?>" alt="Ônibus Compartilhado" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer em Ônibus Compartilhado</h3>
                    <p class="transfer-home-desc">Viaje com conforto e economia em um <strong>ônibus climatizado</strong>, com embarques regulares e motoristas experientes. Ideal para quem busca praticidade em Punta Cana.</p>
                </div>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= asset('images/van.png') ?>" alt="Van Privativa" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer Privativo em Van</h3>
                    <p class="transfer-home-desc">Tenha <strong>mais conforto e privacidade</strong> com nosso transfer exclusivo em van. Perfeito para famílias ou pequenos grupos, com ar-condicionado e horários flexíveis.</p>
                </div>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= asset('images/van_adap.png') ?>" alt="Van Adaptada" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer Acessível com Van Adaptada</h3>
                    <p class="transfer-home-desc">Viaje com <strong>segurança e acessibilidade</strong> em nossa van adaptada com rampa para cadeirantes. Espaço amplo e motorista preparado para um trajeto tranquilo.</p>
                </div>
        </div>
    </div>
</section>

<!-- Experiências em Destaque -->
<section class="section section-experiencias-destaque">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">Experiências em Destaque</h2>
            <p class="section-subtitle">Proporcionar experiências autênticas e memoráveis para brasileiros em Punta Cana, combinando nosso conhecimento local com um atendimento personalizado e carinhoso, como se estivéssemos recebendo amigos em nossa própria casa.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon feature-icon-blue">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <h4>Atendimento Pessoal</h4>
                <p>Cuidamos pessoalmente de cada cliente</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon feature-icon-red">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                </div>
                <h4>Paixão pelo que Fazemos</h4>
                <p>Amor por Punta Cana e pelo Brasil</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon feature-icon-green">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <h4>Suporte em Português</h4>
                <p>24/7 durante sua estadia</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA - Vamos Planejar -->
<section class="section section-cta-planejar">
    <div class="container">
        <div class="cta-planejar-content">
            <h2>Vamos Planejar sua Viagem Juntos?</h2>
            <p>Proporcionar experiências autênticas e memoráveis para brasileiros em Punta Cana, combinando nosso conhecimento local com um atendimento personalizado e carinhoso, como se estivéssemos recebendo amigos em nossa própria casa.</p>
            <a href="/contato" class="btn btn-accent">Fale Conosco</a>
        </div>
    </div>
</section>


<script>
const TRANSFER_LOCATIONS = <?= json_encode(array_map(function($loc) { return ['id' => (int)$loc['id'], 'title' => $loc['title'], 'type' => $loc['location_type'] ?? 'other']; }, $locations)) ?>;

(function() {
    function setupAutocomplete(inputId, hiddenId, listId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const list = document.getElementById(listId);
        if (!input || !hidden || !list) return;

        input.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            hidden.value = '';
            if (q.length === 0) {
                // Mostrar todos quando campo vazio e focado
                list.innerHTML = TRANSFER_LOCATIONS.map(l => 
                    `<div class="tf-autocomplete-item" data-id="${l.id}" data-title="${l.title}">${l.title}</div>`
                ).join('');
                list.style.display = 'block';
                return;
            }

            const filtered = TRANSFER_LOCATIONS.filter(l => l.title.toLowerCase().includes(q));
            if (filtered.length === 0) {
                list.innerHTML = '<div class="tf-autocomplete-empty">Nenhum resultado encontrado</div>';
            } else {
                list.innerHTML = filtered.map(l => 
                    `<div class="tf-autocomplete-item" data-id="${l.id}" data-title="${l.title}">${highlightMatch(l.title, q)}</div>`
                ).join('');
            }
            list.style.display = 'block';
        });

        input.addEventListener('focus', function() {
            // Ao clicar/focar, mostrar lista completa ou filtrada
            if (this.value.length === 0) {
                list.innerHTML = TRANSFER_LOCATIONS.map(l => 
                    `<div class="tf-autocomplete-item" data-id="${l.id}" data-title="${l.title}">${l.title}</div>`
                ).join('');
                list.style.display = 'block';
            } else {
                this.dispatchEvent(new Event('input'));
            }
        });

        list.addEventListener('click', function(e) {
            const item = e.target.closest('.tf-autocomplete-item');
            if (!item) return;
            input.value = item.dataset.title;
            hidden.value = item.dataset.id;
            list.style.display = 'none';
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#' + inputId) && !e.target.closest('#' + listId)) {
                list.style.display = 'none';
            }
        });

        // Fechar esta lista quando outro autocomplete recebe foco
        document.querySelectorAll('.tf-autocomplete-input').forEach(function(otherInput) {
            if (otherInput.id !== inputId) {
                otherInput.addEventListener('focus', function() {
                    list.style.display = 'none';
                });
            }
        });
    }

    function highlightMatch(text, query) {
        const idx = text.toLowerCase().indexOf(query);
        if (idx === -1) return text;
        return text.substring(0, idx) + '<strong>' + text.substring(idx, idx + query.length) + '</strong>' + text.substring(idx + query.length);
    }

    setupAutocomplete('originInput', 'originSelect', 'originList');
    setupAutocomplete('destinationInput', 'destinationSelect', 'destinationList');
})();

// Lógica de detecção de aeroporto: muda label HORA → HORÁRIO DO VOO
(function() {
    var arrivalTimeLabel = document.querySelector('#arrivalTime')?.closest('.tf-field')?.querySelector('label');
    var departureTimeLabel = document.querySelector('[name="departure_time"]')?.closest('.tf-field')?.querySelector('label');

    if (!arrivalTimeLabel) return;

    function getLocationType(locationId) {
        if (!locationId) return null;
        for (var i = 0; i < TRANSFER_LOCATIONS.length; i++) {
            if (String(TRANSFER_LOCATIONS[i].id) === String(locationId)) {
                return TRANSFER_LOCATIONS[i].type;
            }
        }
        return null;
    }

    function updateLabels() {
        var originId = document.getElementById('originSelect').value;
        var destinationId = document.getElementById('destinationSelect').value;
        var originType = getLocationType(originId);
        var destType = getLocationType(destinationId);

        // Chegada: se ORIGEM é aeroporto → label vira "HORÁRIO DO VOO"
        if (originType === 'airport') {
            arrivalTimeLabel.innerHTML = 'HORÁRIO DO VOO ✈️';
            document.getElementById('arrivalTime').title = 'Informe o horário de chegada do seu voo';
        } else {
            arrivalTimeLabel.textContent = 'HORA';
            document.getElementById('arrivalTime').title = '';
        }

        // Partida: se DESTINO é aeroporto → label vira "HORÁRIO DO VOO"
        if (departureTimeLabel) {
            if (destType === 'airport') {
                departureTimeLabel.innerHTML = 'HORÁRIO DO VOO ✈️';
                var depInput = document.querySelector('[name="departure_time"]');
                if (depInput) depInput.title = 'Informe o horário de partida do seu voo. Busca no hotel será ~3h antes.';
            } else {
                departureTimeLabel.textContent = 'HORA';
                var depInput2 = document.querySelector('[name="departure_time"]');
                if (depInput2) depInput2.title = '';
            }
        }

        // Mostrar/esconder dica de voo
        var existingTip = document.getElementById('airportFlightTip');
        if (originType === 'airport' || destType === 'airport') {
            if (!existingTip) {
                var tipDiv = document.createElement('div');
                tipDiv.id = 'airportFlightTip';
                tipDiv.style.cssText = 'grid-column:1/-1;display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;font-size:0.82rem;color:#0369a1;margin-top:-4px;';
                if (destType === 'airport' && originType !== 'airport') {
                    tipDiv.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0369a1" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span>Recomendamos chegar ao aeroporto 3 horas antes do voo. O horário de busca no hotel será calculado automaticamente considerando o tempo de deslocamento.</span>';
                } else {
                    tipDiv.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0369a1" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span>Informe o horário de chegada do seu voo. O motorista estará aguardando no aeroporto.</span>';
                }
                var formRow = document.querySelector('.transfer-form-row');
                if (formRow) formRow.parentNode.insertBefore(tipDiv, formRow.nextSibling);
            } else {
                existingTip.style.display = 'flex';
                if (destType === 'airport' && originType !== 'airport') {
                    existingTip.querySelector('span').textContent = 'Recomendamos chegar ao aeroporto 3 horas antes do voo. O horário de busca no hotel será calculado automaticamente considerando o tempo de deslocamento.';
                } else {
                    existingTip.querySelector('span').textContent = 'Informe o horário de chegada do seu voo. O motorista estará aguardando no aeroporto.';
                }
            }
        } else {
            if (existingTip) existingTip.style.display = 'none';
        }
    }

    // Polling para detectar mudanças nos selects
    setInterval(function() {
        var currentOrigin = document.getElementById('originSelect')?.value || '';
        var currentDest = document.getElementById('destinationSelect')?.value || '';
        if (currentOrigin !== (window._lastOriginCheck || '') || currentDest !== (window._lastDestCheck || '')) {
            window._lastOriginCheck = currentOrigin;
            window._lastDestCheck = currentDest;
            updateLabels();
        }
    }, 500);
})();
</script>

<script>
// Event delegation nos dropdowns para capturar cliques nos botões +/-
(function() {
    var paxDrop = document.getElementById('paxDropdown');
    var paxDropMulti = document.getElementById('paxDropdownMulti');

    function handlePaxClick(e, totalId, dropdownId) {
        var btn = e.target.closest('.pax-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        var row = btn.closest('.pax-counter');
        if (!row) return;
        var input = row.querySelector('.pax-input-sm');
        if (!input) return;
        var delta = btn.classList.contains('pax-btn-plus') ? 1 : -1;
        var v = parseInt(input.value || 0) + delta;
        var min = parseInt(input.getAttribute('min') || 0);
        var max = parseInt(input.getAttribute('max') || 99);
        if (v < min) v = min;
        if (v > max) v = max;
        input.value = v;
        // Update total
        var total = 0;
        var allInputs = document.querySelectorAll('#' + dropdownId + ' .pax-input-sm');
        for (var i = 0; i < allInputs.length; i++) { total += parseInt(allInputs[i].value) || 0; }
        var totalEl = document.getElementById(totalId);
        if (totalEl) totalEl.textContent = total;
    }

    if (paxDrop) {
        paxDrop.addEventListener('click', function(e) {
            e.stopPropagation();
            handlePaxClick(e, 'paxTotal', 'paxDropdown');
        });
    }
    if (paxDropMulti) {
        paxDropMulti.addEventListener('click', function(e) {
            e.stopPropagation();
            handlePaxClick(e, 'paxTotalMulti', 'paxDropdownMulti');
        });
    }
})();
</script>

<script>
// Autocomplete para múltiplos transfers
(function() {
    function setupMultiAutocomplete(container) {
        if (!container) return;
        var inputEls = container.querySelectorAll('.tf-autocomplete-input');
        inputEls.forEach(function(input) {
            var wrapper = input.closest('.tf-autocomplete');
            var hidden = wrapper.querySelector('input[type="hidden"]');
            var list = wrapper.querySelector('.tf-autocomplete-list');
            if (!hidden || !list) return;

            // Skip if already initialized
            if (input.dataset.acInit) return;
            input.dataset.acInit = '1';

            input.addEventListener('input', function() {
                var q = this.value.toLowerCase().trim();
                hidden.value = '';
                if (q.length === 0) {
                    list.innerHTML = TRANSFER_LOCATIONS.map(function(l) {
                        return '<div class="tf-autocomplete-item" data-id="' + l.id + '" data-title="' + l.title + '">' + l.title + '</div>';
                    }).join('');
                    list.style.display = 'block';
                    return;
                }
                var filtered = TRANSFER_LOCATIONS.filter(function(l) { return l.title.toLowerCase().includes(q); });
                if (filtered.length === 0) {
                    list.innerHTML = '<div class="tf-autocomplete-empty">Nenhum resultado</div>';
                } else {
                    list.innerHTML = filtered.map(function(l) {
                        var idx = l.title.toLowerCase().indexOf(q);
                        var highlighted = idx === -1 ? l.title : l.title.substring(0, idx) + '<strong>' + l.title.substring(idx, idx + q.length) + '</strong>' + l.title.substring(idx + q.length);
                        return '<div class="tf-autocomplete-item" data-id="' + l.id + '" data-title="' + l.title + '">' + highlighted + '</div>';
                    }).join('');
                }
                list.style.display = 'block';
            });

            input.addEventListener('focus', function() {
                if (this.value.length === 0) {
                    list.innerHTML = TRANSFER_LOCATIONS.map(function(l) {
                        return '<div class="tf-autocomplete-item" data-id="' + l.id + '" data-title="' + l.title + '">' + l.title + '</div>';
                    }).join('');
                    list.style.display = 'block';
                } else {
                    this.dispatchEvent(new Event('input'));
                }
            });

            list.addEventListener('click', function(e) {
                var item = e.target.closest('.tf-autocomplete-item');
                if (!item) return;
                input.value = item.dataset.title;
                hidden.value = item.dataset.id;
                list.style.display = 'none';
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.tf-autocomplete') || !wrapper.contains(e.target)) {
                    list.style.display = 'none';
                }
            });
        });
    }

    // Inicializar autocompletes das rotas existentes
    var multiContainer = document.getElementById('multipleRoutesContainer');
    setupMultiAutocomplete(multiContainer);

    // Override addRoute para inicializar autocomplete nas novas rotas
    var originalAddRoute = window.addRoute;
    window.addRoute = function() {
        var container = document.getElementById('multipleRoutesContainer');
        if (!container) return;
        var routes = container.querySelectorAll('.multiple-route-item');
        var newIndex = routes.length + 1;
        if (newIndex > 10) { return; }

        var routeHtml = '<div class="multiple-route-item" data-route="' + newIndex + '">' +
            '<div class="multiple-route-header">' +
                '<span class="route-number">Rota ' + newIndex + '</span>' +
                '<button type="button" class="btn-remove-route" onclick="removeRoute(this)" title="Remover rota">' +
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
                '</button>' +
            '</div>' +
            '<div class="transfer-form-row">' +
                '<div class="tf-field"><label>ORIGEM</label><div class="tf-autocomplete"><input type="text" class="tf-input tf-autocomplete-input multi-origin-input" data-route="' + newIndex + '" placeholder="Digite para buscar..." autocomplete="off"><input type="hidden" class="multi-origin" name="multi_origin_' + newIndex + '"><div class="tf-autocomplete-list multi-origin-list"></div></div></div>' +
                '<div class="tf-field"><label>DESTINO</label><div class="tf-autocomplete"><input type="text" class="tf-input tf-autocomplete-input multi-dest-input" data-route="' + newIndex + '" placeholder="Digite para buscar..." autocomplete="off"><input type="hidden" class="multi-destination" name="multi_destination_' + newIndex + '"><div class="tf-autocomplete-list multi-dest-list"></div></div></div>' +
                '<div class="tf-field"><label>DATA</label><input type="date" name="multi_date_' + newIndex + '" class="tf-input multi-date" min="' + new Date().toISOString().split('T')[0] + '"></div>' +
                '<div class="tf-field tf-field-sm"><label>HORA</label><input type="time" name="multi_time_' + newIndex + '" class="tf-input multi-time"></div>' +
            '</div>' +
        '</div>';
        container.insertAdjacentHTML('beforeend', routeHtml);

        // Inicializar autocomplete no novo item
        setupMultiAutocomplete(container);
    };

    // Expor para ser reutilizado
    window.setupMultiAutocomplete = setupMultiAutocomplete;
})();
</script>

<style>
.tf-autocomplete { position: relative; }
.tf-autocomplete-input { width: 100%; }
.tf-autocomplete-list { display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1.5px solid #e0e0e0; border-top: none; border-radius: 0 0 8px 8px; max-height: 250px; overflow-y: auto; z-index: 100; box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
.tf-autocomplete-item { padding: 12px 16px; font-size: 14px; cursor: pointer; border-bottom: 1px solid #f5f5f5; color: #1C2011; transition: background 0.15s; }
.tf-autocomplete-item:last-child { border-bottom: none; }
.tf-autocomplete-item:hover { background: #f0fdf4; }
.tf-autocomplete-item strong { color: #1B6F00; font-weight: 700; }
.tf-autocomplete-empty { padding: 16px; text-align: center; color: #999; font-size: 13px; }
.tf-autocomplete-list::-webkit-scrollbar { width: 5px; }
.tf-autocomplete-list::-webkit-scrollbar-thumb { background: #d4d4d4; border-radius: 3px; }
</style>
