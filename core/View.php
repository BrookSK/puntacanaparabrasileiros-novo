<?php
declare(strict_types=1);

namespace Core;

/**
 * Engine de renderização de views.
 * Suporta layouts, seções e componentes parciais.
 */
class View
{
    private static string $viewsPath = '';
    private static array $sharedData = [];

    /**
     * Define o caminho base das views.
     */
    public static function setViewsPath(string $path): void
    {
        self::$viewsPath = rtrim($path, '/\\');
    }

    /**
     * Compartilha dados com todas as views.
     */
    public static function share(string $key, mixed $value): void
    {
        self::$sharedData[$key] = $value;
    }

    /**
     * Renderiza uma view com layout opcional.
     */
    public static function render(string $view, array $data = [], ?string $layout = null): string
    {
        $viewFile = self::resolveViewPath($view);

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View não encontrada: {$view} ({$viewFile})", 500);
        }

        // Mescla dados compartilhados com dados da view
        $allData = array_merge(self::$sharedData, $data);

        // Renderiza a view
        $content = self::renderFile($viewFile, $allData);

        // Se tem layout, envolve o conteúdo
        if ($layout !== null) {
            $layoutFile = self::resolveViewPath('layouts/' . $layout);
            if (file_exists($layoutFile)) {
                $allData['content'] = $content;
                $content = self::renderFile($layoutFile, $allData);
            }
        }

        return $content;
    }

    /**
     * Renderiza uma view para respostas estáticas (erros, etc.).
     */
    public static function renderStatic(string $view, array $data = []): ?string
    {
        $viewFile = self::resolveViewPath($view);
        if (!file_exists($viewFile)) {
            return null;
        }
        return self::renderFile($viewFile, array_merge(self::$sharedData, $data));
    }

    /**
     * Renderiza um componente/partial.
     */
    public static function partial(string $view, array $data = []): string
    {
        $viewFile = self::resolveViewPath('components/' . $view);
        if (!file_exists($viewFile)) {
            $viewFile = self::resolveViewPath($view);
        }
        if (!file_exists($viewFile)) {
            return '';
        }
        return self::renderFile($viewFile, array_merge(self::$sharedData, $data));
    }

    /**
     * Renderiza arquivo PHP isolado com extract de dados.
     */
    private static function renderFile(string $filePath, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $filePath;
        return ob_get_clean();
    }

    /**
     * Resolve o caminho completo de uma view.
     */
    private static function resolveViewPath(string $view): string
    {
        $view = str_replace('.', '/', $view);
        return self::$viewsPath . '/' . $view . '.php';
    }
}
