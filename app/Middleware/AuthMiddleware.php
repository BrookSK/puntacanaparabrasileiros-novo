<?php
declare(strict_types=1);

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\App;

/**
 * Middleware de autenticação.
 * Garante que o usuário está logado antes de acessar rotas protegidas.
 */
class AuthMiddleware extends Middleware
{
    public function handle(Request $request, Response $response): bool
    {
        $session = App::getInstance()->getSession();

        if (!$session->has('user')) {
            if ($request->expectsJson()) {
                $response->json(['error' => 'Não autenticado.'], 401);
                return false;
            }

            $session->flash('error', 'Você precisa estar logado para acessar esta página.');
            $session->set('intended_url', $request->uri());
            $response->redirect('/login');
            return false;
        }

        // Verificar se o usuário ainda está ativo
        $user = $session->get('user');
        if (($user['status'] ?? '') !== 'active') {
            $session->destroy();
            $response->redirect('/login');
            return false;
        }

        return true;
    }
}
