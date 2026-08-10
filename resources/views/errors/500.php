<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro do Servidor - Punta Cana para Brasileiros</title>
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%); color: #1C2011; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; overflow: hidden; }
        body::before { content: ''; position: absolute; top: -60%; right: -30%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(228,181,5,0.08) 0%, transparent 70%); border-radius: 50%; }
        body::after { content: ''; position: absolute; bottom: -40%; left: -20%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(27,111,0,0.06) 0%, transparent 70%); border-radius: 50%; }
        .error-container { max-width: 880px; width: 100%; display: grid; grid-template-columns: 1fr 1.2fr; gap: 0; align-items: stretch; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(28,32,17,0.08), 0 1px 3px rgba(0,0,0,0.04); position: relative; z-index: 1; }
        .error-left { background: linear-gradient(160deg, #1C2011 0%, #2d3a1e 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 40px; position: relative; overflow: hidden; }
        .error-left::before { content: ''; position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(228,181,5,0.15); border-radius: 50%; }
        .error-left::after { content: ''; position: absolute; bottom: -20px; left: -20px; width: 80px; height: 80px; background: rgba(27,111,0,0.2); border-radius: 50%; }
        .error-badge { display: inline-block; padding: 6px 16px; background: rgba(231,76,60,0.2); border: 1px solid rgba(231,76,60,0.4); border-radius: 20px; font-size: 11px; font-weight: 600; color: #f87171; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 20px; }
        .error-number { font-size: 140px; font-weight: 800; color: #fff; line-height: 1; margin-bottom: 16px; text-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .error-icon { font-size: 48px; margin-bottom: 20px; opacity: 0.8; }
        .error-quote { font-size: 13px; color: rgba(255,255,255,0.6); font-style: italic; text-align: center; line-height: 1.7; max-width: 260px; }
        .error-right { padding: 60px 48px; display: flex; flex-direction: column; justify-content: center; }
        .error-right h1 { font-size: 30px; font-weight: 700; color: #1C2011; margin-bottom: 12px; line-height: 1.3; }
        .error-right p { font-size: 15px; color: #636e72; line-height: 1.7; margin-bottom: 28px; }
        .btn-home { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: #1B6F00; color: #fff; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(27,111,0,0.2); }
        .btn-home:hover { background: #155c00; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(27,111,0,0.3); }
        .btn-retry { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: #fff; color: #1C2011; border: 1.5px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; margin-bottom: 28px; }
        .btn-retry:hover { border-color: #1B6F00; color: #1B6F00; }
        .btn-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
        .suggestions-title { font-size: 13px; font-weight: 600; color: #636e72; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .suggestions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; max-width: 340px; }
        .suggestion-link { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 12px 16px; border: 1.5px solid #e8e8e8; border-radius: 10px; font-size: 13px; font-weight: 500; color: #1C2011; text-decoration: none; transition: all .2s; }
        .suggestion-link:hover { border-color: #1B6F00; color: #1B6F00; background: #f0fdf4; }
        .debug-box { margin-top: 20px; padding: 16px; background: #1e293b; border-radius: 8px; text-align: left; overflow-x: auto; max-width: 100%; }
        .debug-box p { margin: 0 0 8px; font-size: 12px; color: #f87171; font-weight: 600; }
        .debug-box .msg { margin: 0 0 8px; font-size: 13px; color: #fbbf24; word-break: break-word; }
        .debug-box pre { margin: 0; font-size: 11px; color: #94a3b8; white-space: pre-wrap; word-break: break-all; }
        @media (max-width: 768px) {
            .error-container { grid-template-columns: 1fr; max-width: 440px; }
            .error-left { min-height: auto; padding: 40px 30px; }
            .error-number { font-size: 90px; }
            .error-right { padding: 36px 28px; }
            .error-right h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-left">
            <span class="error-badge">Erro!</span>
            <div class="error-icon">&#9888;&#65039;</div>
            <div class="error-number">500</div>
            <p class="error-quote">"Até nos melhores paraísos, às vezes o mar fica agitado."</p>
        </div>
        <div class="error-right">
            <h1>Erro interno do servidor</h1>
            <p>Desculpe pelo inconveniente! Algo deu errado do nosso lado. Nossa equipe já foi notificada e está trabalhando para resolver.</p>
            <div class="btn-actions">
                <a href="/" class="btn-home">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Voltar para o início
                </a>
                <a href="javascript:history.back()" class="btn-retry">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                    Tentar novamente
                </a>
            </div>
            <p class="suggestions-title">Talvez você esteja procurando:</p>
            <div class="suggestions-grid">
                <a href="/" class="suggestion-link">Início</a>
                <a href="/passeios" class="suggestion-link">Passeios</a>
                <a href="/transfers" class="suggestion-link">Transfers</a>
                <a href="/contato" class="suggestion-link">Contato</a>
            </div>
            <?php if (!empty($debug) && !empty($trace)): ?>
            <div class="debug-box">
                <p>DEBUG (apenas desenvolvimento):</p>
                <p class="msg"><?= htmlspecialchars($message ?? '') ?></p>
                <pre><?= htmlspecialchars($trace ?? '') ?></pre>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
