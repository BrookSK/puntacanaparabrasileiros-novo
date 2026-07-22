<?php
declare(strict_types=1);

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\App;

/**
 * Middleware de acesso administrativo.
 * Garante que o usuário tem role de admin, superadmin ou editor.
 */
class AdminMiddleware extends Middleware
{
    private const ALLOWED_ROLES = ['superadmin', 'admin', 'editor'];

    public function handle(Request $request, Response $response): bool
    {
        $session = App::getInstance()->getSession();
        $user = $session->get('user');

        if (!$user || !in_array($user['role'] ?? '', self::ALLOWED_ROLES, true)) {
            if ($request->expectsJson()) {
                $response->json(['error' => 'Acesso negado.'], 403);
                return false;
            }

            $session->flash('error', 'Você não tem permissão para acessar esta área.');
            $response->redirect('/');
            return false;
        }

        return true;
    }
}
