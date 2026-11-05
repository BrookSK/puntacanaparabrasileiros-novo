<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;

/**
 * Faixas etárias: edita nome e intervalo de idade das categorias de viajante
 * (Adulto, Criança, Infantil...). O texto da faixa aparece no site, no modal
 * de reserva e no carrinho.
 */
class TravelerCategoriesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
    }

    public function index(Request $request, Response $response): void
    {
        $categories = $this->db->fetchAll("SELECT * FROM traveler_categories ORDER BY sort_order ASC, id ASC");
        foreach ($categories as &$c) {
            [$c['age_min'], $c['age_max']] = self::parseAgeGroup((string) ($c['age_group'] ?? ''));
            $c['in_use'] = (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM trip_package_categories WHERE traveler_category_id = ?",
                [(int) $c['id']]
            );
        }

        $this->view('admin/traveler-categories/index', [
            'categories' => $categories,
            'pageTitle' => 'Faixas Etárias',
        ], 'admin');
    }

    public function save(Request $request, Response $response): void
    {
        $rows = (array) $request->input('categories', []);
        $saved = 0;
        foreach ($rows as $id => $row) {
            $id = (int) $id;
            if ($id <= 0 || !is_array($row)) continue;
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') continue;

            $min = trim((string) ($row['age_min'] ?? ''));
            $max = trim((string) ($row['age_max'] ?? ''));
            $min = ctype_digit($min) ? (int) $min : null;
            $max = ctype_digit($max) ? (int) $max : null;
            if ($min !== null && $max !== null && $max < $min) [$min, $max] = [$max, $min];

            $this->db->update('traveler_categories', [
                'name' => mb_substr($name, 0, 100),
                'age_group' => self::buildAgeGroup($min, $max),
            ], 'id = ?', [$id]);
            $saved++;
        }

        $this->flash('success', $saved . ' faixa(s) etária(s) atualizada(s). O site já mostra as novas idades.');
        $this->redirect('/admin/faixas-etarias');
    }

    /** "0-3 anos" => [0,3] | "+12 anos" / "12+ anos" => [12,null] | "até 3 anos" => [null,3] */
    public static function parseAgeGroup(string $text): array
    {
        if (preg_match('/(\d+)\s*(?:-|a|–|até)\s*(\d+)/u', $text, $m)) return [(int) $m[1], (int) $m[2]];
        if (preg_match('/^\s*at[eé]\s*(\d+)/iu', $text, $m)) return [null, (int) $m[1]];
        if (preg_match('/(\d+)/', $text, $m)) return [(int) $m[1], null];
        return [null, null];
    }

    public static function buildAgeGroup(?int $min, ?int $max): ?string
    {
        if ($min !== null && $max !== null) return "{$min}-{$max} anos";
        if ($min !== null) return "+{$min} anos";
        if ($max !== null) return "até {$max} anos";
        return null;
    }
}
