<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('affiliate-nav', ['active' => 'links']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Links Afiliados</h1>
                        <p class="aff-page-subtitle">Gerencie e compartilhe seus links de indicação</p>
                    </div>
                </div>

                <!-- Link principal -->
                <div class="aff-card">
                    <div class="aff-card-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-green)" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                        <h3 class="aff-card-title">Seu Link de Afiliado</h3>
                    </div>
                    <p class="aff-card-desc">Esta é a sua URL de referência exclusiva. Compartilhe com seu público para ganhar comissões em cada venda realizada.</p>
                    <div class="aff-link-copy-box">
                        <input type="text" class="form-control aff-link-input" value="<?= e(setting('site_url', 'https://puntacanaparabrasileiros.com')) ?>/?ref=<?= (int)($affiliate['id'] ?? 1) ?>" id="affiliateLink" readonly>
                        <button type="button" class="btn btn-primary aff-copy-btn" onclick="copyAffLink('affiliateLink')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            Copiar
                        </button>
                    </div>
                </div>

                <!-- Gerador de links -->
                <div class="aff-card">
                    <div class="aff-card-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-green)" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <h3 class="aff-card-title">Gerar Link Personalizado</h3>
                    </div>
                    <p class="aff-card-desc">Cole qualquer URL do site abaixo para gerar um link de referência personalizado para aquela página específica.</p>
                    <div class="aff-link-generator">
                        <div class="aff-link-copy-box">
                            <input type="text" class="form-control aff-link-input" id="customUrl" placeholder="https://puntacanaparabrasileiros.com/passeios/...">
                            <button type="button" class="btn btn-primary aff-copy-btn" onclick="generateAffLink()">Gerar Link</button>
                        </div>
                        <div id="generatedLinkResult" style="display:none;margin-top:16px;">
                            <label style="font-size:12px;font-weight:600;color:var(--gray);text-transform:uppercase;margin-bottom:6px;display:block;">Link gerado:</label>
                            <div class="aff-link-copy-box">
                                <input type="text" class="form-control aff-link-input" id="generatedLinkInput" readonly>
                                <button type="button" class="btn btn-primary aff-copy-btn" onclick="copyAffLink('generatedLinkInput')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                    Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dicas -->
                <div class="aff-card aff-card--tip">
                    <div class="aff-card-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <h3 class="aff-card-title">Dicas para Maximizar seus Ganhos</h3>
                    </div>
                    <ul class="aff-tips-list">
                        <li>Compartilhe links de passeios específicos para maior conversão</li>
                        <li>Use seus links em stories, reels e posts com chamadas para ação</li>
                        <li>Aproveite períodos de alta temporada para intensificar divulgação</li>
                        <li>Combine links com conteúdo autêntico sobre suas experiências</li>
                    </ul>
                </div>
            </main>
        </div>
    </div>
</section>

<script>
function copyAffLink(inputId) {
    var el = document.getElementById(inputId);
    el.select();
    el.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(el.value).then(function() {
        var btn = el.closest('.aff-link-copy-box').querySelector('.aff-copy-btn');
        var orig = btn.innerHTML;
        btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copiado!';
        btn.classList.add('btn-copied');
        setTimeout(function() { btn.innerHTML = orig; btn.classList.remove('btn-copied'); }, 2000);
    });
}
function generateAffLink() {
    var url = document.getElementById('customUrl').value.trim();
    if (!url) { alert('Cole uma URL do site no campo acima.'); return; }
    var sep = url.includes('?') ? '&' : '?';
    document.getElementById('generatedLinkInput').value = url + sep + 'ref=<?= (int)($affiliate['id'] ?? 1) ?>';
    document.getElementById('generatedLinkResult').style.display = 'block';
}
</script>
