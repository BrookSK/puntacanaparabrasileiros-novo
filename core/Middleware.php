<?php
declare(strict_types=1);

namespace Core;

/**
 * Interface base para middleware.
 * Cada middleware deve implementar o método handle().
 * Retornar false interrompe o processamento da requisição.
 */
abstract class Middleware
{
    /**
     * Processa a requisição.
     * @return bool true para continuar, false para interromper
     */
    abstract public function handle(Request $request, Response $response): bool;
}
