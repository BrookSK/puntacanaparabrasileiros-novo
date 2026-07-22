<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada - Punta Cana para Brasileiros</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #fff; color: #1C2011; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .error-container { max-width: 900px; width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,0.06); }
        .error-left { background: #eef2f7; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 40px; min-height: 400px; }
        .error-number { font-size: 120px; font-weight: 700; color: #7b8fa8; opacity: 0.6; line-height: 1; margin-bottom: 30px; }
        .error-quote { font-size: 14px; color: #666; font-style: italic; text-align: center; line-height: 1.7; max-width: 300px; }
        .error-right { padding: 50px 40px 50px 0; }
        .error-right h1 { font-size: 34px; font-weight: 700; color: #1C2011; margin-bottom: 14px; line-height: 1.2; }
        .error-right p { font-size: 15px; color: #636e72; line-height: 1.7; margin-bottom: 24px; }
        .btn-home { display: inline-block; padding: 13px 28px; background: #1C2011; color: #fff; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: background .2s; margin-bottom: 20px; }
        .btn-home:hover { background: #000; }
        .search-box { display: flex; align-items: center; gap: 8px; border: 1px solid #ddd; border-radius: 8px; padding: 10px 14px; margin-bottom: 24px; max-width: 320px; }
        .search-box svg { flex-shrink: 0; color: #999; }
        .search-box input { flex: 1; border: none; outline: none; font-size: 14px; font-family: 'Poppins', sans-serif; }
        .suggestions-title { font-size: 14px; font-weight: 700; color: #1C2011; margin-bottom: 12px; }
        .suggestions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; max-width: 320px; }
        .suggestion-link { display: block; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 8px; text-align: center; font-size: 13px; font-weight: 500; color: #1C2011; text-decoration: none; transition: all .2s; }
        .suggestion-link:hover { border-color: #3772C0; color: #3772C0; background: #f7f9ff; }
        @media (max-width: 768px) {
            .error-container { grid-template-columns: 1fr; }
            .error-left { min-height: 200px; padding: 40px 20px; }
            .error-number { font-size: 80px; }
            .error-right { padding: 30px 20px; }
            .error-right h1 { font-size: 26px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Lado esquerdo -->
        <div class="error-left">
            <div class="error-number">404</div>
            <p class="error-quote">"O verdadeiro objetivo da viagem não é chegar ao destino, mas desfrutar do caminho."</p>
        </div>

        <!-- Lado direito -->
        <div class="error-right">
            <h1>Página não encontrada</h1>
            <p>Ops! Parece que você se perdeu durante sua jornada. A página que você tentou acessar não existe.</p>

            <a href="/" class="btn-home">&larr; Voltar para o início</a>

            <form action="/blog" method="GET" class="search-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="busca" placeholder="Buscar no blog...">
            </form>

            <p class="suggestions-title">Talvez você esteja procurando por:</p>
            <div class="suggestions-grid">
                <a href="/" class="suggestion-link">Página Inicial</a>
                <a href="/passeios" class="suggestion-link">Passeios</a>
                <a href="/blog" class="suggestion-link">Blog</a>
                <a href="/contato" class="suggestion-link">Contato</a>
            </div>
        </div>
    </div>
</body>
</html>
