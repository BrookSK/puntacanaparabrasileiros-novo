<?php
declare(strict_types=1);

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\App;

/**
 * Middleware de proteção CSRF.
 * Valida o token CSRF em todas as requisições POST/PUT/DELETE.
 */
class CsrfMiddleware extends Middleware
{
    public function handle(Request $request, Response $response): bool
    {
        $session = App::getInstance()->getSession();

        // Obter token do formulário ou header
        $token = $request->input('_token')
            ?? $request->header('X-CSRF-TOKEN')
            ?? '';

        if (!$session->validateCsrfToken($token)) {
            if ($request->expectsJson()) {
                $response->json(['error' => 'Token CSRF inválido.'], 419);
                return false;
            }

            $session->flash('error', 'Sessão expirada. Por favor, tente novamente.');
            $response->redirect($request->header('referer') ?? '/');
            return false;
        }

        return true;
    }
}
