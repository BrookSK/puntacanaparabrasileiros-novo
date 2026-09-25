<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Demanda de Suporte criada no painel e enviada ao LRV (helpdeskON).
 *
 * A demanda é sempre gravada localmente; o envio ao LRV é registrado nos
 * campos lrv_* / sync_status. Ver App\Services\LrvService.
 */
class SupportTicket extends Model
{
    protected string $table = 'support_tickets';

    protected array $fillable = [
        'title', 'description', 'priority', 'category',
        'requester_name', 'requester_company', 'external_ref',
        'lrv_id', 'lrv_client_ticket_number', 'lrv_status',
        'sync_status', 'sync_error', 'synced_at', 'created_by',
    ];

    /**
     * Prioridades aceitas pela API do LRV.
     */
    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    /**
     * Gera uma referência externa única para o chamado (external_ref).
     * Formato: PUNTACANA-{ano}-{id zero-padded}. Como o id só existe após o
     * insert, geramos com base no id retornado (ver o controller).
     */
    public static function buildExternalRef(int $id): string
    {
        return sprintf('PUNTACANA-%s-%06d', date('Y'), $id);
    }

    /**
     * Marca a demanda como sincronizada com o LRV, guardando os dados de retorno.
     */
    public function markSynced(int $id, array $lrvData): void
    {
        $this->update($id, [
            'lrv_id' => isset($lrvData['id']) ? (int) $lrvData['id'] : null,
            'lrv_client_ticket_number' => isset($lrvData['client_ticket_number'])
                ? (int) $lrvData['client_ticket_number'] : null,
            'lrv_status' => $lrvData['status'] ?? 'open',
            'sync_status' => 'synced',
            'sync_error' => null,
            'synced_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Marca a demanda como falha de sincronização (fica pendente de reenvio).
     */
    public function markFailed(int $id, string $error): void
    {
        $this->update($id, [
            'sync_status' => 'failed',
            'sync_error' => mb_substr($error, 0, 2000),
        ]);
    }
}
