<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado - Punta Cana para Brasileiros</title>
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #f0fdf4 100%); color: #1C2011; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; overflow: hidden; }
        .error-container { max-width: 560px; width: 100%; text-align: center; background: #fff; border-radius: 20px; padding: 60px 48px; box-shadow: 0 20px 60px rgba(28,32,17,0.08), 0 1px 3px rgba(0,0,0,0.04); position: relative; z-index: 1; }
        .error-icon { width: 80px; height: 80px; margin: 0 auto 24px; background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .error-icon svg { color: #d97706; }
        .error-number { font-size: 64px; font-weight: 800; color: #d97706; line-height: 1; margin-bottom: 12px; opacity: 0.8; }
        h1 { font-size: 26px; font-weight: 700; color: #1C2011; margin-bottom: 12px; }
        p { font-size: 15px; color: #636e72; line-height: 1.7; margin-bottom: 32px; }
        .btn-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-home { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: #1B6F00; color: #fff; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; box-shadow: 0 4px 12px rgba(27,111,0,0.2); }
        .btn-home:hover { background: #155c00; transform: translateY(-1px); }
        .btn-login { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: #fff; color: #1C2011; border: 1.5px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; }
        .btn-login:hover { border-color: #1B6F00; color: #1B6F00; }
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
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        </div>
        <div class="error-number">403</div>
        <h1>Acesso negado</h1>
        <p>Você não tem permissão para acessar esta página. Se acredita que isso é um erro, faça login ou entre em contato com o suporte.</p>
        <div class="btn-actions">
            <a href="/" class="btn-home">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Ir para o início
            </a>
            <a href="/login" class="btn-login">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Fazer login
            </a>
        </div>
    </div>
</body>
</html>
