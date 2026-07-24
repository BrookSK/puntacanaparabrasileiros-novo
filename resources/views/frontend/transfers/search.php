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

            <!-- Form Ida e Volta -->
            <div class="transfer-form-content" id="tabRoundtrip">
                <div class="transfer-form-row">
                    <div class="tf-field">
                        <label>ORIGEM</label>
                        <select name="origin_id" id="originSelect" class="tf-input">
                            <option value="">Digite para buscar...</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= (int)$loc['id'] ?>"><?= e($loc['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="tf-field">
                        <label>DESTINO</label>
                        <select name="destination_id" id="destinationSelect" class="tf-input">
                            <option value="">Digite para buscar...</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= (int)$loc['id'] ?>"><?= e($loc['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
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
                                <div class="pax-dropdown-row">
                                    <div><strong>ADULTOS</strong><span>(+12 ANOS)</span></div>
                                    <div class="pax-counter">
                                        <button type="button" class="pax-btn" onclick="changePaxTransfer('adults', -1)">-</button>
                                        <input type="number" name="adults" id="transferAdults" value="1" min="1" max="50" class="pax-input-sm">
                                        <button type="button" class="pax-btn pax-btn-plus" onclick="changePaxTransfer('adults', 1)">+</button>
                                    </div>
                                </div>
                                <div class="pax-dropdown-row">
                                    <div><strong>CRIANÇAS</strong><span>(2-11 ANOS)</span></div>
                                    <div class="pax-counter">
                                        <button type="button" class="pax-btn" onclick="changePaxTransfer('children', -1)">-</button>
                                        <input type="number" name="children" id="transferChildren" value="0" min="0" max="20" class="pax-input-sm">
                                        <button type="button" class="pax-btn pax-btn-plus" onclick="changePaxTransfer('children', 1)">+</button>
                                    </div>
                                </div>
                                <div class="pax-dropdown-row">
                                    <div><strong>BEBÊS</strong><span>(0-1 ANO)</span></div>
                                    <div class="pax-counter">
                                        <button type="button" class="pax-btn" onclick="changePaxTransfer('infants', -1)">-</button>
                                        <input type="number" name="infants" id="transferInfants" value="0" min="0" max="10" class="pax-input-sm">
                                        <button type="button" class="pax-btn pax-btn-plus" onclick="changePaxTransfer('infants', 1)">+</button>
                                    </div>
                                </div>
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
        </div>

        <!-- Resultados -->
        <div class="transfer-results-area" id="transferResults" style="display:none;">
            <div class="transfer-results-card">
                <h3 class="transfer-results-title">PACOTE DE TRANSFERS</h3>
                <hr class="transfer-divider">
                <div id="resultsList"></div>

                <!-- Total e Botões -->
                <div class="transfer-total-bar" id="transferTotalBar" style="display:none;">
                    <p class="transfer-total-label">TOTAL:</p>
                    <p class="transfer-total-value" id="transferTotalValue">$0.00 USD</p>
                    <div class="transfer-total-actions">
                        <button type="button" class="btn-add-cart" id="btnAddCart">ADICIONAR AO CARRINHO</button>
                        <button type="button" class="btn-direct-checkout" id="btnDirectCheckout">IR DIRETO AO CHECKOUT</button>
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
            <?php if (!empty($vehicles ?? null)): ?>
                <?php foreach (array_slice($vehicles, 0, 3) as $vehicle): ?>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= e($vehicle['image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($vehicle['title']) ?>" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title"><?= e($vehicle['title']) ?></h3>
                    <p class="transfer-home-desc"><?= $vehicle['description'] ?? '' ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/inspecao-veicular-para-onibus-fretado-1024x683.jpg" alt="Ônibus Compartilhado" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer em Ônibus Compartilhado</h3>
                    <p class="transfer-home-desc">Viaje com conforto e economia em um <strong>ônibus climatizado</strong>, com embarques regulares e motoristas experientes. Ideal para quem busca praticidade em Punta Cana.</p>
                </div>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/BB461A77-985A-4E39-8468-1C9D5F051C03-1024x683.png" alt="Van Privativa" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer Privativo em Van</h3>
                    <p class="transfer-home-desc">Tenha <strong>mais conforto e privacidade</strong> com nosso transfer exclusivo em van. Perfeito para famílias ou pequenos grupos, com ar-condicionado e horários flexíveis.</p>
                </div>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/C8EC6F30-8ED0-4C0C-A377-C8317B5673A7-1024x683.png" alt="Van Adaptada" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer Acessível com Van Adaptada</h3>
                    <p class="transfer-home-desc">Viaje com <strong>segurança e acessibilidade</strong> em nossa van adaptada com rampa para cadeirantes. Espaço amplo e motorista preparado para um trajeto tranquilo.</p>
                </div>
            <?php endif; ?>
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
            <a href="/pagina/contato" class="btn btn-accent">Fale Conosco</a>
        </div>
    </div>
</section>
