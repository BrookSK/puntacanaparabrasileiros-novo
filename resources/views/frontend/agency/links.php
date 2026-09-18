<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'link']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Link de Indicação</h1>
                        <p class="aff-page-subtitle">Compartilhe seu link e ganhe comissão em cada venda</p>
                    </div>
                </div>

                <!-- Link principal -->
                <div class="aff-card">
                    <h3 class="aff-card-title">Seu link de indicação</h3>
                    <p class="aff-card-desc">Esta é a URL exclusiva da sua agência. Compartilhe com seus clientes — as vendas feitas por ela geram comissão para você.</p>
                    <div class="aff-link-copy-box">
                        <input type="text" class="aff-link-input" id="mainLink" value="<?= e($refLink) ?>" readonly>
                        <button type="button" class="btn btn-primary aff-copy-btn" onclick="copyAgLink('mainLink')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            Copiar
                        </button>
                    </div>
                    <p style="font-size:12px;color:#94a3b8;margin:10px 0 0;">Código da agência: <strong><?= e($agency['ref_code']) ?></strong></p>
                </div>

                <!-- Gerador de link personalizado -->
                <div class="aff-card">
                    <h3 class="aff-card-title">Gerar link personalizado</h3>
                    <p class="aff-card-desc">Cole qualquer página do site abaixo (um passeio específico, por exemplo) para gerar um link de indicação direto para aquela página.</p>
                    <div class="aff-link-copy-box">
                        <input type="text" class="aff-link-input" id="customUrl" placeholder="https://puntacanaparabrasileiros.com/passeios/...">
                        <button type="button" class="btn btn-primary aff-copy-btn" onclick="generateAgLink()">Gerar Link</button>
                    </div>
                    <div id="genResult" style="display:none;margin-top:16px;">
                        <label style="font-size:12px;font-weight:600;color:#636e72;text-transform:uppercase;margin-bottom:6px;display:block;">Link gerado:</label>
                        <div class="aff-link-copy-box">
                            <input type="text" class="aff-link-input" id="genInput" readonly>
                            <button type="button" class="btn btn-primary aff-copy-btn" onclick="copyAgLink('genInput')">Copiar</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>

<script>
var AG_REF = <?= json_encode($agency['ref_code']) ?>;
function copyAgLink(id){
    var el = document.getElementById(id);
    el.select(); el.setSelectionRange(0, 99999);
    try { navigator.clipboard.writeText(el.value); } catch(e){ document.execCommand('copy'); }
    var btn = el.closest('.aff-link-copy-box').querySelector('.aff-copy-btn');
    var orig = btn.innerHTML; btn.classList.add('btn-copied'); btn.textContent = 'Copiado!';
    setTimeout(function(){ btn.classList.remove('btn-copied'); btn.innerHTML = orig; }, 1800);
}
function generateAgLink(){
    var url = document.getElementById('customUrl').value.trim();
    if (!url) { alert('Cole uma URL do site no campo acima.'); return; }
    var sep = url.includes('?') ? '&' : '?';
    document.getElementById('genInput').value = url + sep + 'ag=' + encodeURIComponent(AG_REF);
    document.getElementById('genResult').style.display = 'block';
}
</script>
