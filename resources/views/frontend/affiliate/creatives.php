<section class="passeios-hero"><div class="container"><div class="passeios-hero-content"><h1>Painel do Afiliado</h1></div></div></section>
<section class="section section-affiliate-panel">
    <div class="container">
        <?= partial('affiliate-nav', ['active' => 'criativos']) ?>
        <div class="affiliate-panel-content">
            <h3 class="affiliate-section-title">Materiais Promocionais</h3>
            <p class="text-muted" style="margin-bottom:20px">Use estes criativos para divulgar em suas redes sociais.</p>
            <div class="creatives-grid">
                <!-- TODO: Puxar criativos do banco/admin -->
                <div class="creative-card">
                    <div class="creative-img"><img src="<?= asset('images/placeholder.jpg') ?>" alt="Criativo" loading="lazy"></div>
                    <div class="creative-actions">
                        <a href="#" class="creative-link">&#128065; Ver</a>
                        <button type="button" class="creative-link" onclick="alert('Link copiado!')">&#128203; Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
