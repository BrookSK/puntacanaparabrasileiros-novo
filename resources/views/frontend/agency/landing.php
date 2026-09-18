<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'landing']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Landing Page</h1>
                        <p class="aff-page-subtitle">Divulgue os passeios de Punta Cana com o seu link</p>
                    </div>
                </div>

                <div class="aff-card">
                    <div class="aff-card-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <h3 class="aff-card-title">Sua página de divulgação</h3>
                    </div>
                    <p class="aff-card-desc">Compartilhe o link abaixo nas suas redes e canais de atendimento. Ele leva o cliente à página de passeios já com a atribuição da sua agência — toda venda feita por ele gera comissão para você.</p>
                    <div class="aff-link-copy-box">
                        <input type="text" class="aff-link-input" id="landingLink" value="<?= e($refLink) ?>" readonly>
                        <button type="button" class="btn btn-primary aff-copy-btn" onclick="copyLandingLink()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            Copiar
                        </button>
                    </div>
                    <div style="margin-top:14px;">
                        <a href="<?= e($refLink) ?>" target="_blank" class="btn btn-outline">Abrir página de divulgação</a>
                    </div>
                </div>

                <div class="aff-card aff-card--tip">
                    <div class="aff-card-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <h3 class="aff-card-title">Dicas para vender mais</h3>
                    </div>
                    <ul class="aff-tips-list">
                        <li>Envie o link direto no WhatsApp junto com uma sugestão de passeio</li>
                        <li>Compartilhe passeios específicos usando o gerador de link em "Link de Indicação"</li>
                        <li>Use os materiais da aba "Criativos" para acompanhar suas publicações</li>
                        <li>Aproveite períodos de alta temporada para intensificar a divulgação</li>
                    </ul>
                </div>
            </main>
        </div>
    </div>
</section>

<script>
function copyLandingLink() {
    var el = document.getElementById('landingLink');
    el.select(); el.setSelectionRange(0, 99999);
    try { navigator.clipboard.writeText(el.value); } catch(e) { document.execCommand('copy'); }
    var btn = el.closest('.aff-link-copy-box').querySelector('.aff-copy-btn');
    var orig = btn.innerHTML; btn.classList.add('btn-copied'); btn.textContent = 'Copiado!';
    setTimeout(function(){ btn.classList.remove('btn-copied'); btn.innerHTML = orig; }, 1800);
}
</script>
