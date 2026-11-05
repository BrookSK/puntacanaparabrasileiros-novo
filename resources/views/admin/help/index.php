<?php
/**
 * Tutorial "Como Usar" — guia completo, passo a passo, em linguagem simples.
 * Cada seção: onde fica, para que serve, passo a passo, exemplos, o que NÃO fazer.
 */
$sections = [
    ['id' => 'comeco', 'title' => 'Comece aqui: como o painel funciona'],
    ['id' => 'urgente', 'title' => 'Atalhos: "quero fazer X, onde clico?"'],
    ['id' => 'passeio-novo', 'title' => 'Passeios: criar um passeio novo'],
    ['id' => 'passeio-campos', 'title' => 'Passeios: cada campo da tela explicado'],
    ['id' => 'pacotes', 'title' => 'Pacotes / modalidades (ex.: Front Row)'],
    ['id' => 'precos', 'title' => 'Preços normais e Cortesia (bebê grátis)'],
    ['id' => 'precos-dia', 'title' => 'Preço diferente por dia da semana ou data'],
    ['id' => 'dias', 'title' => 'Dias fechados e datas especiais'],
    ['id' => 'horarios', 'title' => 'Horários de saída do passeio'],
    ['id' => 'hoteis', 'title' => 'Horários de busca por hotel'],
    ['id' => 'exemplos', 'title' => 'Exemplos completos (Coco Bongo, Golfinhos, Saona)'],
    ['id' => 'faixas', 'title' => 'Faixas etárias (idades)'],
    ['id' => 'politicas', 'title' => 'Políticas, câmbio e textos do site'],
    ['id' => 'transfers', 'title' => 'Transfers: locais, veículos, rotas e preços'],
    ['id' => 'transfer-horarios', 'title' => 'Transfers: como o cliente informa os horários'],
    ['id' => 'reservas', 'title' => 'Reservas: acompanhar, mudar status, pagamentos'],
    ['id' => 'reserva-manual', 'title' => 'Reserva manual (venda fora do site)'],
    ['id' => 'vouchers', 'title' => 'Vouchers'],
    ['id' => 'cancelamentos', 'title' => 'Cancelamentos e regras de reembolso'],
    ['id' => 'categorias', 'title' => 'Categorias de passeios'],
    ['id' => 'cupons', 'title' => 'Cupons de desconto'],
    ['id' => 'equipe', 'title' => 'Equipe (página Sobre Nós)'],
    ['id' => 'agendamentos', 'title' => 'Agendamentos de videochamada'],
    ['id' => 'afiliados', 'title' => 'Afiliados'],
    ['id' => 'agencias', 'title' => 'Agências parceiras'],
    ['id' => 'newsletter', 'title' => 'Newsletter'],
    ['id' => 'usuarios', 'title' => 'Usuários e permissões'],
    ['id' => 'whatsapp', 'title' => 'WhatsApp, CRM e Chat Interno'],
    ['id' => 'relatorios', 'title' => 'Dashboard e Relatórios'],
    ['id' => 'suporte', 'title' => 'Suporte (pedir ajuda técnica)'],
    ['id' => 'config', 'title' => 'Configurações (só superadmin)'],
    ['id' => 'cliente', 'title' => 'O que o cliente vê ao reservar'],
    ['id' => 'nao-fazer', 'title' => 'O que NUNCA fazer'],
    ['id' => 'problemas', 'title' => 'Problemas comuns e soluções'],
    ['id' => 'limites', 'title' => 'O que o sistema ainda NÃO faz'],
];
?>

<div class="card-header">
    <div>
        <h2>Como Usar o Painel</h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Guia completo, passo a passo. Use o índice ao lado ou aperte <strong>Ctrl + F</strong> e digite a palavra que procura (ex.: "câmbio", "bebê", "segunda").</p>
    </div>
</div>

<div class="help-layout">
    <nav class="help-toc admin-card">
        <strong>Índice</strong>
        <ol>
            <?php foreach ($sections as $s): ?>
            <li><a href="#<?= $s['id'] ?>"><?= e($s['title']) ?></a></li>
            <?php endforeach; ?>
        </ol>
    </nav>

    <div class="help-content">

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="comeco">
            <h3>1. Comece aqui: como o painel funciona</h3>
            <h4>Regras de ouro</h4>
            <ol>
                <li><strong>Nada muda no site até você clicar no botão verde de salvar</strong> (fica sempre no final da tela: "Salvar", "Salvar preços", "Salvar dias e horários" etc.). Se fechar a aba antes, perde o que digitou.</li>
                <li>Depois de salvar, aparece uma <strong>faixa verde</strong> no topo ("…salvos!"). Se não apareceu, não salvou.</li>
                <li>Se aparecer <strong>faixa vermelha</strong>, leia a mensagem: diz o que falta (ex.: "Nome do hotel é obrigatório"). Corrija e salve de novo.</li>
                <li>Tudo vale <strong>na hora</strong> no site. Pode fazer de madrugada, fim de semana, sem chamar a equipe técnica.</li>
                <li>Para conferir como ficou para o cliente: menu <strong>Ver Site</strong> (último item do menu da esquerda, abre em nova aba). Se não mudou, aperte <strong>F5</strong> (ou puxe a página para baixo no celular) para recarregar.</li>
                <li>Todos os <strong>preços são em dólar (USD)</strong>. Use ponto ou vírgula para centavos (60.50 ou 60,50). O real (R$) só aparece no Pix, usando o câmbio de <a href="#politicas">Políticas e Câmbio</a>.</li>
                <li>Os <strong>horários são no formato 24h</strong>: 07:00, 13:30, 21:00. No campo de hora, clique e digite os números; ou use a setinha/relógio do navegador.</li>
                <li>Datas: clique no campo e escolha no calendário que abre.</li>
            </ol>
            <h4>Onde fica cada coisa</h4>
            <ul>
                <li><strong>Menu da esquerda</strong> = todas as áreas. No celular, ele fica escondido no botão ☰ (três tracinhos) no topo.</li>
                <li>Cada pessoa vê só as áreas permitidas pelo cargo dela (ver <a href="#usuarios">Usuários</a>). Se falta algum menu para você, seu usuário não é Admin.</li>
                <li>Quase toda lista tem um botão <strong>Editar</strong> na linha do item e um botão <strong>+ Novo…</strong> no topo.</li>
            </ul>
            <div class="help-tip"><strong>Dica:</strong> trabalhe com o painel numa aba e o site em outra. Salvou no painel → vá na aba do site → F5 → confira.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="urgente">
            <h3>2. Atalhos: "quero fazer X, onde clico?"</h3>
            <table class="table help-table">
                <thead><tr><th style="width:38%">Quero…</th><th>Caminho exato</th></tr></thead>
                <tbody>
                    <tr><td>Mudar o câmbio (US$ 1 = R$ 6,00)</td><td>Menu <strong>Políticas e Câmbio</strong> → campo <em>US$ 1 = R$</em> → digite <code>6,00</code> → botão <strong>Salvar políticas e câmbio</strong> (fim da página).</td></tr>
                    <tr><td>Passeio esgotou / fornecedor fechou num dia</td><td><strong>Passeios</strong> → na linha do passeio, botão <strong>Dias e Horários</strong> → bloco 2 → <strong>+ Adicionar data especial</strong> → "O que fazer": <em>Fechar (não vender)</em> → "De": a data → <strong>Salvar dias e horários</strong>.</td></tr>
                    <tr><td>Fechar vários dias seguidos (período)</td><td>Igual ao de cima, mas preencha também "Até (opcional)" com o último dia.</td></tr>
                    <tr><td>Passeio nunca funciona às segundas</td><td><strong>Dias e Horários</strong> → bloco 1 → clique em <em>Segunda</em> (fica vermelho) → Salvar.</td></tr>
                    <tr><td>Abrir excepcionalmente num dia que é fechado</td><td><strong>Dias e Horários</strong> → bloco 2 → + Adicionar data especial → <em>Abrir excepcionalmente</em> → data → Salvar.</td></tr>
                    <tr><td>Reabrir um dia que eu tinha fechado</td><td><strong>Dias e Horários</strong> → bloco 2 → na linha daquela data, botão vermelho <strong>Remover</strong> → Salvar.</td></tr>
                    <tr><td>Tirar um horário de saída em certos dias</td><td><strong>Dias e Horários</strong> → bloco 3 → na linha do horário, clique nos dias para desmarcar (ficam brancos) → Salvar.</td></tr>
                    <tr><td>Mudar o preço de um passeio</td><td><strong>Passeios</strong> → botão <strong>Preços</strong> → tabela "Preço normal" → altere → <strong>Salvar preços</strong>.</td></tr>
                    <tr><td>Preço mais caro sexta e sábado</td><td><strong>Preços</strong> → <strong>+ Adicionar preço diferente</strong> → Quando: <em>Dia(s) da semana</em> → marque Sex e Sáb → preço → Salvar. <a href="#precos-dia">Detalhes</a>.</td></tr>
                    <tr><td>Bebê grátis, mas contando na reserva</td><td><strong>Preços</strong> → linha "Infantil" → Preço <code>0</code> → Salvar. <a href="#precos">Detalhes</a>.</td></tr>
                    <tr><td>Mudar a idade de criança / bebê</td><td>Menu <strong>Faixas Etárias</strong> → mude "Idade de"/"Idade até" → <strong>Salvar</strong>.</td></tr>
                    <tr><td>Hotel não aparece quando o cliente digita</td><td><strong>Passeios</strong> → Editar → card <strong>Horários por Hotel</strong> → <strong>+ Adicionar Hotel</strong>. <a href="#hoteis">Detalhes</a>.</td></tr>
                    <tr><td>Mudar texto da política de pagamento / cancelamento</td><td>Menu <strong>Políticas e Câmbio</strong> → caixa de texto correspondente → Salvar.</td></tr>
                    <tr><td>Esconder um passeio do site (sem apagar)</td><td><strong>Passeios</strong> → Editar → coluna da direita, card <strong>Publicação</strong> → Status: <em>Rascunho</em> ou <em>Desativado</em> → Salvar.</td></tr>
                    <tr><td>Criar passeio parecido com outro</td><td><strong>Passeios</strong> → botão <strong>Duplicar</strong> na linha do passeio → confirme → edite a cópia.</td></tr>
                    <tr><td>Ver as vendas</td><td>Menu <strong>Reservas</strong> (lista) ou <strong>Relatórios</strong> (números por período).</td></tr>
                    <tr><td>Cliente pagou o restante no destino</td><td><strong>Reservas</strong> → <strong>Ver</strong> → botão <strong>Registrar pagamento do restante</strong>. <a href="#reservas">Detalhes</a>.</td></tr>
                    <tr><td>Reenviar o voucher ao cliente</td><td>Menu <strong>Vouchers</strong> → na linha → <strong>Enviar</strong>.</td></tr>
                    <tr><td>Criar um cupom de desconto</td><td>Menu <strong>Cupons</strong> → <strong>+ Novo Cupom</strong>. <a href="#cupons">Detalhes</a>.</td></tr>
                    <tr><td>Mudar quanto tempo antes do voo o transfer busca</td><td>Padrão geral: <strong>Políticas e Câmbio</strong> → "Horas antes do voo". Por hotel/região: <strong>Transfers → Gerenciar Locais</strong> → Editar → "Busca antes do voo (h)".</td></tr>
                    <tr><td>Dar acesso ao painel para alguém</td><td>Menu <strong>Usuários</strong> → <strong>+ Novo Usuário</strong>. <a href="#usuarios">Detalhes</a>.</td></tr>
                    <tr><td>Pedir algo à equipe técnica</td><td>Menu <strong>Suporte</strong> → nova demanda. <a href="#suporte">Detalhes</a>.</td></tr>
                </tbody>
            </table>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="passeio-novo">
            <h3>3. Passeios: criar um passeio novo</h3>
            <p><strong>Onde:</strong> menu <strong>Passeios</strong>. A lista mostra todos os passeios. Use o filtro "Todos os Status" + <strong>Filtrar</strong> para ver só Publicados, Rascunhos ou Desativados.</p>
            <p>Cada linha tem os botões: <strong>Editar</strong> (dados do passeio) · <strong>Preços</strong> · <strong>Dias e Horários</strong> · <strong>Duplicar</strong> · <strong>Excluir</strong>.</p>
            <h4>Passo a passo (faça nesta ordem)</h4>
            <ol class="steps">
                <li>Clique em <strong>+ Novo Passeio</strong> (canto superior). <em>Ou</em>, se já existe um passeio parecido, clique em <strong>Duplicar</strong> nele: a cópia vem com "(Cópia)" no nome, como Rascunho, com pacotes, preços, hotéis, horários e fotos. Só ajuste o que muda.</li>
                <li>Preencha o card <strong>Informações Básicas</strong>: Título (obrigatório), Duração, Descrição Curta, Descrição Completa etc. (<a href="#passeio-campos">cada campo explicado</a>).</li>
                <li>Role até o card <strong>Pacotes</strong>: crie pelo menos 1 pacote e marque as <strong>Categorias de Viajante</strong> (Adulto, Criança, Infantil). <a href="#pacotes">Detalhes</a>. <strong>Sem pacote o passeio não tem preço e não pode ser vendido.</strong></li>
                <li>Na coluna da direita, card <strong>Publicação</strong>: deixe <em>Rascunho</em> por enquanto. Coloque a <strong>Imagem Destacada</strong> (foto principal) e a <strong>Galeria de Fotos</strong>.</li>
                <li>Clique em <strong>Salvar</strong> (fim da página). O passeio é criado e você volta para a tela de edição.</li>
                <li>Clique no botão <strong>Preços</strong> (no topo da tela de edição) e preencha os preços. Salve.</li>
                <li>Clique em <strong>Dias e Horários</strong> e configure dias fechados e horários. Salve.</li>
                <li>Volte em <strong>Editar</strong> → card <strong>Horários por Hotel</strong> → cadastre os hotéis e horários de busca (ou importe planilha).</li>
                <li>Confira no site (Ver Site). Estando tudo certo: Editar → Publicação → Status <strong>Publicado</strong> → Salvar.</li>
            </ol>
            <div class="help-dont"><strong>Não faça:</strong> publicar antes de colocar preços e horários. O cliente pode ver preço errado ou horários que não existem.</div>
            <h4>Excluir x Desativar</h4>
            <ul>
                <li><strong>Excluir</strong> apaga para sempre (preços, hotéis, horários, fotos do passeio). Não tem como desfazer.</li>
                <li><strong>Desativado/Rascunho</strong> só esconde do site. É o recomendado para passeio que parou temporariamente.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="passeio-campos">
            <h3>4. Passeios: cada campo da tela explicado</h3>
            <p>Tela: <strong>Passeios → Editar</strong>. Os cards ficam um embaixo do outro; a coluna da direita tem Publicação, fotos e Pagamento Parcial.</p>
            <table class="table help-table">
                <thead><tr><th style="width:28%">Card / campo</th><th>O que é e como preencher</th></tr></thead>
                <tbody>
                    <tr><td><strong>Título</strong></td><td>Nome do passeio no site. Obrigatório.</td></tr>
                    <tr><td><strong>Duração + Unidade</strong></td><td>Número e "Horas" ou "Dias". Ex.: 8 Horas.</td></tr>
                    <tr><td><strong>Mín./Máx. Passageiros</strong></td><td>Informativo de capacidade do grupo.</td></tr>
                    <tr><td><strong>Descrição Curta</strong></td><td>2–3 linhas. Aparece nos cards da lista de passeios.</td></tr>
                    <tr><td><strong>Descrição Completa</strong></td><td>Texto grande da página do passeio.</td></tr>
                    <tr><td><strong>Ponto de Encontro</strong></td><td>Ex.: "Lobby do hotel".</td></tr>
                    <tr><td><strong>Saída do hotel — De / Até</strong> e <strong>Retorno ao hotel — De / Até</strong></td><td>Só <strong>texto informativo</strong> na página ("Saída do hotel: 07:00 – 08:00"). Deixe "Até" vazio para mostrar só um horário. <strong>Não</strong> define os horários que o cliente escolhe: isso fica em <a href="#horarios">Dias e Horários</a>.</td></tr>
                    <tr><td><strong>Disponibilidade</strong></td><td>Texto informativo (padrão: "Sujeita a quórum, capacidade e clima"). <strong>Não bloqueia</strong> datas. Para bloquear use <a href="#dias">Dias e Horários</a>.</td></tr>
                    <tr><td><strong>Notas Importantes</strong></td><td>Avisos (ex.: "Levar passaporte").</td></tr>
                    <tr><td><strong>Detalhes do passeio</strong></td><td>Cards de destaque abaixo do título. "+ Adicionar" para cada um.</td></tr>
                    <tr><td><strong>Vídeo do Passeio</strong></td><td>Cole o link do YouTube (o endereço do vídeo).</td></tr>
                    <tr><td><strong>FAQ do Passeio</strong></td><td>Perguntas e respostas. "+ Adicionar Pergunta"; "Remover" apaga a pergunta.</td></tr>
                    <tr><td><strong>Categorias</strong></td><td>Clique e escolha em quais grupos o passeio aparece (Praias, Aventura…). Pode escolher vários.</td></tr>
                    <tr><td><strong>Documentos Extras</strong></td><td>PDFs para o cliente baixar (roteiro, regras).</td></tr>
                    <tr><td><strong>O que Inclui / Não Inclui / O que levar / Ideal para</strong></td><td>Listas. Uma frase por linha; "+ Adicionar" cria nova linha, "×" apaga.</td></tr>
                    <tr><td><strong>Roteiro</strong></td><td>Etapas em ordem. Campo de hora (opcional) + título + descrição. Dica: começar a descrição com "7h00 – 8h00 | Busca no hotel" mostra a faixa no selo verde.</td></tr>
                    <tr><td><strong>Tabela de Preços por Grupo</strong></td><td>Marque "Ativar" só se o preço é fechado por quantidade de <strong>adultos</strong> (ex.: 1 adulto $150, 2 adultos $200). Cada faixa: nº de adultos + preço total. Criança/infantil continuam com o preço da tela Preços.</td></tr>
                    <tr><td><strong>Pacotes por Composição</strong></td><td>Marque "Ativar" só para passeios vendidos por veículo/unidade (ex.: buggy). Cada opção: nome que o cliente vê, total de pessoas, preço total, quantos veículos, nome do veículo, pessoas por veículo. O cliente escolhe quantas pessoas e o site mostra as opções que batem.</td></tr>
                    <tr><td><strong>Acompanhantes</strong></td><td>Para quem vai junto sem fazer a atividade. Marque "Permitir", dê um nome (ex.: "Acompanhante sem nado"), preço e limites.</td></tr>
                    <tr><td><strong>Pacotes</strong></td><td>As modalidades do passeio com as faixas etárias. <a href="#pacotes">Ver seção própria</a>.</td></tr>
                    <tr><td><strong>SEO (Meta Title / Description)</strong></td><td>Texto para o Google. Pode deixar vazio (usa título e descrição curta).</td></tr>
                    <tr><td><strong>Horários por Hotel</strong></td><td>Hotéis e horários de busca. <a href="#hoteis">Ver seção própria</a>. Só aparece depois que o passeio foi salvo uma vez.</td></tr>
                    <tr><td><strong>Publicação</strong> (direita)</td><td>Status: Rascunho (escondido), Publicado (no site), Desativado (escondido). Dificuldade. "Passeio em Destaque" = aparece na home.</td></tr>
                    <tr><td><strong>Imagem Destacada / Galeria</strong> (direita)</td><td>"Escolher imagem"/"Escolher Imagens". Use fotos JPG/PNG de boa qualidade, de preferência deitadas (horizontais).</td></tr>
                    <tr><td><strong>Pagamento Parcial / % Depósito</strong> (direita)</td><td>Marque para o cliente pagar só um sinal na reserva. Em "% Depósito" coloque o percentual (ex.: 50). Esse número entra no texto "Como reservar e pagar" (<code>{sinal}</code>).</td></tr>
                </tbody>
            </table>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="pacotes">
            <h3>5. Pacotes / modalidades (ex.: Pista Regular, Front Row)</h3>
            <p><strong>Onde:</strong> Passeios → Editar → card <strong>Pacotes</strong>.</p>
            <p>Pacote = cada opção que o cliente pode escolher dentro do mesmo passeio. Passeio simples tem 1 pacote (ex.: "Padrão"). Coco Bongo tem vários (Pista Regular, Pista Premium, Gold Member, Front Row).</p>
            <ol class="steps">
                <li>Clique em <strong>+ Adicionar Pacote</strong>.</li>
                <li>Em <strong>Nome</strong>, escreva como o cliente vai ver (ex.: "Front Row").</li>
                <li>Em <strong>Descrição</strong> (opcional), explique o que inclui.</li>
                <li>Em <strong>Categorias de Viajante</strong>, marque quem pode ir nesse pacote: Adulto, Criança, Infantil. <strong>Só aparece para o cliente o que estiver marcado.</strong> Quer que o cliente possa colocar bebê? Marque Infantil.</li>
                <li>Clique em <strong>Salvar</strong> no fim da página.</li>
                <li>Vá em <strong>Preços</strong> e coloque o valor de cada faixa desse pacote.</li>
            </ol>
            <div class="help-dont"><strong>Cuidado:</strong>
                <ul>
                    <li><strong>Desmarcar</strong> uma faixa num pacote apaga o preço dela nesse pacote. Se marcar de novo, ela volta com preço 0: confira em Preços.</li>
                    <li><strong>× Remover Pacote</strong> apaga o pacote e todos os preços dele.</li>
                    <li>Não crie pacotes duplicados só para ter preço de fim de semana. Use <a href="#precos-dia">preço por dia da semana</a>.</li>
                </ul>
            </div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="precos">
            <h3>6. Preços normais e Cortesia (bebê grátis)</h3>
            <p><strong>Onde:</strong> Passeios → botão <strong>Preços</strong> (na lista ou no topo da tela de edição). A tela mostra um bloco para cada pacote.</p>
            <h4>Preço normal (vale para todos os dias)</h4>
            <ol class="steps">
                <li>No bloco do pacote, tabela <strong>Preço normal</strong>: uma linha por faixa etária.</li>
                <li>Coluna <strong>Preço (USD)</strong>: digite o valor em dólar (ex.: <code>85</code> ou <code>85.50</code>).</li>
                <li>Coluna <strong>Promo (opcional)</strong>: se preencher, o site mostra o preço cheio riscado e o promocional. Deixe vazio se não tem promoção. O promo tem que ser <strong>menor</strong> que o preço.</li>
                <li>A coluna <strong>No site</strong> mostra como vai aparecer ("$85.00" ou "Cortesia").</li>
                <li>Clique em <strong>Salvar preços</strong> (fim da página).</li>
            </ol>
            <h4>Bebê / criança sem cobrança = Cortesia</h4>
            <ul>
                <li>Coloque <strong>0</strong> no Preço. O site mostra <strong>"Cortesia"</strong>, o cliente consegue colocar a quantidade de bebês na reserva e <strong>não cobra nada</strong>. A reserva registra o bebê (aparece no voucher e na reserva).</li>
                <li><strong>Não use mais 0,01.</strong> Era um contorno de um defeito que já foi corrigido.</li>
                <li>Se o bebê não aparece para o cliente escolher, a faixa "Infantil" não está marcada no pacote (ver <a href="#pacotes">Pacotes</a>).</li>
            </ul>
            <div class="help-dont"><strong>Não faça:</strong> deixar o preço em branco. Todo campo de preço normal precisa ter um número (0 se for grátis).</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="precos-dia">
            <h3>7. Preço diferente por dia da semana ou data</h3>
            <p><strong>Onde:</strong> tela <strong>Preços</strong>, em cada pacote, parte <strong>"Preços diferentes por dia / data"</strong>.</p>
            <p>Use quando o mesmo pacote custa diferente em certos dias. Nos dias sem regra, vale o preço normal.</p>
            <ol class="steps">
                <li>Clique em <strong>+ Adicionar preço diferente</strong> (dentro do pacote certo).</li>
                <li><strong>Faixa etária</strong>: escolha Adulto, Criança… <strong>Cada faixa é uma regra separada.</strong> Se adulto e criança mudam no sábado, crie 2 regras.</li>
                <li><strong>Quando</strong>: escolha o tipo:
                    <ul>
                        <li><em>Dia(s) da semana</em> → clique nos dias (ficam verdes). Pode marcar vários.</li>
                        <li><em>Uma data específica</em> → escolha a data no calendário (ex.: 31/12/2026). Só vale nesse dia daquele ano.</li>
                        <li><em>Data fixa todo ano (feriado)</em> → dia + mês (ex.: 25 de Dezembro). Vale todo ano.</li>
                        <li><em>Dia do mês (todo mês)</em> → ex.: dia 15 de todos os meses.</li>
                        <li><em>Mês inteiro (todo ano)</em> → ex.: Janeiro inteiro (alta temporada).</li>
                    </ul>
                </li>
                <li><strong>Preço (USD)</strong> obrigatório (pode ser 0). <strong>Promo</strong> opcional.</li>
                <li>Campo de nome (embaixo) é só para vocês se organizarem (ex.: "Fim de semana").</li>
                <li><strong>Salvar preços</strong>.</li>
            </ol>
            <h4>Se duas regras valem no mesmo dia, qual ganha?</h4>
            <p>A mais específica: <strong>Data específica</strong> → Feriado → Dia da semana → Dia do mês → Mês → Preço normal.<br>Ex.: sábado 31/12 com regra "Sábado = $90" e "31/12/2026 = $150" → vale $150.</p>
            <h4>Como conferir</h4>
            <p>No site, abra o passeio → <strong>Verificar Disponibilidade</strong> → escolha o pacote e as pessoas → Continuar → clique numa sexta: o <strong>Total</strong> do resumo (à direita) muda para o preço da sexta. O carrinho cobra esse valor.</p>
            <div class="help-dont"><strong>Para remover uma regra:</strong> botão vermelho <strong>Remover</strong> na regra → <strong>Salvar preços</strong>. Só some depois de salvar.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="dias">
            <h3>8. Dias fechados e datas especiais</h3>
            <p><strong>Onde:</strong> Passeios → botão <strong>Dias e Horários</strong>. Vale só para aquele passeio (cada passeio tem o seu).</p>
            <p><strong>O que acontece no site:</strong> dias fechados aparecem <strong>riscados e cinza</strong> no calendário e não dá para clicar. Mesmo se alguém tentar burlar, o carrinho e o pagamento recusam. Se o cliente já tinha colocado no carrinho e vocês fecharam a data depois, o pagamento é bloqueado com aviso para escolher outra data.</p>
            <h4>Bloco 1 — Dias da semana em que o passeio NÃO funciona</h4>
            <ol class="steps">
                <li>Clique no dia (ex.: <em>Segunda</em>). Ele fica <strong>vermelho = fechado</strong>.</li>
                <li>Clique de novo para desmarcar (volta branco = aberto).</li>
                <li>Nada marcado = funciona todos os dias.</li>
                <li>Salvar dias e horários.</li>
            </ol>
            <h4>Bloco 2 — Datas especiais</h4>
            <ol class="steps">
                <li><strong>+ Adicionar data especial</strong>.</li>
                <li><strong>O que fazer</strong>:
                    <ul>
                        <li><em>Fechar (não vender)</em>: esgotado, feriado, fornecedor fechado, clima.</li>
                        <li><em>Abrir excepcionalmente</em>: abre num dia da semana que normalmente é fechado (bloco 1).</li>
                    </ul>
                </li>
                <li><strong>De</strong>: primeiro dia. <strong>Até (opcional)</strong>: último dia, se for um período. Vazio = só um dia.</li>
                <li><strong>Observação</strong>: só para vocês (ex.: "Fornecedor avisou dia 28/10"). O cliente não vê.</li>
                <li>Salvar dias e horários.</li>
            </ol>
            <ul>
                <li>Se a mesma data tiver "Fechar" e "Abrir", <strong>Fechar ganha</strong>.</li>
                <li>Datas que já passaram somem sozinhas da lista.</li>
                <li>Para desfazer: <strong>Remover</strong> na linha → Salvar.</li>
            </ul>
            <div class="help-dont"><strong>Importante:</strong> fechar uma data fecha o <strong>passeio inteiro</strong> nessa data (todos os pacotes). Para fechar só um pacote, veja <a href="#limites">o que o sistema ainda não faz</a>.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="horarios">
            <h3>9. Horários de saída do passeio</h3>
            <p><strong>Onde:</strong> Dias e Horários → <strong>Bloco 3 — Horários de saída</strong>.</p>
            <table class="table help-table">
                <thead><tr><th>Quantos horários cadastrados</th><th>O que o cliente vê</th><th>Use para</th></tr></thead>
                <tbody>
                    <tr><td><strong>Nenhum</strong></td><td>Escolhe só a data. Não aparece escolha de horário.</td><td>Passeios em que o horário é combinado depois.</td></tr>
                    <tr><td><strong>1</strong></td><td>Aparece "Horário de saída: 07:00" já selecionado. Não precisa clicar.</td><td>Saona, Santo Domingo, Scape Park, El Dorado, Buggy noturno, Coco Bongo, Chic Cabaret.</td></tr>
                    <tr><td><strong>2 ou mais</strong></td><td>Botões para escolher um. Obrigatório escolher para continuar.</td><td>Golfinhos, Buggys, Party Boat, Pesca.</td></tr>
                </tbody>
            </table>
            <ol class="steps">
                <li><strong>+ Adicionar horário</strong>.</li>
                <li><strong>Horário</strong>: hora de saída (ex.: 08:30).</li>
                <li><strong>Nome (opcional)</strong>: aparece ao lado (ex.: "Manhã", "1ª saída").</li>
                <li><strong>Dias em que este horário existe</strong>: vem tudo marcado (verde). Clique para desmarcar os dias em que esse horário <strong>não</strong> acontece.</li>
                <li>Repita para cada horário. Salvar dias e horários.</li>
            </ol>
            <ul>
                <li>Num dia em que nenhum horário está marcado, o site avisa o cliente "Não há horários para este dia" e não deixa continuar.</li>
                <li>Um horário com nenhum dia marcado é descartado ao salvar.</li>
                <li>Os horários aparecem para o cliente em ordem de hora (mais cedo primeiro).</li>
            </ul>
            <div class="help-tip"><strong>Diferença importante:</strong> "horário de saída" = horário do passeio. "Horário de busca" = a hora que o transporte passa no hotel, que varia por hotel: fica em <a href="#hoteis">Horários por Hotel</a>.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="hoteis">
            <h3>10. Horários de busca por hotel</h3>
            <p><strong>Onde:</strong> Passeios → <strong>Editar</strong> → role até o card <strong>Horários por Hotel</strong> (perto do fim). Aparece só em passeio já salvo.</p>
            <p><strong>Para que serve:</strong> quando o cliente digita o nome do hotel na reserva, o site sugere os hotéis daqui e mostra o horário de busca daquele hotel. <strong>Passeio sem hotéis cadastrados não mostra sugestões</strong>: era por isso que funcionava em uns passeios e em outros não.</p>
            <h4>Cadastrar um hotel</h4>
            <ol class="steps">
                <li><strong>+ Adicionar Hotel</strong>.</li>
                <li><strong>Nome do Hotel</strong>: escreva como o cliente procuraria (ex.: "Barceló Bávaro Palace"). O cliente acha digitando qualquer parte ("barcelo").</li>
                <li><strong>Horários de Busca</strong>: <strong>+ Adicionar Horário</strong> para cada horário que o transporte passa nesse hotel. × apaga.</li>
                <li>"Hotel Ativo" marcado = aparece. Desmarque para esconder sem apagar.</li>
                <li>Salvar.</li>
            </ol>
            <h4>Cadastrar muitos hotéis de uma vez (planilha)</h4>
            <ol class="steps">
                <li>No card, clique em <strong>Importar Planilha</strong>.</li>
                <li>Clique em <strong>Baixar Modelo</strong> para ver o formato: colunas <em>Hotel | Horário 1 | Horário 2 | Horário 3</em>. Uma linha por hotel.</li>
                <li>Preencha no Excel, salve como .xlsx (ou .csv) e envie em <strong>Arquivo</strong> → <strong>Importar</strong>. Máximo 5 MB.</li>
                <li>Se marcar a opção de limpar existentes, apaga os hotéis atuais antes de importar.</li>
            </ol>
            <h4>Passeio com várias saídas</h4>
            <p>Se o passeio tem 3 saídas em Dias e Horários, cadastre <strong>3 horários de busca</strong> em cada hotel, um para cada saída. O 1º horário de busca (o mais cedo) corresponde à 1ª saída, o 2º à 2ª, e assim por diante. O site mostra ao cliente só a busca da saída que ele escolheu.</p>
            <div class="help-ex"><strong>Exemplo Golfinhos:</strong> saídas 09:00, 11:00 e 13:00. Hotel Barceló: buscas 07:40, 09:40, 11:40. Cliente escolhe 11:00 → vê "Busca no hotel: 09:40".</div>
            <h4>Hotel fora da lista</h4>
            <p>O cliente clica em <strong>"Meu hotel não está na lista (digitar nome ou endereço)"</strong> e escreve o hotel ou o endereço (Airbnb, casa). A reserva chega com esse texto e <strong>sem horário de busca</strong>: vocês combinam o horário pelo WhatsApp. Num passeio sem nenhum hotel cadastrado, esse campo já aparece aberto.</p>
            <div class="help-dont"><strong>Não faça:</strong> "Limpar Tudo" apaga todos os hotéis e horários do passeio, sem volta.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="exemplos">
            <h3>11. Exemplos completos</h3>
            <h4>Coco Bongo</h4>
            <ol class="steps">
                <li><strong>Pacotes</strong> (Editar): Pista Regular, Pista Premium, Gold Member, Front Row. Em cada um marque Adulto (e as outras faixas se aceitarem).</li>
                <li><strong>Preços</strong>: em cada pacote, "Preço normal" = valor de terça a quinta e domingo. "+ Adicionar preço diferente" → Adulto → Dia(s) da semana → Sex + Sáb → valor mais caro. Repita em cada pacote.</li>
                <li><strong>Dias e Horários</strong>: bloco 1 → marque Segunda. Bloco 2 → "Abrir excepcionalmente" De 28/12/2026. Bloco 3 → 1 horário (ex.: 21:00) com todos os dias.</li>
                <li><strong>Horários por Hotel</strong>: cada hotel com o horário de busca da noite.</li>
                <li>Esgotou num dia (todos os pacotes): bloco 2 → "Fechar" naquela data.</li>
            </ol>
            <h4>Golfinhos na piscina (fechado sábado, sem 1º horário por enquanto)</h4>
            <ol class="steps">
                <li>Dias e Horários → bloco 1 → marque Sábado.</li>
                <li>Bloco 3 → 3 horários (1ª, 2ª, 3ª saída). No 1º horário, desmarque todos os dias. O cliente só vê os dois últimos.</li>
                <li>Na alta temporada: marque de novo os dias do 1º horário e desmarque Sábado no bloco 1. Salve.</li>
            </ol>
            <h4>Golfinhos no mar (todo dia, 3 horários)</h4>
            <p>Bloco 1 vazio. Bloco 3 com 3 horários, todos os dias marcados. Cada hotel com 3 horários de busca.</p>
            <h4>Saona / Santo Domingo / Scape Park / El Dorado</h4>
            <p>Bloco 3 com <strong>1 horário</strong>. O cliente não escolhe horário; vê só a busca do hotel.</p>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="faixas">
            <h3>12. Faixas etárias (idades)</h3>
            <p><strong>Onde:</strong> menu <strong>Faixas Etárias</strong>. Vale para <strong>todos</strong> os passeios ao mesmo tempo.</p>
            <ol class="steps">
                <li>Coluna <strong>Nome que aparece no site</strong>: pode trocar (ex.: "Infantil" → "Bebê").</li>
                <li><strong>Idade de / Idade até</strong>: ex.: 0 e 3 → "0-3 anos". Só "de" (ex.: 12) → "+12 anos".</li>
                <li>A coluna <strong>Como fica no site</strong> mostra o resultado na hora.</li>
                <li><strong>Usada em</strong> mostra em quantos pacotes a faixa está.</li>
                <li><strong>Salvar</strong>.</li>
            </ol>
            <div class="help-dont"><strong>Não faça:</strong> trocar o significado de uma faixa (ex.: transformar "Criança" em "Sênior"). Os preços já cadastrados continuariam valendo para ela. Mude só nome e idades.</div>
            <p>Faixas do <strong>transfer</strong> são separadas: Transfers → <strong>Passageiros</strong>.</p>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="politicas">
            <h3>13. Políticas, câmbio e textos do site</h3>
            <p><strong>Onde:</strong> menu <strong>Políticas e Câmbio</strong>. Um botão só no final salva tudo: <strong>Salvar políticas e câmbio</strong>.</p>
            <h4>Câmbio</h4>
            <ol class="steps">
                <li>Campo <strong>US$ 1 = R$</strong>: digite o valor (ex.: <code>6,00</code>).</li>
                <li>Salvar. Vale para os <strong>próximos</strong> Pix gerados. Pix já gerado antes não muda.</li>
            </ol>
            <h4>Transfer de saída</h4>
            <p>"Horas antes do voo (padrão)": quantas horas antes do voo o motorista busca no hotel. Padrão 4. Pode usar meia hora: 4,5.</p>
            <h4>Textos</h4>
            <ul>
                <li><strong>Política de pagamento</strong>: texto "Como reservar e pagar" de todas as páginas de passeio. Escreva <code>{sinal}</code> onde quer o % de sinal e <code>{restante}</code> onde quer o % que falta. O site troca pelo % de cada passeio.</li>
                <li><strong>Política de cancelamento (resumo)</strong>: quadro "Política de cancelamento" das páginas de passeio.</li>
                <li><strong>Páginas</strong> "Políticas de Cancelamento", "Termos e Condições", "Política de Privacidade": a caixa já vem com o texto atual. Edite o que quiser. O link "Ver página no site ↗" abre a página.</li>
                <li>Depois de editar uma página, aparece a opção <strong>"Voltar para o texto original"</strong>: marque e salve para desfazer.</li>
            </ul>
            <h4>Como escrever (sem saber programação)</h4>
            <table class="table help-table">
                <thead><tr><th>Você escreve</th><th>Fica no site</th></tr></thead>
                <tbody>
                    <tr><td><code># Reembolso</code></td><td>Título grande "Reembolso"</td></tr>
                    <tr><td><code>## Para reagendar</code></td><td>Subtítulo</td></tr>
                    <tr><td><code>- Até 48h antes: reembolso total</code></td><td>• item de lista</td></tr>
                    <tr><td><code>**importante**</code></td><td><strong>importante</strong> (negrito)</td></tr>
                    <tr><td>linha em branco</td><td>separa parágrafos</td></tr>
                </tbody>
            </table>
            <div class="help-dont"><strong>Atenção:</strong> as porcentagens que o sistema usa para calcular o reembolso ficam em <a href="#cancelamentos">Cancelamentos → Regras de Reembolso</a>. Mudar o texto <strong>não</strong> muda o cálculo: se mudar a política, mude nos dois lugares.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="transfers">
            <h3>14. Transfers: locais, veículos, rotas e preços</h3>
            <p><strong>Onde:</strong> menu <strong>Transfers</strong> (abre a lista de veículos). No topo há os botões <strong>Gerenciar Locais</strong>, <strong>Passageiros</strong> e <strong>Reservas</strong>.</p>
            <h4>Ordem para montar do zero</h4>
            <ol class="steps">
                <li><strong>Locais</strong> (Gerenciar Locais): cadastre aeroportos, hotéis, zonas, shoppings, restaurantes.
                    <ul>
                        <li><strong>Título</strong>: nome que o cliente digita para achar.</li>
                        <li><strong>Tipo</strong>: escolha certo. <strong>"Aeroporto" é essencial</strong>: é ele que faz o site pedir horário do voo e calcular a busca.</li>
                        <li><strong>Busca antes do voo (h)</strong>: só para quem vai ao aeroporto. Vazio = padrão (4h). Coloque mais (ex.: 5) para hotéis longe (fora de Bávaro/Punta Cana) ou para outro aeroporto (no aeroporto, vale para todos que vão até ele). Se origem e destino tiverem tempos diferentes, vale o maior.</li>
                        <li>"Salvar Local". Para alterar: botão <strong>Editar</strong> na linha → muda o formulário de cima → <strong>Atualizar</strong>.</li>
                    </ul>
                </li>
                <li><strong>Passageiros</strong>: faixas do transfer (Adulto, Criança, Bebê) com idade e quantidade inicial.</li>
                <li><strong>Veículos</strong> → <strong>+ Novo Veículo</strong>:
                    <ul>
                        <li>Título, tipo (Van, Ônibus, SUV, Sedan), capacidade (máx. passageiros, adultos, crianças, bagagem), se é adaptado.</li>
                        <li><strong>Rotas e Tarifas</strong> → <strong>Adicionar Rota</strong>: Origem, Destino, Preço Base, Duração. Dentro da rota, <strong>+ Nova Tarifa</strong>: tipo (Privado/Compartilhado), mínimo e máximo de passageiros, preço. Ex.: Privado 1–4 pax $40; 5–8 pax $60.</li>
                        <li>Status Ativo, imagem → Salvar.</li>
                    </ul>
                </li>
            </ol>
            <ul>
                <li>O site só mostra veículos que têm <strong>rota cadastrada</strong> entre os dois locais escolhidos e <strong>lugar disponível</strong> naquele dia (a capacidade do dia é somada com as reservas).</li>
                <li>A rota vale nos dois sentidos (ida e volta).</li>
                <li><strong>Reservas</strong> (botão no topo): transfers vendidos com cliente, rota, data, passageiros, valor e status.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="transfer-horarios">
            <h3>15. Transfers: como o cliente informa os horários</h3>
            <p>O site muda os campos sozinho conforme a origem e o destino. Vocês não precisam configurar nada além do <strong>Tipo</strong> correto dos locais.</p>
            <table class="table help-table">
                <thead><tr><th>Trajeto</th><th>O cliente preenche</th><th>O que vocês recebem</th></tr></thead>
                <tbody>
                    <tr><td><strong>Aeroporto → hotel</strong></td><td>Data de chegada + "Chegada do voo". No <em>Privado</em>, também "Busca desejada" (opcional).</td><td>Horário do voo e horário de busca (se não informou, igual ao voo).</td></tr>
                    <tr><td><strong>Hotel → aeroporto</strong></td><td>Data do voo + "Horário do voo".</td><td>Horário do voo e <strong>busca calculada</strong> (padrão 4h antes). O cliente vê: "Voo às 15:00 → buscaremos você às 11:00" e o link "Fale com a gente no WhatsApp" para mudar.</td></tr>
                    <tr><td><strong>Hotel → shopping/restaurante (Ida e Volta)</strong></td><td>"Hora da ida" e "Hora da volta".</td><td>Os dois horários.</td></tr>
                    <tr><td><strong>Hotel → hotel (Somente Ida)</strong></td><td>"Hora da busca".</td><td>Horário de busca.</td></tr>
                    <tr><td><strong>Múltiplos Transfers</strong></td><td>Para cada rota: data e "Hora da busca".</td><td>Um transfer por rota.</td></tr>
                </tbody>
            </table>
            <ul>
                <li>Voo de madrugada (ex.: 02:00) → a busca fica no dia anterior às 22:00, automaticamente.</li>
                <li>No carrinho e no WhatsApp do pedido aparece "busca às HH:MM | Voo: HH:MM".</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="reservas">
            <h3>16. Reservas: acompanhar, mudar status, pagamentos</h3>
            <p><strong>Onde:</strong> menu <strong>Reservas</strong>. Filtro por status + <strong>Filtrar</strong>. Botão <strong>Ver</strong> em cada linha abre a reserva.</p>
            <h4>Dentro da reserva</h4>
            <ul>
                <li><strong>Dados do Cliente</strong>: nome, e-mail, telefone.</li>
                <li><strong>Passeios Reservados</strong>: passeio, pacote, data, horário, <strong>hotel</strong> e <strong>horário de busca</strong>, pessoas por faixa.</li>
                <li><strong>Transfers</strong>: rota, data, busca e voo.</li>
                <li><strong>Pagamentos</strong>, <strong>Vouchers</strong> (Ver/Baixar PDF), <strong>Passagem Aérea</strong> (se o cliente enviou), <strong>Observações</strong>, <strong>Resumo Financeiro</strong>, <strong>Histórico</strong>.</li>
            </ul>
            <h4>Status (o que cada um quer dizer)</h4>
            <table class="table help-table">
                <tbody>
                    <tr><td><strong>Pendente</strong></td><td>Pedido feito, pagamento não confirmado.</td></tr>
                    <tr><td><strong>Confirmado</strong></td><td>Reserva garantida.</td></tr>
                    <tr><td><strong>Parc. Pago</strong></td><td>Pagou o sinal; falta o restante.</td></tr>
                    <tr><td><strong>Concluído</strong></td><td>Passeio realizado e tudo pago.</td></tr>
                    <tr><td><strong>Cancelado</strong></td><td>Não vai acontecer.</td></tr>
                    <tr><td><strong>Reembolsado</strong></td><td>Cancelado e dinheiro devolvido.</td></tr>
                </tbody>
            </table>
            <p>Para mudar: card <strong>Alterar Status</strong> → escolha → <strong>Atualizar</strong>.</p>
            <h4>Sinal (caução) e restante</h4>
            <ul>
                <li><strong>Registrar pagamento do restante</strong>: quando o cliente paga o resto no destino. Preencha valor recebido (USD), forma de pagamento, observação → <strong>Registrar pagamento</strong>.</li>
                <li><strong>Devolver a caução / sinal</strong>: quando o cliente pagou tudo presencialmente e vocês devolvem o sinal. Escolha a forma da devolução → <strong>Confirmar devolução</strong>.</li>
                <li><strong>Agência Parceira</strong>: escolha a agência que vendeu → <strong>Salvar agência</strong> (gera a comissão dela).</li>
            </ul>
            <div class="help-dont"><strong>Não faça:</strong> marcar "Reembolsado" sem ter devolvido o dinheiro. Use o fluxo de <a href="#cancelamentos">Cancelamentos</a> para pedidos de clientes.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="reserva-manual">
            <h3>17. Reserva manual (venda fora do site)</h3>
            <p><strong>Onde:</strong> Reservas → <strong>+ Nova Reserva Manual</strong>.</p>
            <ol class="steps">
                <li><strong>Dados do Cliente</strong>: Nome, Sobrenome, E-mail (obrigatórios), Telefone, País.</li>
                <li><strong>Detalhes da Reserva</strong>: passeio/serviço, data, pessoas, <strong>Valor Total (USD)</strong>.</li>
                <li><strong>Observações Internas</strong>: só vocês veem.</li>
                <li>Confira o <strong>Resumo</strong> → <strong>Criar Reserva</strong>.</li>
            </ol>
            <p>A reserva manual <strong>não passa</strong> pelos bloqueios de Dias e Horários: vocês podem vender num dia fechado se tiverem combinado com o fornecedor.</p>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="vouchers">
            <h3>18. Vouchers</h3>
            <p><strong>Onde:</strong> menu <strong>Vouchers</strong> (ou dentro de cada reserva).</p>
            <ul>
                <li><strong>Ver</strong>: abre o voucher na tela.</li>
                <li><strong>Download</strong>: baixa o PDF (para mandar no WhatsApp).</li>
                <li><strong>Enviar</strong>: manda por e-mail ao cliente. A coluna "Enviado" mostra se já foi.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="cancelamentos">
            <h3>19. Cancelamentos e regras de reembolso</h3>
            <p><strong>Onde:</strong> menu <strong>Cancelamentos</strong>. Aqui chegam os pedidos que o cliente faz em "Minha conta → Cancelamentos".</p>
            <h4>Atender um pedido</h4>
            <ol class="steps">
                <li>Filtre "Pendentes" → <strong>Filtrar</strong> → <strong>Gerenciar</strong> no pedido.</li>
                <li>Leia o motivo e os dados da reserva.</li>
                <li>Escreva a <strong>Resposta ao cliente</strong>.</li>
                <li><strong>Aprovar Cancelamento</strong> ou <strong>Rejeitar</strong>.</li>
                <li>Se aprovou e vai devolver dinheiro: <strong>Processar Reembolso</strong> → valor (USD) (o sistema sugere pelas regras) → <strong>Confirmar Reembolso</strong>.</li>
            </ol>
            <h4>Regras de reembolso</h4>
            <p>Cancelamentos → <strong>Regras de Reembolso</strong>. Cada faixa: antecedência mínima (dias e horas), reembolso (% do valor pago ou valor fixo), ordem, ativa. Ex.: "48 horas → 100%", "24 horas → 50%", "0 → 0%". <strong>Salvar faixa</strong>. Editar/Excluir na lista.</p>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="categorias">
            <h3>20. Categorias de passeios</h3>
            <p>Menu <strong>Categorias</strong> → <strong>+ Nova Categoria</strong> ou Editar: Nome, Descrição, Ordem de Exibição (menor aparece primeiro), Imagem. Salvar. Para colocar um passeio na categoria: Passeios → Editar → card Categorias.</p>
            <div class="help-dont">Excluir uma categoria tira os passeios dela, mas não apaga os passeios.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="cupons">
            <h3>21. Cupons de desconto</h3>
            <p>Menu <strong>Cupons</strong> → <strong>+ Novo Cupom</strong>.</p>
            <ol class="steps">
                <li><strong>Código do Cupom</strong>: o que o cliente digita (ex.: VERAO10). Sem espaços.</li>
                <li><strong>Descrição (interna)</strong>: só para vocês.</li>
                <li><strong>Tipo de Desconto</strong>: Percentual (%) ou Valor fixo (US$). <strong>Valor</strong>: ex.: 10.</li>
                <li><strong>Vincular a um Afiliado</strong>: se o cupom é de um influenciador (a venda vira comissão dele). Senão, "Cupom geral".</li>
                <li><strong>Regras e Validade</strong>: pedido mínimo, limite de usos, início e fim da validade.</li>
                <li><strong>Cupom ativo</strong> marcado → Salvar.</li>
            </ol>
            <p>O cliente aplica no carrinho. Para parar um cupom: Editar → desmarque "Cupom ativo".</p>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="equipe">
            <h3>22. Equipe (página Sobre Nós)</h3>
            <p>Menu <strong>Equipe</strong> → <strong>+ Novo Membro</strong> ou Editar: Nome, Cargo, Apresentação, Status (Publicado/Rascunho), Ordem, Foto (de preferência quadrada). Salvar.</p>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="agendamentos">
            <h3>23. Agendamentos de videochamada</h3>
            <p>Menu <strong>Agendamentos</strong>: clientes que marcaram uma conversa pelo site. Abas: Pendentes, Confirmadas, Concluídas, Canceladas.</p>
            <ul>
                <li><strong>Abrir</strong>: entra no link da reunião. <strong>Confirmar</strong>: confirma o horário. <strong>Concluir</strong>: depois que aconteceu. <strong>Cancelar</strong>: pede o motivo. <strong>Excluir</strong>: apaga.</li>
                <li>Dias e horários disponíveis para agendar: Configurações → Agendamento (superadmin).</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="afiliados">
            <h3>24. Afiliados</h3>
            <p>Menu <strong>Afiliados</strong>. Abas: <strong>Solicitações de Afiliação</strong>, <strong>Afiliados Ativos</strong>, <strong>Bloqueados</strong>, <strong>Comissões</strong>, <strong>Criativos</strong>.</p>
            <ul>
                <li>Solicitação: <strong>Ver Detalhes</strong> → aprovar ou recusar.</li>
                <li>Ativos: veja comissão (%), vendas e ganhos. <strong>Bloquear</strong> pede um motivo; <strong>Reativar</strong> desfaz.</li>
                <li>Comissões: marque como paga quando pagar; ou cancele.</li>
                <li>Criativos: suba imagens para os afiliados divulgarem.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="agencias">
            <h3>25. Agências parceiras</h3>
            <p>Menu <strong>Agências</strong>. Abas: Solicitações, Agências Ativas, Bloqueadas, Comissões. Botão <strong>+ Nova Agência</strong>.</p>
            <ul>
                <li>Solicitação: <strong>Aprovar</strong> ou <strong>Recusar</strong>.</li>
                <li>Cadastro: Razão Social, Nome Fantasia, CNPJ, contato, dados bancários/PIX, <strong>Taxa de Comissão (%)</strong>, <strong>Código de Indicação</strong> (vai no link da agência), Status.</li>
                <li>Comissões: pagar ou cancelar.</li>
                <li>Na reserva, "Agência Parceira" liga a venda à agência.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="newsletter">
            <h3>26. Newsletter</h3>
            <p>Menu <strong>Newsletter</strong>: e-mails cadastrados no site. <strong>Exportar CSV</strong> baixa a lista (abre no Excel). <strong>Criar Campanha</strong> envia um e-mail para os inscritos. <strong>Remover</strong> tira um e-mail.</p>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="usuarios">
            <h3>27. Usuários e permissões</h3>
            <p>Menu <strong>Usuários</strong> → <strong>+ Novo Usuário</strong>: nome, e-mail (é o login), telefone, <strong>Perfil / Role</strong>, Status e <strong>Senha</strong> (mínimo 6 caracteres). Salvar. Para trocar a senha de alguém: Editar → "Nova Senha" (em branco mantém a atual).</p>
            <table class="table help-table">
                <thead><tr><th>Perfil</th><th>Pode</th></tr></thead>
                <tbody>
                    <tr><td><strong>Superadmin</strong></td><td>Tudo, inclusive Configurações do sistema.</td></tr>
                    <tr><td><strong>Admin</strong></td><td>Passeios, preços, dias e horários, faixas, políticas e câmbio, transfers, cupons, reservas, usuários, afiliados, agências, relatórios. Não entra em Configurações.</td></tr>
                    <tr><td><strong>Atendente / Agente WhatsApp / Comercial</strong></td><td>Reservas, cancelamentos, agendamentos, suporte, WhatsApp, CRM, chat. <strong>Não</strong> mexe em preços nem passeios.</td></tr>
                    <tr><td><strong>Afiliado / Cliente</strong></td><td>Não entram no painel admin.</td></tr>
                </tbody>
            </table>
            <ul>
                <li>Status <strong>Inativo</strong> ou <strong>Banido</strong> bloqueia o login sem apagar.</li>
                <li>Quem sai da empresa: mude para Inativo (não exclua, para manter o histórico).</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="whatsapp">
            <h3>28. WhatsApp, CRM e Chat Interno</h3>
            <ul>
                <li><strong>WhatsApp</strong>: conversas com clientes dentro do painel.</li>
                <li><strong>CRM</strong>: quadro de atendimento em colunas (etapas). Mova cada cliente para a etapa em que ele está.</li>
                <li><strong>Chat Interno</strong>: conversa entre a equipe.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="relatorios">
            <h3>29. Dashboard e Relatórios</h3>
            <ul>
                <li><strong>Dashboard</strong> (primeiro item do menu): reservas dos últimos 30 dias e reservas recentes. "Ver Todas" leva à lista de reservas.</li>
                <li><strong>Relatórios</strong>: escolha "De" e "Até" → <strong>Filtrar</strong>. Mostra volume de vendas, origem dos clientes, vendas por país e cidade, passeios mais vendidos, campanhas de tráfego pago e vendas por método de pagamento.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="suporte">
            <h3>30. Suporte (pedir ajuda técnica)</h3>
            <p>Menu <strong>Suporte</strong> → nova demanda: <strong>Título</strong> (resumo), <strong>Prioridade</strong>, <strong>Categoria</strong>, <strong>Descrição</strong> (o que fez, o que esperava, o que aconteceu, link da página). Envie. A demanda chega direto à equipe técnica e o status atualiza na lista. "Reenviar" se não chegou.</p>
            <div class="help-tip">Quanto mais detalhe (passeio, data, print), mais rápido resolvemos.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="config">
            <h3>31. Configurações (só superadmin)</h3>
            <p>Dados do site, logo, e-mail, formas de pagamento (PayPal, Stripe, PagBank/Pix), checkout, WhatsApp, videochamada, assistente Aurora, integrações.</p>
            <div class="help-dont"><strong>Não mexa</strong> em chaves/tokens (PayPal, Stripe, PagBank, OpenAI, Google) sem falar com a equipe técnica: um caractere errado para os pagamentos. Câmbio, políticas e textos ficam em <a href="#politicas">Políticas e Câmbio</a>, que qualquer Admin acessa.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="cliente">
            <h3>32. O que o cliente vê ao reservar</h3>
            <ol class="steps">
                <li>Abre o passeio e clica em <strong>Verificar Disponibilidade</strong>.</li>
                <li><strong>Tipo de Pacote</strong>: escolhe o pacote e quantas pessoas de cada faixa (Cortesia aparece como "Cortesia (sem custo)").</li>
                <li><strong>Data e Hora</strong>: calendário com dias fechados riscados. Escolhe a data; se houver mais de um horário, escolhe o horário. O total à direita já mostra o preço do dia.</li>
                <li><strong>Hotel (opcional)</strong>: digita o hotel e escolhe da lista (vê o horário de busca), ou clica em "Meu hotel não está na lista" e digita.</li>
                <li><strong>Adicionar ao Carrinho</strong> ou <strong>Ir para Checkout</strong> → paga (sinal ou total) ou finaliza pelo WhatsApp.</li>
            </ol>
            <div class="help-tip">Faça uma reserva de teste você mesmo (sem pagar) sempre que mudar algo importante. É o melhor jeito de conferir.</div>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="nao-fazer">
            <h3>33. O que NUNCA fazer</h3>
            <ul class="help-never">
                <li>Excluir passeio que tem reservas. Use Desativado.</li>
                <li>Excluir usuário de quem saiu. Use Inativo.</li>
                <li>Usar 0,01 no preço do bebê. Use 0.</li>
                <li>Duplicar passeio só para ter preço de fim de semana. Use preço por dia da semana.</li>
                <li>Achar que o campo "Disponibilidade" do passeio bloqueia datas. Ele é só texto. Bloqueio é em Dias e Horários.</li>
                <li>Achar que "Saída do hotel De/Até" define os horários de escolha. É só texto. Horários são em Dias e Horários.</li>
                <li>Mudar texto de cancelamento sem mudar as Regras de Reembolso (e vice-versa).</li>
                <li>"Limpar Tudo" em Horários por Hotel sem ter a planilha para reimportar.</li>
                <li>Mexer em chaves de pagamento em Configurações.</li>
                <li>Sair da tela sem clicar em Salvar.</li>
                <li>Compartilhar senha. Crie um usuário para cada pessoa.</li>
            </ul>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="problemas">
            <h3>34. Problemas comuns e soluções</h3>
            <table class="table help-table">
                <thead><tr><th style="width:35%">O que acontece</th><th>Por quê / o que fazer</th></tr></thead>
                <tbody>
                    <tr><td>Mudei e o site não mostra</td><td>1) Viu a faixa verde ao salvar? 2) Aperte F5 no site. 3) No celular, feche e abra a página.</td></tr>
                    <tr><td>Bebê/criança não aparece para escolher</td><td>Faixa não marcada no pacote: Editar → Pacotes → marque → Salvar → confira em Preços.</td></tr>
                    <tr><td>Bebê aparece com preço em vez de Cortesia</td><td>Preços → preço normal do Infantil = 0. Confira também se não há regra por dia com valor.</td></tr>
                    <tr><td>Hotel não aparece na busca</td><td>Hotel não cadastrado nesse passeio, ou "Hotel Ativo" desmarcado, ou hotel sem nenhum horário (hotel sem horário não aparece).</td></tr>
                    <tr><td>Aparecem 3 horários num passeio integral</td><td>Dias e Horários → bloco 3: deixe só 1 horário.</td></tr>
                    <tr><td>"Não há horários para este dia"</td><td>Nenhum horário tem esse dia da semana marcado. Marque o dia no horário certo.</td></tr>
                    <tr><td>Dia deveria estar fechado e está aberto</td><td>Veja se existe "Abrir excepcionalmente" nessa data no bloco 2.</td></tr>
                    <tr><td>Dia deveria estar aberto e está fechado</td><td>Veja o bloco 1 (dia da semana marcado) e o bloco 2 ("Fechar" nessa data).</td></tr>
                    <tr><td>Preço do sábado não mudou</td><td>A regra é por faixa: crie uma para Adulto e outra para Criança. Confira se o Sáb está verde e se salvou.</td></tr>
                    <tr><td>Busca do hotel errada para a saída escolhida</td><td>O hotel precisa ter a mesma quantidade de horários de busca que o passeio tem de saídas, em ordem.</td></tr>
                    <tr><td>Transfer não aparece para o cliente</td><td>Falta rota entre os dois locais no veículo, ou o veículo está lotado/inativo, ou não há tarifa para aquela quantidade de pessoas/tipo de serviço.</td></tr>
                    <tr><td>Transfer não pede horário do voo</td><td>O local do aeroporto não está com Tipo "Aeroporto" em Gerenciar Locais.</td></tr>
                    <tr><td>Pix com valor em reais errado</td><td>Câmbio desatualizado em Políticas e Câmbio.</td></tr>
                    <tr><td>Não vejo algum menu</td><td>Seu perfil não é Admin. Peça para um Admin/Superadmin mudar em Usuários.</td></tr>
                    <tr><td>Erro estranho / tela branca</td><td>Abra uma demanda em Suporte com print e o endereço da página.</td></tr>
                </tbody>
            </table>
        </section>

        <!-- ============================================================ -->
        <section class="admin-card help-sec" id="limites">
            <h3>35. O que o sistema ainda NÃO faz</h3>
            <ul>
                <li><strong>Fechar só um pacote ou só um horário numa data</strong> (ex.: só o Front Row esgotado no dia 31/10, mantendo os outros). Hoje: fechar a data fecha o passeio inteiro. Enquanto isso, para Coco Bongo, avise no WhatsApp ou peça pela área de Suporte.</li>
                <li><strong>Controle de vagas por dia</strong> nos passeios (quantidade máxima por data): não há contagem automática; use "Fechar" quando o fornecedor avisar.</li>
            </ul>
        </section>
    </div>
</div>

<style>
.help-layout { display:grid; grid-template-columns: 270px 1fr; gap:20px; align-items:start; }
.help-toc { position:sticky; top:16px; font-size:13px; max-height:calc(100vh - 40px); overflow:auto; }
.help-toc ol { margin:8px 0 0; padding-left:20px; }
.help-toc li { margin-bottom:5px; }
.help-toc a { color:#1B6F00; text-decoration:none; }
.help-toc a:hover { text-decoration:underline; }
.help-sec { scroll-margin-top:16px; font-size:14px; line-height:1.65; color:#1e293b; }
.help-sec h3 { margin:0 0 10px; font-size:18px; color:#14532d; }
.help-sec h4 { margin:16px 0 6px; font-size:14.5px; color:#1e293b; }
.help-sec ul, .help-sec ol { padding-left:22px; margin:6px 0; }
.help-sec li { margin-bottom:6px; }
.help-sec ol.steps > li { padding-left:2px; }
.help-sec code { background:#f1f5f9; padding:1px 6px; border-radius:4px; font-size:13px; }
.help-tip { background:#f0fdf4; border-left:4px solid #1B6F00; padding:10px 14px; border-radius:6px; margin:12px 0 0; }
.help-dont { background:#fef2f2; border-left:4px solid #dc2626; padding:10px 14px; border-radius:6px; margin:12px 0 0; color:#7f1d1d; }
.help-dont ul { margin-top:6px; }
.help-ex { background:#eff6ff; border-left:4px solid #2563eb; padding:10px 14px; border-radius:6px; margin:10px 0; color:#1e3a8a; }
.help-never li { color:#7f1d1d; }
.help-table td, .help-table th { vertical-align:top; font-size:13px; }
@media (max-width: 900px) { .help-layout { grid-template-columns: 1fr; } .help-toc { position:static; max-height:none; } }
</style>
