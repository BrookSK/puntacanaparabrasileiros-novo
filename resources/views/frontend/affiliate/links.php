<section class="passeios-hero"><div class="container"><div class="passeios-hero-content"><h1>Painel do Afiliado</h1></div></div></section>
<section class="section section-affiliate-panel">
    <div class="container">
        <?= partial('affiliate-nav', ['active' => 'links']) ?>
        <div class="affiliate-panel-content">
            <div class="affiliate-card">
                <h3>Seu Link De Afiliado</h3>
                <p class="text-muted">Esta é a sua URL de referência. Compartilhe com seu público para ganhar comissões.</p>
                <div class="affiliate-link-box">
                    <input type="text" class="form-control" value="<?= e(setting('site_url', 'https://puntacanaparabrasileiros.com')) ?>/?ref=<?= (int)($affiliate['id'] ?? 1) ?>" id="affiliateLink" readonly>
                </div>
                <button type="button" class="btn btn-primary" onclick="copyLink()" style="margin-top:12px">&#128203; Copy</button>
            </div>

            <div class="affiliate-card" style="margin-top:20px">
                <h3>Gerar Link De Afiliado</h3>
                <p class="text-muted">Adicione qualquer URL deste site no campo abaixo para gerar um link de referência.</p>
                <div class="form-group">
                    <input type="text" class="form-control" id="customUrl" placeholder="Cole o link aqui">
                </div>
                <button type="button" class="btn btn-primary" onclick="generateLink()">Gerar</button>
                <div id="generatedLink" style="margin-top:12px;display:none">
                    <input type="text" class="form-control" id="generatedLinkInput" readonly>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
function copyLink() { const el = document.getElementById('affiliateLink'); el.select(); document.execCommand('copy'); alert('Link copiado!'); }
function generateLink() { const url = document.getElementById('customUrl').value; if(!url) return; const sep = url.includes('?') ? '&' : '?'; document.getElementById('generatedLinkInput').value = url + sep + 'ref=<?= (int)($affiliate['id'] ?? 1) ?>'; document.getElementById('generatedLink').style.display = 'block'; }
</script>
