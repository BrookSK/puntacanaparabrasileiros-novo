<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Session;

/**
 * Serviço de carrinho (multi-item: trips + transfers).
 * Usa sessão PHP para persistência.
 */
class CartService
{
    private Session $session;
    private const CART_KEY = 'cart_items';
    private const TRANSFER_CART_KEY = 'transfer_cart_items';
    private const EXPIRATION = 604800; // 7 dias

    public function __construct()
    {
        $this->session = App::getInstance()->getSession();
    }

    // ==================== TRIPS ====================

    public function addTrip(array $item): void
    {
        $cart = $this->getTrips();
        $item['added_at'] = time();
        $item['id'] = uniqid('trip_', true);
        $cart[] = $item;
        $this->session->set(self::CART_KEY, $cart);
    }

    public function getTrips(): array
    {
        return $this->session->get(self::CART_KEY, []);
    }

    public function removeTrip(string $itemId): void
    {
        $cart = $this->getTrips();
        $cart = array_filter($cart, fn($item) => $item['id'] !== $itemId);
        $this->session->set(self::CART_KEY, array_values($cart));
    }

    public function clearTrips(): void
    {
        $this->session->set(self::CART_KEY, []);
    }

    // ==================== TRANSFERS ====================

    public function addTransfer(array $transfer): void
    {
        $cart = $this->getTransfers();
        $transfer['added_at'] = time();
        $transfer['id'] = uniqid('transfer_', true);
        $cart[] = $transfer;
        $this->session->set(self::TRANSFER_CART_KEY, $cart);
    }

    public function getTransfers(): array
    {
        return $this->session->get(self::TRANSFER_CART_KEY, []);
    }

    public function removeTransfer(string $itemId): void
    {
        $cart = $this->getTransfers();
        $cart = array_filter($cart, fn($item) => $item['id'] !== $itemId);
        $this->session->set(self::TRANSFER_CART_KEY, array_values($cart));
    }

    /**
     * Remove grupo de transfers (ida+volta).
     */
    public function removeTransferGroup(string $groupId): void
    {
        $cart = $this->getTransfers();
        $cart = array_filter($cart, fn($item) => ($item['group_id'] ?? '') !== $groupId);
        $this->session->set(self::TRANSFER_CART_KEY, array_values($cart));
    }

    public function clearTransfers(): void
    {
        $this->session->set(self::TRANSFER_CART_KEY, []);
    }

    // ==================== TOTAIS ====================

    public function getTripTotal(): float
    {
        $total = 0.0;
        foreach ($this->getTrips() as $item) {
            $total += (float) ($item['total'] ?? $item['price'] ?? 0);
        }
        return $total;
    }

    public function getTransferTotal(): float
    {
        $total = 0.0;
        foreach ($this->getTransfers() as $item) {
            $total += (float) ($item['price'] ?? 0);
        }
        return $total;
    }

    public function getGrandTotal(): float
    {
        return $this->getTripTotal() + $this->getTransferTotal();
    }

    public function getPartialTotal(float $percent): float
    {
        return round($this->getGrandTotal() * ($percent / 100), 2);
    }

    public function getItemCount(): int
    {
        return count($this->getTrips()) + count($this->getTransfers());
    }

    public function isEmpty(): bool
    {
        return $this->getItemCount() === 0;
    }

    public function hasOnlyTransfers(): bool
    {
        return empty($this->getTrips()) && !empty($this->getTransfers());
    }

    // ==================== LIMPEZA ====================

    public function clearAll(): void
    {
        $this->clearTrips();
        $this->clearTransfers();
    }

    /**
     * Remove itens expirados do carrinho.
     */
    public function cleanExpired(): void
    {
        $now = time();

        $trips = $this->getTrips();
        $trips = array_filter($trips, fn($item) => ($now - ($item['added_at'] ?? 0)) < self::EXPIRATION);
        $this->session->set(self::CART_KEY, array_values($trips));

        $transfers = $this->getTransfers();
        $transfers = array_filter($transfers, fn($item) => ($now - ($item['added_at'] ?? 0)) < self::EXPIRATION);
        $this->session->set(self::TRANSFER_CART_KEY, array_values($transfers));
    }

    /**
     * Retorna resumo completo do carrinho para exibição.
     */
    public function getSummary(): array
    {
        return [
            'trips' => $this->getTrips(),
            'transfers' => $this->getTransfers(),
            'trip_total' => $this->getTripTotal(),
            'transfer_total' => $this->getTransferTotal(),
            'grand_total' => $this->getGrandTotal(),
            'item_count' => $this->getItemCount(),
            'has_only_transfers' => $this->hasOnlyTransfers(),
        ];
    }
}
