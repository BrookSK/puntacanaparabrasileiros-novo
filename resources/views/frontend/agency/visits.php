<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'visitas']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Visitas</h1>
                        <p class="aff-page-subtitle">Acompanhe os acessos gerados pelo seu link de indicação</p>
                    </div>
                </div>

                <div class="aff-card">
                    <div class="aff-empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <p>O acompanhamento de visitas ainda não está disponível para agências.</p>
                        <span>As vendas realizadas pelo seu link continuam sendo registradas normalmente em Comissões.</span>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>
