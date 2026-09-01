<?php
declare(strict_types=1);

/**
 * Configurações de banco de dados.
 *
 * NOTA: Em produção, estas credenciais devem ser protegidas
 * e este arquivo não deve ser versionado.
 *
 * COMO FUNCIONA:
 * Você pode cadastrar VÁRIAS conexões em 'connections'. O sistema tenta
 * conectar na primeira; se falhar (servidor fora do ar, credencial errada,
 * host inacessível, etc.), ele automaticamente tenta a próxima da lista,
 * e assim por diante — sem precisar apagar as informações antigas.
 *
 * Ao trocar de servidor: coloque a conexão do servidor NOVO no topo da lista
 * e mantenha a antiga logo abaixo como reserva (ou vice-versa).
 */
return [
    // A lista é testada de cima para baixo. A primeira que conectar é usada.
    'connections' => [

        // Conexão atual / principal
        [
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'db_12_puntacana_novo_db',
            'username' => 'puntacana_novo_db',
            'password' => 'TB*yaCqwkrh5p81$',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],

        // Conexão de reserva / novo servidor.
        // Preencha com as credenciais do outro servidor.
        // Se a de cima falhar, esta será testada automaticamente.
        [
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'puntacana_novo_db',
            'username' => 'puntacana_novo_db',
            'password' => 'TB*yaCqwkrh5p81$',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],

    ],
];
