<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class NewsletterSubscriber extends Model
{
    protected string $table = 'newsletter_subscribers';
    protected array $fillable = ['email', 'name', 'status', 'source', 'ip_address'];

    public function subscribe(string $email, ?string $name = null, string $source = 'blog_sidebar', ?string $ip = null): bool
    {
        $existing = $this->findWhere('email', $email);
        if ($existing) {
            if ($existing['status'] === 'unsubscribed') {
                $this->update((int) $existing['id'], ['status' => 'active', 'unsubscribed_at' => null]);
                return true;
            }
            return false; // já inscrito
        }

        $this->create([
            'email' => $email,
            'name' => $name,
            'status' => 'active',
            'source' => $source,
            'ip_address' => $ip,
        ]);
        return true;
    }

    public function unsubscribe(string $email): bool
    {
        $subscriber = $this->findWhere('email', $email);
        if (!$subscriber) return false;

        $this->db->update($this->table, [
            'status' => 'unsubscribed',
            'unsubscribed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [(int) $subscriber['id']]);
        return true;
    }

    public function getActive(): array
    {
        return $this->where("status = 'active'", [], 'subscribed_at DESC');
    }

    public function getActiveCount(): int
    {
        return $this->count("status = 'active'");
    }

    public function getAll(int $page = 1, int $perPage = 30, ?string $status = null): array
    {
        $where = '1=1';
        $params = [];
        if ($status) {
            $where = 'status = ?';
            $params = [$status];
        }
        return $this->paginate($page, $perPage, $where, $params, 'subscribed_at DESC');
    }

    public function exportActive(): array
    {
        return $this->db->fetchAll(
            "SELECT email, name, subscribed_at FROM newsletter_subscribers WHERE status = 'active' ORDER BY subscribed_at DESC"
        );
    }
}
