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
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #fef2f2 0%, #fff7ed 50%, #fefce8 100%); color: #1C2011; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; overflow: hidden; }
        body::before { content: ''; position: absolute; top: -50%; right: -20%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(231,76,60,0.06) 0%, transparent 70%); border-radius: 50%; }
        .error-container { max-width: 560px; width: 100%; text-align: center; background: #fff; border-radius: 20px; padding: 60px 48px; box-shadow: 0 20px 60px rgba(28,32,17,0.08), 0 1px 3px rgba(0,0,0,0.04); position: relative; z-index: 1; }
        .error-icon { width: 80px; height: 80px; margin: 0 auto 24px; background: linear-gradient(135deg, #fee2e2, #fecaca); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .error-icon svg { color: #dc2626; }
        .error-number { font-size: 64px; font-weight: 800; color: #dc2626; line-height: 1; margin-bottom: 12px; opacity: 0.8; }
        h1 { font-size: 26px; font-weight: 700; color: #1C2011; margin-bottom: 12px; }
        p { font-size: 15px; color: #636e72; line-height: 1.7; margin-bottom: 32px; }
        .btn-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-home { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: #1B6F00; color: #fff; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; box-shadow: 0 4px 12px rgba(27,111,0,0.2); }
        .btn-home:hover { background: #155c00; transform: translateY(-1px); }
        .btn-retry { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: #fff; color: #1C2011; border: 1.5px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; cursor: pointer; }
        .btn-retry:hover { border-color: #1B6F00; color: #1B6F00; }
        .error-footer { margin-top: 32px; padding-top: 24px; border-top: 1px solid #f0f0f0; font-size: 12px; color: #999; }
        .error-footer a { color: #1B6F00; text-decoration: none; font-weight: 500; }
        @media (max-width: 768px) {
            .error-container { padding: 40px 28px; }
            .error-number { font-size: 48px; }
            h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="error-number">500</div>
        <h1>Erro interno do servidor</h1>
        <p>Desculpe pelo inconveniente! Algo deu errado do nosso lado. Nossa equipe já foi notificada e está trabalhando para resolver.</p>
        <div class="btn-actions">
            <a href="/" class="btn-home">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Ir para o início
            </a>
            <a href="javascript:history.back()" class="btn-retry">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                Tentar novamente
            </a>
        </div>
        <div class="error-footer">
            Precisa de ajuda? <a href="/contato">Entre em contato conosco</a>
        </div>

        <?php if (!empty($debug) && !empty($trace)): ?>
        <div style="margin-top:24px;padding:16px;background:#1e293b;border-radius:8px;text-align:left;overflow-x:auto;">
            <p style="margin:0 0 8px;font-size:12px;color:#f87171;font-weight:600;">DEBUG (visível apenas em desenvolvimento):</p>
            <p style="margin:0 0 8px;font-size:13px;color:#fbbf24;word-break:break-word;"><?= htmlspecialchars($message ?? '') ?></p>
            <pre style="margin:0;font-size:11px;color:#94a3b8;white-space:pre-wrap;word-break:break-all;"><?= htmlspecialchars($trace ?? '') ?></pre>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
