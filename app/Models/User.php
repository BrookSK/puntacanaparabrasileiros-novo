<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone',
        'country', 'address', 'city', 'avatar', 'role', 'status',
        'email_verified_at', 'remember_token', 'last_login_at',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->findWhere('email', $email);
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        if ($user['status'] !== 'active') {
            return null;
        }
        // Atualizar último login
        $this->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        unset($user['password']);
        return $user;
    }

    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->create($data);
    }

    public function updatePassword(int $id, string $newPassword): int
    {
        return $this->db->update(
            $this->table,
            ['password' => password_hash($newPassword, PASSWORD_BCRYPT)],
            'id = ?',
            [$id]
        );
    }

    public function getByRole(string $role, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, 'role = ?', [$role], 'first_name ASC');
    }

    public function search(string $query, int $page = 1, int $perPage = 20): array
    {
        $search = '%' . $query . '%';
        return $this->paginate(
            $page,
            $perPage,
            'first_name LIKE ? OR last_name LIKE ? OR email LIKE ?',
            [$search, $search, $search]
        );
    }
}
