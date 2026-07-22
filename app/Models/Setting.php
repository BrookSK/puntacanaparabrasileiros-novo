<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';
    protected array $fillable = [
        'setting_key', 'setting_value', 'setting_group', 'setting_type', 'autoload',
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        $row = $this->findWhere('setting_key', $key);
        return $row ? $row['setting_value'] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $existing = $this->findWhere('setting_key', $key);
        if ($existing) {
            $this->db->update($this->table, ['setting_value' => $value], 'id = ?', [(int) $existing['id']]);
        } else {
            $this->db->insert($this->table, [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => 'general',
                'setting_type' => 'text',
            ]);
        }
    }

    public function getByGroup(string $group): array
    {
        return $this->where('setting_group = ?', [$group], 'setting_key ASC');
    }

    public function getAll(): array
    {
        $rows = $this->all('setting_group ASC, setting_key ASC');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function getGrouped(): array
    {
        $rows = $this->all('setting_group ASC, setting_key ASC');
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['setting_group']][$row['setting_key']] = $row;
        }
        return $grouped;
    }

    public function updateBulk(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
