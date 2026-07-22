<!-- Modal de Reserva -->
<div class="booking-modal-overlay" id="bookingModal" style="display:none">
    <div class="booking-modal">
        <!-- Botão Fechar -->
        <button class="booking-modal-close" id="bookingModalClose">&times;</button>

        <div class="booking-modal-layout">
            <!-- Conteúdo Principal (esquerda) -->
            <div class="booking-modal-main">
                <!-- Tabs -->
                <div class="booking-modal-tabs">
                    <button class="bm-tab active" data-step="1" id="bmTab1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Data E Hora
                    </button>
                    <button class="bm-tab" data-step="2" id="bmTab2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4"/></svg>
                        Tipo De Pacote
                    </button>
                </div>

                <!-- Step 1: Data e Hora -->
                <div class="bm-step active" id="bmStep1">
                    <!-- Navegação do calendário -->
                    <div class="bm-calendar-nav">
                        <div class="bm-month-select">
                            <select id="bmMonth" class="bm-select"></select>
                            <span id="bmYear" class="bm-year"></span>
                        </div>
                        <div class="bm-nav-arrows">
                            <button type="button" id="bmPrevMonth" class="bm-arrow">&lsaquo;</button>
                            <button type="button" id="bmNextMonth" class="bm-arrow">&rsaquo;</button>
                        </div>
                    </div>

                    <!-- Calendário -->
                    <div class="bm-calendar">
                        <div class="bm-weekdays">
                            <span>DOM</span><span>SEG</span><span>TER</span><span>QUA</span><span>QUI</span><span>SEX</span><span>SÁB</span>
                        </div>
                        <div class="bm-days" id="bmDays"></div>
                    </div>

                    <!-- Horários -->
                    <div class="bm-times" id="bmTimes" style="display:none">
                        <div class="bm-times-list" id="bmTimesList"></div>
                    </div>

                    <!-- Botão Continuar -->
                    <div class="bm-actions">
                        <button type="button" class="bm-btn-continue" id="bmContinue" disabled>CONTINUAR</button>
                    </div>
                </div>

                <!-- Step 2: Tipo de Pacote -->
                <div class="bm-step" id="bmStep2">
                    <!-- Pacotes -->
                    <div class="bm-packages" id="bmPackages"></div>

                    <!-- Viajantes -->
                    <div class="bm-travelers">
                        <div class="bm-travelers-header">
                            <span>VIAJANTES</span>
                            <span>QUANTIDADE</span>
                        </div>
                        <div class="bm-travelers-list" id="bmTravelersList"></div>
                    </div>

                    <!-- Ações -->
                    <div class="bm-actions bm-actions-step2">
                        <button type="button" class="bm-btn-back" id="bmBack">&lsaquo; Voltar</button>
                        <button type="button" class="bm-btn-cart" id="bmAddCart">ADICIONAR AO CARRINHO</button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Resumo -->
            <div class="booking-modal-sidebar">
                <h4 class="bm-sidebar-label">RESUMO DA RESERVA</h4>
                <h3 class="bm-sidebar-title" id="bmSidebarTitle"></h3>
                <p class="bm-sidebar-date" id="bmSidebarDate">Data De Início: --</p>
                <div class="bm-sidebar-package" id="bmSidebarPackage" style="display:none">
                    <span id="bmSidebarPackageName"></span>
                </div>

                <div class="bm-sidebar-travelers" id="bmSidebarTravelers" style="display:none">
                    <h5>Viajantes</h5>
                    <div id="bmSidebarTravelersList"></div>
                </div>

                <div class="bm-sidebar-total">
                    <span>Total :</span>
                    <span id="bmSidebarTotal">$0</span>
                </div>

                <button type="button" class="bm-btn-checkout" id="bmCheckout">IR DIRETO AO CHECKOUT</button>
            </div>
        </div>
    </div>
</div>
