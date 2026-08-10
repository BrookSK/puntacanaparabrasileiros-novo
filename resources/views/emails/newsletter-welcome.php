<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="font-family:'Poppins',Arial,sans-serif;background:#f4f7f4;margin:0;padding:20px">
<div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06)">
    <!-- Header com logo -->
    <div style="background:linear-gradient(135deg,#1B6F00 0%,#228B22 100%);padding:35px 30px;text-align:center">
        <img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/punta_cana_para_brasileiros_logo.png" alt="Punta Cana para Brasileiros" style="height:60px;margin-bottom:15px" onerror="this.style.display='none'">
        <h1 style="margin:0;font-size:22px;color:#ffffff;font-weight:600">Bem-vindo à nossa Newsletter!</h1>
    </div>

    <!-- Conteúdo -->
    <div style="padding:35px 30px">
        <p style="font-size:16px;color:#333;line-height:1.6;margin:0 0 20px">
            Olá! 👋
        </p>
        <p style="font-size:15px;color:#444;line-height:1.7;margin:0 0 15px">
            Sua inscrição na newsletter da <strong>Punta Cana para Brasileiros</strong> foi confirmada com sucesso!
        </p>
        <p style="font-size:15px;color:#444;line-height:1.7;margin:0 0 25px">
            A partir de agora você vai receber em primeira mão:
        </p>

        <div style="background:#f0faf0;border-left:4px solid #1B6F00;padding:15px 20px;border-radius:0 8px 8px 0;margin:0 0 25px">
            <p style="margin:0 0 8px;font-size:14px;color:#333">✅ Dicas exclusivas de Punta Cana</p>
            <p style="margin:0 0 8px;font-size:14px;color:#333">✅ Ofertas e promoções especiais</p>
            <p style="margin:0 0 8px;font-size:14px;color:#333">✅ Roteiros e guias de viagem</p>
            <p style="margin:0;font-size:14px;color:#333">✅ Novidades sobre passeios e experiências</p>
        </div>

        <p style="font-size:15px;color:#444;line-height:1.7;margin:0 0 25px">
            Enquanto isso, explore nossos passeios e comece a planejar sua viagem dos sonhos para Punta Cana!
        </p>

        <!-- CTA Button -->
        <div style="text-align:center;margin:30px 0">
            <a href="<?= e($siteUrl ?? 'https://puntacanaparabrasileiros.com') ?>/passeios" style="display:inline-block;background:#1B6F00;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;font-weight:600">
                Ver Passeios em Punta Cana
            </a>
        </div>

        <p style="font-size:14px;color:#666;line-height:1.6;margin:25px 0 0">
            Um abraço,<br>
            <strong style="color:#1B6F00">Equipe Punta Cana para Brasileiros</strong>
        </p>
    </div>

    <!-- Footer -->
    <div style="background:#f8f8f8;padding:20px 30px;text-align:center;border-top:1px solid #eee">
        <p style="margin:0 0 8px;font-size:12px;color:#999">
            Você recebeu este email porque se inscreveu em nossa newsletter.
        </p>
        <p style="margin:0;font-size:12px;color:#999">
            © <?= date('Y') ?> Punta Cana para Brasileiros. Todos os direitos reservados.
        </p>
    </div>
</div>
</body></html>
