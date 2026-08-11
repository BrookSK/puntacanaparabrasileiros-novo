<?php
declare(strict_types=1);

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\App;

/**
 * Middleware de acesso ao módulo WhatsApp/CRM.
 * Permite acesso para: superadmin, admin, attendant, whatsapp_agent, comercial.
 */
class WhatsAppMiddleware extends Middleware
{
    private const ALLOWED_ROLES = [
        'superadmin',
        'admin',
        'attendant',
        'whatsapp_agent',
        'comercial',
    ];

    public function handle(Request $request, Response $response): bool
    {
        $session = App::getInstance()->getSession();
        $user = $session->get('user');

        if (!$user || !in_array($user['role'] ?? '', self::ALLOWED_ROLES, true)) {
            if ($request->expectsJson()) {
                $response->setStatusCode(403);
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody(json_encode(['error' => 'Acesso negado.']));
                $response->send();
                return false;
            }

            $session->flash('error', 'Você não tem permissão para acessar esta área.');
            $response->redirect('/');
            return false;
        }

        return true;
    }

    /**
     * Verifica se o usuário é superadmin (para ações restritas).
     */
    public static function isSuperAdmin(): bool
    {
        $user = App::getInstance()->getSession()->get('user');
        return ($user['role'] ?? '') === 'superadmin';
    }

    /**
     * Verifica se o usuário tem role de comercial.
     */
    public static function isComercial(): bool
    {
        $user = App::getInstance()->getSession()->get('user');
        return ($user['role'] ?? '') === 'comercial';
    }

    /**
     * Verifica se o usuário pode acessar o módulo WhatsApp/CRM.
     */
    public static function canAccessWhatsApp(): bool
    {
        $user = App::getInstance()->getSession()->get('user');
        return in_array($user['role'] ?? '', self::ALLOWED_ROLES, true);
    }
}
