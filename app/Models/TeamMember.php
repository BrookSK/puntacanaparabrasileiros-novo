<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TeamMember extends Model
{
    protected string $table = 'team_members';
    protected array $fillable = ['name', 'role', 'photo', 'bio', 'sort_order', 'status'];

    /**
     * Todos os membros (admin), ordenados pela ordem de exibição.
     */
    public function getAll(): array
    {
        return $this->all('sort_order ASC, id ASC');
    }

    /**
     * Apenas os membros publicados — usados no carrossel do site.
     */
    public function getPublished(): array
    {
        return $this->where("status = 'published'", [], 'sort_order ASC, id ASC');
    }
}
