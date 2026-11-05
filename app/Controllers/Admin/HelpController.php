<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;

/**
 * Tela "Como Usar": tutorial simples de todas as áreas do painel.
 */
class HelpController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        $this->view('admin/help/index', [
            'pageTitle' => 'Como Usar o Painel',
        ], 'admin');
    }
}
