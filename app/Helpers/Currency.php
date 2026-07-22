<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Helper de formatação de moeda.
 */
class Currency
{
    public static function format(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? setting('currency', 'USD');
        $symbol = setting('currency_symbol', '$');
        return $symbol . number_format($amount, 2, '.', ',');
    }

    public static function formatWithCode(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? setting('currency', 'USD');
        return self::format($amount, $currency) . ' ' . $currency;
    }

    public static function parse(string $value): float
    {
        $cleaned = preg_replace('/[^0-9.]/', '', $value);
        return (float) $cleaned;
    }
}
