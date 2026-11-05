<?php
/**
 * Tutorial "Como Usar" — linguagem simples, passo a passo.
 * Cada seção: o que é, onde fica e como fazer as tarefas do dia a dia.
 */
$sections = [
    ['id' => 'comeco', 'title' => 'Antes de tudo: como o painel funciona'],
    ['id' => 'urgente', 'title' => 'Tarefas do dia a dia (atalhos rápidos)'],
    ['id' => 'passeios', 'title' => 'Passeios: cadastrar e editar'],
    ['id' => 'precos', 'title' => 'Preços (normal, Cortesia e por dia da semana)'],
    ['id' => 'dias', 'title' => 'Dias e Horários (dias fechados, datas especiais, horários)'],
    ['id' => 'hoteis', 'title' => 'Horários por Hotel (busca no hotel)'],
    ['id' => 'faixas', 'title' => 'Faixas Etárias (bebê, criança, adulto)'],
    ['id' => 'politicas', 'title' => 'Políticas e Câmbio'],
    ['id' => 'transfers', 'title' => 'Transfers (veículos, locais, preços, horários)'],
    ['id' => 'reservas', 'title' => 'Reservas, pagamentos e vouchers'],
    ['id' => 'cancelamentos', 'title' => 'Cancelamentos e regras de reembolso'],
    ['id' => 'categorias', 'title' => 'Categorias de passeios'],
    ['id' => 'cupons', 'title' => 'Cupons de desconto'],
    ['id' => 'equipe', 'title' => 'Equipe (página Sobre Nós)'],
    ['id' => 'agendamentos', 'title' => 'Agendamentos de videochamada'],
    ['id' => 'afiliados', 'title' => 'Afiliados e Agências parceiras'],
    ['id' => 'newsletter', 'title' => 'Newsletter'],
    ['id' => 'usuarios', 'title' => 'Usuários e permissões'],
    ['id' => 'whatsapp', 'title' => 'WhatsApp, CRM e Chat Interno'],
    ['id' => 'relatorios', 'title' => 'Dashboard e Relatórios'],
    ['id' => 'suporte', 'title' => 'Suporte (pedir ajuda à equipe técnica)'],
    ['id' => 'config', 'title' => 'Configurações do sistema (superadmin)'],
    ['id' => 'duvidas', 'title' => 'Problemas comuns e o que fazer'],
];
?>

<div class="card-header">
    <div>
        <h2>Como Usar o Painel</h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Guia simples, passo a passo, de tudo o que dá para fazer aqui. Use o índice para pular direto ao assunto.</p>
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

        <section class="admin-card help-sec" id="comeco">
            <h3>1. Antes de tudo: como o painel funciona</h3>
            <ul>
                <li>O menu da esquerda leva a cada área. Cada pessoa vê só as áreas que o cargo dela permite (veja <a href="#usuarios">Usuários</a>).</li>
                <li>Quase toda tela tem um botão verde <strong>Salvar</strong> no final. <strong>Nada muda no site até você clicar em Salvar.</strong></li>
                <li>Depois de salvar, aparece uma mensagem verde no topo. Se aparecer vermelha, leia o que diz: normalmente falta preencher algum campo.</li>
                <li>Tudo o que vocês salvam vale <strong>na hora</strong> no site. Não precisa chamar a equipe técnica, pode ser de madrugada.</li>
                <li>Para conferir como o cliente vê, use <strong>Ver Site</strong> no fim do menu (abre em outra aba). Se não mudou, aperte F5 para recarregar.</li>
                <li>Os preços do site são em <strong>dólar (USD)</strong>. O real só aparece no Pix, usando a taxa de câmbio de <a href="#politicas">Políticas e Câmbio</a>.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="urgente">
            <h3>2. Tarefas do dia a dia (atalhos rápidos)</h3>
            <table class="table help-table">
                <thead><tr><th>Quero…</th><th>Onde fazer</th></tr></thead>
                <tbody>
                    <tr><td>Mudar o câmbio (ex.: US$ 1 = R$ 6,00)</td><td>Menu <strong>Políticas e Câmbio</strong> → campo "US$ 1 = R$" → Salvar.</td></tr>
                    <tr><td>Fechar um passeio numa data (fornecedor esgotou ou fechou)</td><td><strong>Passeios</strong> → botão <strong>Dias e Horários</strong> do passeio → "Datas especiais" → <em>Fechar</em> → escolha a data → Salvar.</td></tr>
                    <tr><td>Passeio não abre às segundas</td><td><strong>Dias e Horários</strong> → item 1 → marque "Segunda" → Salvar.</td></tr>
                    <tr><td>Abrir numa segunda excepcional (ex.: 28/12)</td><td><strong>Dias e Horários</strong> → "Datas especiais" → <em>Abrir excepcionalmente</em> → 28/12 → Salvar.</td></tr>
                    <tr><td>Tirar um horário de saída (ex.: 1º horário dos Golfinhos)</td><td><strong>Dias e Horários</strong> → item 3 → desmarque os dias daquele horário (ou Remover) → Salvar.</td></tr>
                    <tr><td>Mudar um preço</td><td><strong>Passeios</strong> → botão <strong>Preços</strong> → "Preço normal" → Salvar.</td></tr>
                    <tr><td>Preço mais caro sexta e sábado</td><td><strong>Preços</strong> → "+ Adicionar preço diferente" → Quando: <em>Dia(s) da semana</em> → marque Sex e Sáb → preço → Salvar.</td></tr>
                    <tr><td>Bebê grátis mas contando na reserva</td><td><strong>Preços</strong> → preço normal do Infantil = <strong>0</strong> → Salvar. Aparece "Cortesia" e o cliente consegue incluir o bebê.</td></tr>
                    <tr><td>Mudar idade de bebê/criança</td><td>Menu <strong>Faixas Etárias</strong> → mude as idades → Salvar.</td></tr>
                    <tr><td>Mudar texto de pagamento/cancelamento</td><td>Menu <strong>Políticas e Câmbio</strong> → edite o texto → Salvar.</td></tr>
                    <tr><td>Esconder um passeio do site</td><td><strong>Passeios</strong> → Editar → "Publicação" → Status <em>Rascunho</em> ou <em>Desativado</em> → Salvar.</td></tr>
                </tbody>
            </table>
        </section>

        <section class="admin-card help-sec" id="passeios">
            <h3>3. Passeios: cadastrar e editar</h3>
            <p><strong>Onde:</strong> menu <strong>Passeios</strong>. Cada passeio da lista tem os botões <em>Editar</em>, <em>Preços</em>, <em>Dias e Horários</em>, <em>Duplicar</em> e <em>Excluir</em>.</p>
            <h4>Criar um passeio novo</h4>
            <ol>
                <li>Clique em <strong>Novo Passeio</strong> (ou use <strong>Duplicar</strong> num passeio parecido: a cópia nasce como rascunho, com preços, hotéis e fotos).</li>
                <li>Preencha <strong>Título</strong>, duração, descrição curta (aparece nos cards) e completa.</li>
                <li><strong>Saída do hotel / Retorno ao hotel (De/Até)</strong>: é só o texto informativo da página ("Saída entre 07:00 e 08:00"). Os horários que o cliente escolhe ficam em <a href="#dias">Dias e Horários</a>.</li>
                <li>Preencha os cards que quiser: Detalhes, Vídeo do YouTube, FAQ, Categorias, Documentos, Inclui/Não inclui, O que levar, Roteiro, Galeria de fotos.</li>
                <li>No card <strong>Pacotes</strong>, crie as opções do passeio (ex.: Coco Bongo "Pista Regular", "Front Row") e marque as <strong>faixas etárias</strong> de cada pacote (Adulto, Criança, Infantil). Faixa não marcada não aparece para o cliente.</li>
                <li>Em <strong>Publicação</strong>, escolha <em>Publicado</em> para aparecer no site. <em>Rascunho</em> = escondido.</li>
                <li>Clique em <strong>Salvar</strong>. Depois vá em <strong>Preços</strong> e <strong>Dias e Horários</strong>.</li>
            </ol>
            <h4>Outros cards do passeio</h4>
            <ul>
                <li><strong>Sinal / pagamento parcial</strong>: % que o cliente paga na reserva (ex.: 50%). Aparece no texto "Como reservar e pagar".</li>
                <li><strong>Tabela de Preços por Grupo</strong>: preço fechado pelo número de adultos (ex.: buggy para 2 pessoas). Criança e infantil continuam com o preço por pessoa.</li>
                <li><strong>Pacotes por Composição</strong>: preço por combinação de pessoas/unidades (ex.: 3 pessoas em 2 buggies).</li>
                <li><strong>Acompanhantes</strong>: pessoa que vai junto sem fazer a atividade, com preço próprio.</li>
                <li><strong>Avaliações do Google</strong>: Place ID ou nota/quantidade manual.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="precos">
            <h3>4. Preços (normal, Cortesia e por dia da semana)</h3>
            <p><strong>Onde:</strong> <strong>Passeios</strong> → botão <strong>Preços</strong> do passeio. Cada pacote tem sua parte.</p>
            <h4>Preço normal</h4>
            <ol>
                <li>Na tabela "Preço normal", coloque o valor em dólar de cada faixa etária.</li>
                <li><strong>0 = Cortesia</strong>: o site mostra "Cortesia", não cobra nada e o cliente consegue colocar a pessoa na reserva. Não precisa mais usar 0,01.</li>
                <li>"Promo" é opcional: o site mostra o preço cheio riscado e o promocional.</li>
            </ol>
            <h4>Preço diferente por dia ou data</h4>
            <ol>
                <li>Clique em <strong>+ Adicionar preço diferente</strong>.</li>
                <li>Escolha a <strong>faixa etária</strong> e <strong>Quando</strong>:
                    <ul>
                        <li><em>Dia(s) da semana</em>: marque os dias (ex.: Sex e Sáb).</li>
                        <li><em>Uma data específica</em>: ex.: 31/12/2026.</li>
                        <li><em>Data fixa todo ano</em>: ex.: 25 de dezembro.</li>
                        <li><em>Dia do mês</em> ou <em>Mês inteiro</em>: para temporadas.</li>
                    </ul>
                </li>
                <li>Coloque o preço (pode ser 0) e Salvar. Repita para cada faixa (adulto e criança são regras separadas).</li>
                <li>Nos dias sem regra, vale o preço normal. Quando o cliente escolhe a data, o resumo da reserva já mostra o preço daquele dia, e o carrinho cobra esse valor.</li>
            </ol>
            <p class="help-tip">Exemplo Coco Bongo: preço normal = valor de ter/qua/qui/dom. Regra "Dia(s) da semana" com Sex + Sáb = valor mais alto. Não precisa duplicar o passeio.</p>
        </section>

        <section class="admin-card help-sec" id="dias">
            <h3>5. Dias e Horários (dias fechados, datas especiais, horários)</h3>
            <p><strong>Onde:</strong> <strong>Passeios</strong> → botão <strong>Dias e Horários</strong>. Vale só para aquele passeio. O site bloqueia no calendário e o sistema também recusa no carrinho e no checkout, então o cliente não consegue comprar em dia fechado.</p>
            <h4>1. Dias da semana em que NÃO funciona</h4>
            <p>Marque os dias fechados (ficam vermelhos). Ex.: Coco Bongo → Segunda. Golfinhos na piscina → Sábado.</p>
            <h4>2. Datas especiais</h4>
            <ul>
                <li><strong>Fechar (não vender)</strong>: para esgotado, feriado ou fornecedor fechado. Pode ser um dia ou um período (De/Até).</li>
                <li><strong>Abrir excepcionalmente</strong>: abre num dia que normalmente é fechado. Ex.: segunda 28/12/2026 da Coco Bongo.</li>
                <li>"Fechar" sempre ganha de "Abrir". Datas passadas somem sozinhas.</li>
            </ul>
            <h4>3. Horários de saída</h4>
            <ul>
                <li><strong>Sem horário cadastrado</strong>: o cliente escolhe só a data.</li>
                <li><strong>1 horário</strong> (Saona, Santo Domingo, Scape Park, El Dorado, noturnos): o site escolhe sozinho.</li>
                <li><strong>2 ou 3 horários</strong> (Golfinhos, Buggy, Party Boat, Pesca): o cliente escolhe um.</li>
                <li>Cada horário tem os dias em que existe. Ex.: Golfinhos na piscina sem o 1º horário → desmarque todos os dias do 1º horário. Quando voltar na alta temporada, é só marcar de novo.</li>
            </ul>
            <p class="help-tip">Esgotou uma opção específica (ex.: só o Front Row num dia)? Esta tela fecha o passeio inteiro na data. Para fechar só um pacote, fale com a equipe técnica (ver resumo das pendências).</p>
        </section>

        <section class="admin-card help-sec" id="hoteis">
            <h3>6. Horários por Hotel (busca no hotel)</h3>
            <p><strong>Onde:</strong> <strong>Passeios</strong> → Editar → card <strong>Horários por Hotel</strong> (no fim da página).</p>
            <ol>
                <li><strong>+ Adicionar Hotel</strong>: digite o nome do hotel e os horários em que o transporte busca o cliente.</li>
                <li>Ou <strong>Importar planilha</strong> (.xlsx ou .csv) com hotel e horários para cadastrar vários de uma vez.</li>
                <li>O cliente digita o nome do hotel na reserva e o site sugere os hotéis cadastrados. <strong>Passeio sem hotéis cadastrados não tem sugestões</strong>: era por isso que funcionava em uns passeios e em outros não.</li>
                <li>Se o hotel não estiver na lista, o cliente clica em <strong>"Meu hotel não está na lista"</strong> e digita o nome ou o endereço (Airbnb, casa). Vocês confirmam o horário de busca pelo WhatsApp.</li>
                <li>Passeio com várias saídas: cadastre os horários de busca de cada hotel na ordem das saídas (1ª busca = 1ª saída, 2ª busca = 2ª saída…). O site mostra só a busca da saída que o cliente escolheu.</li>
            </ol>
        </section>

        <section class="admin-card help-sec" id="faixas">
            <h3>7. Faixas Etárias (bebê, criança, adulto)</h3>
            <p><strong>Onde:</strong> menu <strong>Faixas Etárias</strong>. Vale para todos os passeios.</p>
            <ol>
                <li>Mude o nome (ex.: "Infantil" para "Bebê") e as idades <em>de</em> e <em>até</em>.</li>
                <li>A coluna "Como fica no site" mostra o resultado, ex.: "Criança (4-11 anos)".</li>
                <li>Clique em <strong>Salvar</strong>.</li>
            </ol>
        </section>

        <section class="admin-card help-sec" id="politicas">
            <h3>8. Políticas e Câmbio</h3>
            <p><strong>Onde:</strong> menu <strong>Políticas e Câmbio</strong>.</p>
            <ul>
                <li><strong>Câmbio</strong>: digite quanto vale US$ 1 em reais (ex.: 6,00). Vale para os próximos Pix.</li>
                <li><strong>Transfer de saída</strong>: quantas horas antes do voo buscar o cliente (padrão 4).</li>
                <li><strong>Política de pagamento</strong>: texto "Como reservar e pagar" de todos os passeios. <code>{sinal}</code> vira o % de sinal do passeio.</li>
                <li><strong>Política de cancelamento (resumo)</strong>: quadro que aparece nos passeios.</li>
                <li><strong>Páginas</strong> Cancelamento, Termos e Privacidade: o texto atual já vem preenchido. Edite e salve. Para desfazer, marque "Voltar para o texto original".</li>
                <li>Para escrever: <code># </code> no começo da linha = título, <code>- </code> = item de lista, <code>**palavra**</code> = negrito.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="transfers">
            <h3>9. Transfers (veículos, locais, preços, horários)</h3>
            <p><strong>Onde:</strong> menu <strong>Transfers</strong>.</p>
            <ul>
                <li><strong>Locais</strong>: aeroportos, hotéis, zonas, shoppings, restaurantes. Marque o <strong>Tipo</strong> certo (Aeroporto é importante: muda os campos de horário do site). Em "Busca antes do voo (h)", coloque mais horas para locais longe ou outro aeroporto (vazio = padrão de 4h).</li>
                <li><strong>Veículos</strong>: cada veículo tem capacidade e rotas (origem → destino) com preço. O site mostra só veículos com rota cadastrada e lugar disponível.</li>
                <li><strong>Passageiros</strong>: faixas do transfer (adulto, criança, bebê) e o texto de idade.</li>
                <li><strong>Reservas</strong>: lista dos transfers vendidos, com horário de busca e horário do voo.</li>
            </ul>
            <h4>Como o cliente informa os horários</h4>
            <ul>
                <li><strong>Aeroporto → hotel</strong>: informa a chegada do voo. No privado, pode indicar a busca desejada.</li>
                <li><strong>Hotel → aeroporto</strong>: informa o horário do voo. O site calcula a busca (4h antes, ou o tempo do local) e avisa que, para outro horário, é só chamar no WhatsApp.</li>
                <li><strong>Hotel → shopping/restaurante (ida e volta)</strong>: horário da ida e da volta.</li>
                <li><strong>Hotel → hotel</strong> (Somente ida): horário da busca.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="reservas">
            <h3>10. Reservas, pagamentos e vouchers</h3>
            <ul>
                <li><strong>Reservas</strong>: todas as compras do site. Clique numa reserva para ver cliente, passeios, hotel, horário de busca, passageiros e pagamentos.</li>
                <li><strong>Status</strong>: Pendente → Confirmado / Parc. Pago → Concluído. Também há Cancelado e Reembolsado. Mude o status e salve.</li>
                <li><strong>Registrar pagamento do restante</strong>: quando o cliente paga o resto no destino.</li>
                <li><strong>Devolver sinal</strong>: registra a devolução do sinal.</li>
                <li><strong>Agência</strong>: vincula a reserva a uma agência parceira (para comissão).</li>
                <li><strong>Criar reserva</strong> (Reservas → Nova): para vendas feitas fora do site.</li>
                <li><strong>Vouchers</strong>: ver, baixar em PDF e reenviar o voucher por e-mail ao cliente.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="cancelamentos">
            <h3>11. Cancelamentos e regras de reembolso</h3>
            <ul>
                <li><strong>Cancelamentos</strong>: pedidos feitos pelos clientes. Abra o pedido e escolha <em>Aprovar</em>, <em>Rejeitar</em> ou <em>Reembolsar</em>.</li>
                <li><strong>Cancelamentos → Regras</strong>: % de reembolso por antecedência (ex.: mais de 48h = 100%). O sistema usa essas regras para calcular o valor. Se mudar a regra, mude também o texto em <a href="#politicas">Políticas</a>.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="categorias">
            <h3>12. Categorias de passeios</h3>
            <p>Menu <strong>Categorias</strong>: grupos do site (Praias, Aventura, Família…). Crie, edite a imagem e a ordem. No passeio, marque as categorias no card "Categorias".</p>
        </section>

        <section class="admin-card help-sec" id="cupons">
            <h3>13. Cupons de desconto</h3>
            <p>Menu <strong>Cupons</strong> → Novo cupom: código (ex.: VERAO10), tipo (percentual ou valor fixo em US$), validade e limites. O cliente digita o código no carrinho. Um cupom pode ser ligado a um afiliado.</p>
        </section>

        <section class="admin-card help-sec" id="equipe">
            <h3>14. Equipe (página Sobre Nós)</h3>
            <p>Menu <strong>Equipe</strong>: pessoas do carrossel "Nossa equipe". Adicione foto, nome e cargo.</p>
        </section>

        <section class="admin-card help-sec" id="agendamentos">
            <h3>15. Agendamentos de videochamada</h3>
            <p>Menu <strong>Agendamentos</strong>: clientes que marcaram videochamada pelo site. Mude o status (confirmado, realizado, cancelado). Os dias e horários disponíveis ficam em Configurações (superadmin).</p>
        </section>

        <section class="admin-card help-sec" id="afiliados">
            <h3>16. Afiliados e Agências parceiras</h3>
            <ul>
                <li><strong>Afiliados</strong>: influenciadores que divulgam com link próprio. Aprove ou recuse pedidos, veja e pague comissões, suba criativos (imagens para divulgação).</li>
                <li><strong>Agências</strong>: agências que vendem os passeios. Aprove pedidos, cadastre agências, veja e pague comissões.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="newsletter">
            <h3>17. Newsletter</h3>
            <p>Menu <strong>Newsletter</strong>: e-mails cadastrados no site. Dá para exportar a lista e enviar campanhas (Newsletter → Campanhas).</p>
        </section>

        <section class="admin-card help-sec" id="usuarios">
            <h3>18. Usuários e permissões</h3>
            <ul>
                <li>Menu <strong>Usuários</strong>: crie logins para a equipe.</li>
                <li><strong>Superadmin</strong>: tudo, inclusive Configurações. <strong>Admin</strong>: passeios, preços, políticas, câmbio, transfers, reservas. <strong>Atendente / Agente WhatsApp / Comercial</strong>: reservas, cancelamentos, WhatsApp e CRM, sem mexer em preços.</li>
                <li>"Entrar como" (impersonate) permite ver o painel como aquele usuário.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="whatsapp">
            <h3>19. WhatsApp, CRM e Chat Interno</h3>
            <ul>
                <li><strong>WhatsApp</strong>: conversas com clientes dentro do painel.</li>
                <li><strong>CRM</strong>: quadro de negociações em colunas (etapas do atendimento). Mova cada cliente para a etapa em que ele está.</li>
                <li><strong>Chat Interno</strong>: conversa entre a equipe.</li>
            </ul>
        </section>

        <section class="admin-card help-sec" id="relatorios">
            <h3>20. Dashboard e Relatórios</h3>
            <p><strong>Dashboard</strong>: resumo de vendas e reservas recentes. <strong>Relatórios</strong>: números de vendas e reservas por período.</p>
        </section>

        <section class="admin-card help-sec" id="suporte">
            <h3>21. Suporte (pedir ajuda à equipe técnica)</h3>
            <p>Menu <strong>Suporte</strong> → Nova demanda: descreva o problema (e mande print). A demanda vai direto para a equipe técnica e vocês acompanham o status ali.</p>
        </section>

        <section class="admin-card help-sec" id="config">
            <h3>22. Configurações do sistema (só superadmin)</h3>
            <p>Dados do site, logo, e-mail, formas de pagamento (PayPal, Stripe, PagBank/Pix), WhatsApp, videochamada, assistente Aurora e integrações. Mexa só se souber o que está mudando. O câmbio também pode ser mudado em <a href="#politicas">Políticas e Câmbio</a>, que qualquer admin acessa.</p>
        </section>

        <section class="admin-card help-sec" id="duvidas">
            <h3>23. Problemas comuns e o que fazer</h3>
            <table class="table help-table">
                <thead><tr><th>O que acontece</th><th>Por quê / o que fazer</th></tr></thead>
                <tbody>
                    <tr><td>Bebê/criança não aparece para escolher</td><td>A faixa não está marcada no card <strong>Pacotes</strong> do passeio. Marque, salve e confira o preço em <strong>Preços</strong>.</td></tr>
                    <tr><td>Hotel não aparece na busca</td><td>O passeio não tem hotéis em <strong>Horários por Hotel</strong>. Cadastre ou importe a planilha. O cliente ainda pode digitar o hotel à mão.</td></tr>
                    <tr><td>Aparecem horários que não existem</td><td>Confira <strong>Dias e Horários</strong> → item 3. Sem horário cadastrado, o cliente não vê horário nenhum.</td></tr>
                    <tr><td>Dia deveria estar fechado e está aberto</td><td>Veja se não existe uma "Data especial: Abrir excepcionalmente" nessa data.</td></tr>
                    <tr><td>Preço do sábado não mudou</td><td>A regra precisa ser para a faixa certa (adulto e criança são regras separadas) e o dia precisa estar marcado.</td></tr>
                    <tr><td>Mudei e o site não mostra</td><td>Confira se clicou em Salvar (mensagem verde) e aperte F5 no site.</td></tr>
                </tbody>
            </table>
        </section>
    </div>
</div>

<style>
.help-layout { display:grid; grid-template-columns: 260px 1fr; gap:20px; align-items:start; }
.help-toc { position:sticky; top:16px; font-size:13px; }
.help-toc ol { margin:8px 0 0; padding-left:18px; }
.help-toc li { margin-bottom:6px; }
.help-toc a { color:#1B6F00; text-decoration:none; }
.help-toc a:hover { text-decoration:underline; }
.help-sec { scroll-margin-top:16px; font-size:14px; line-height:1.6; color:#1e293b; }
.help-sec h3 { margin:0 0 10px; font-size:17px; }
.help-sec h4 { margin:14px 0 6px; font-size:14px; color:#14532d; }
.help-sec ul, .help-sec ol { padding-left:20px; margin:6px 0; }
.help-sec li { margin-bottom:5px; }
.help-tip { background:#f0fdf4; border-left:4px solid #1B6F00; padding:10px 14px; border-radius:6px; margin:10px 0 0; }
.help-table td, .help-table th { vertical-align:top; font-size:13px; }
@media (max-width: 900px) { .help-layout { grid-template-columns: 1fr; } .help-toc { position:static; } }
</style>
