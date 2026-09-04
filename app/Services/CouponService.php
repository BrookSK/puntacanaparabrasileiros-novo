<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;

/**
 * Validação e cálculo de desconto de cupons promocionais.
 */
class CouponService
{
    private Coupon $model;

    public function __construct()
    {
        $this->model = new Coupon();
    }

    /**
     * Valida um cupom para um determinado subtotal.
     *
     * @return array{
     *     valid: bool,
     *     message: string,
     *     discount: float,
     *     coupon: array|null
     * }
     */
    public function validate(string $code, float $subtotal): array
    {
        $fail = fn(string $msg) => [
            'valid' => false, 'message' => $msg, 'discount' => 0.0, 'coupon' => null,
        ];

        $code = trim($code);
        if ($code === '') {
            return $fail('Informe um código de cupom.');
        }

        $coupon = $this->model->findByCode($code);
        if (!$coupon) {
            return $fail('Cupom inválido ou inexistente.');
        }

        if ((int) $coupon['active'] !== 1) {
            return $fail('Este cupom não está mais ativo.');
        }

        $now = time();
        if (!empty($coupon['starts_at']) && strtotime($coupon['starts_at']) > $now) {
            return $fail('Este cupom ainda não está disponível.');
        }
        if (!empty($coupon['expires_at']) && strtotime($coupon['expires_at']) < $now) {
            return $fail('Este cupom expirou.');
        }

        if (!empty($coupon['max_uses']) && (int) $coupon['used_count'] >= (int) $coupon['max_uses']) {
            return $fail('Este cupom atingiu o limite de usos.');
        }

        if (!empty($coupon['min_order']) && $subtotal < (float) $coupon['min_order']) {
            return $fail('Este cupom exige um pedido mínimo de ' . money((float) $coupon['min_order']) . '.');
        }

        $discount = $this->calculateDiscount($coupon, $subtotal);
        if ($discount <= 0) {
            return $fail('Este cupom não gera desconto para este pedido.');
        }

        return [
            'valid' => true,
            'message' => 'Cupom aplicado com sucesso!',
            'discount' => $discount,
            'coupon' => $coupon,
        ];
    }

    /**
     * Calcula o valor do desconto, limitado ao subtotal.
     */
    public function calculateDiscount(array $coupon, float $subtotal): float
    {
        $value = (float) $coupon['value'];
        if ($coupon['type'] === 'percentage') {
            $discount = round($subtotal * ($value / 100), 2);
        } else {
            $discount = round($value, 2);
        }
        // Nunca descontar mais que o subtotal
        return min($discount, $subtotal);
    }
}
