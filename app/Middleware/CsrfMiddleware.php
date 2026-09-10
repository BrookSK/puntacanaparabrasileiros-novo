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

        // Detecta POST "estourado": quando o corpo enviado ultrapassa o
        // post_max_size do PHP, o PHP DESCARTA $_POST e $_FILES por completo.
        // Nesse caso o _token some e cairíamos em "Sessão expirada" sem motivo
        // real. Damos uma mensagem clara para o admin entender que a imagem
        // (ou o total de imagens) passou do limite permitido pelo servidor.
        $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        $isPost = strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
        $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
        // Só é um POST de formulário se o Content-Type for de form. Corpos JSON
        // (APIs) não populam $_POST e NÃO devem ser confundidos com estouro.
        $isFormPost = str_contains($contentType, 'multipart/form-data')
            || str_contains($contentType, 'application/x-www-form-urlencoded');
        if ($isPost && $isFormPost && $contentLength > 0 && empty($_POST) && empty($_FILES)) {
            $limit = $this->bytesFromIni((string) ini_get('post_max_size'));
            $limitLabel = $limit > 0 ? round($limit / 1048576, 1) . ' MB' : ini_get('post_max_size');

            if ($request->expectsJson()) {
                $response->json([
                    'error' => 'O envio ultrapassou o limite do servidor (' . $limitLabel . '). Envie imagens menores.',
                ], 413);
                return false;
            }

            $session->flash('error', 'As imagens enviadas ultrapassaram o limite do servidor (máx. ' . $limitLabel . ' no total). Reduza o tamanho/quantidade das imagens e tente novamente.');
            $response->redirect($request->header('referer') ?? '/');
            return false;
        }

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

    /**
     * Converte um valor de php.ini (ex.: "8M", "2G", "512K") em bytes.
     */
    private function bytesFromIni(string $value): int
    {
        $value = trim($value);
        if ($value === '') return 0;
        $unit = strtolower($value[strlen($value) - 1]);
        $num = (int) $value;
        return match ($unit) {
            'g' => $num * 1024 * 1024 * 1024,
            'm' => $num * 1024 * 1024,
            'k' => $num * 1024,
            default => (int) $value,
        };
    }
}
