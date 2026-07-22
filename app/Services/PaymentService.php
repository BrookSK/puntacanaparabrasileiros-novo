<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Core\App;
use Core\Database;

/**
 * Serviço central de pagamentos.
 * Orquestra os gateways (PayPal, Stripe) e gerencia status.
 */
class PaymentService
{
    private Database $db;
    private Booking $bookingModel;
    private Payment $paymentModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->bookingModel = new Booking();
        $this->paymentModel = new Payment();
    }

    /**
     * Cria um registro de pagamento pendente.
     */
    public function createPendingPayment(int $bookingId, string $gateway, float $amount, string $type = 'full'): int
    {
        return $this->paymentModel->create([
            'booking_id' => $bookingId,
            'gateway' => $gateway,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'pending',
            'type' => $type,
        ]);
    }

    /**
     * Confirma pagamento após sucesso do gateway.
     */
    public function confirmPayment(int $paymentId, string $transactionId, ?string $gatewayResponse = null): void
    {
        $this->db->beginTransaction();

        try {
            // Atualizar pagamento
            $this->paymentModel->markCompleted($paymentId, $transactionId, $gatewayResponse);

            // Buscar dados do pagamento
            $payment = $this->paymentModel->find($paymentId);
            if (!$payment) {
                throw new \RuntimeException('Pagamento não encontrado.');
            }

            $bookingId = (int) $payment['booking_id'];

            // Atualizar booking
            $totalPaid = $this->paymentModel->getTotalPaidForBooking($bookingId);
            $booking = $this->bookingModel->find($bookingId);
            $total = (float) $booking['total'];
            $due = max(0, $total - $totalPaid);

            $status = $due <= 0 ? 'booked' : 'partially_paid';

            $this->bookingModel->update($bookingId, [
                'paid_amount' => $totalPaid,
                'due_amount' => $due,
                'status' => $status,
            ]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Marca pagamento como falho.
     */
    public function failPayment(int $paymentId, ?string $gatewayResponse = null): void
    {
        $this->paymentModel->markFailed($paymentId, $gatewayResponse);
    }

    /**
     * Processa pagamento restante (remaining) de um booking parcialmente pago.
     */
    public function processRemainingPayment(int $bookingId, string $gateway): array
    {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            throw new \RuntimeException('Reserva não encontrada.');
        }

        $dueAmount = (float) $booking['due_amount'];
        if ($dueAmount <= 0) {
            throw new \RuntimeException('Não há valor pendente para esta reserva.');
        }

        $paymentId = $this->createPendingPayment($bookingId, $gateway, $dueAmount, 'remaining');

        return [
            'payment_id' => $paymentId,
            'amount' => $dueAmount,
            'booking' => $booking,
        ];
    }

    /**
     * Calcula valor parcial baseado no percentual configurado.
     */
    public function calculatePartialAmount(float $total, ?float $percentOverride = null): float
    {
        $percent = $percentOverride ?? (float) (App::getInstance()->setting('partial_payment_percent', '50'));
        return round($total * ($percent / 100), 2);
    }

    /**
     * Verifica se pagamento parcial está habilitado globalmente.
     */
    public function isPartialPaymentEnabled(): bool
    {
        return (bool) App::getInstance()->setting('partial_payment_enabled', '0');
    }

    /**
     * Retorna gateways disponíveis (habilitados).
     */
    public function getAvailableGateways(): array
    {
        $app = App::getInstance();
        $gateways = [];

        if ($app->setting('paypal_enabled', '0') === '1') {
            $gateways[] = [
                'id' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Pague com PayPal ou cartão de crédito',
            ];
        }

        if ($app->setting('stripe_enabled', '0') === '1') {
            $gateways[] = [
                'id' => 'stripe',
                'name' => 'Cartão de Crédito',
                'description' => 'Pague com cartão via Stripe',
            ];
        }

        return $gateways;
    }
}
