<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Voucher;
use Core\App;
use Core\Database;
use Core\View;

/**
 * Serviço de geração, armazenamento e envio de vouchers.
 */
class VoucherService
{
    private Voucher $voucherModel;
    private Database $db;
    private string $vouchersPath;

    public function __construct()
    {
        $this->voucherModel = new Voucher();
        $this->db = Database::getInstance();
        $this->vouchersPath = BASE_PATH . '/public/uploads/vouchers';
    }

    /**
     * Gera voucher HTML para um item de booking (trip).
     */
    public function generateTripVoucher(int $bookingId, int $bookingItemId): array
    {
        $booking = $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
        $item = $this->db->fetchOne(
            "SELECT bi.*, t.title as trip_title, t.meeting_point, t.featured_image
             FROM booking_items bi
             INNER JOIN trips t ON bi.trip_id = t.id
             WHERE bi.id = ?",
            [$bookingItemId]
        );

        if (!$booking || !$item) {
            throw new \RuntimeException('Booking ou item não encontrado.');
        }

        $reference = $this->voucherModel->generateReference();

        // Renderizar template do voucher
        $html = View::render('vouchers/trip-voucher', [
            'booking' => $booking,
            'item' => $item,
            'reference' => $reference,
            'logo' => App::getInstance()->setting('voucher_logo', ''),
            'footer_text' => App::getInstance()->setting('voucher_footer_text', ''),
            'instructions' => App::getInstance()->setting('voucher_instructions', ''),
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($reference),
        ]);

        // Salvar arquivo
        $filename = "voucher-{$bookingId}-{$item['trip_id']}-{$reference}.html";
        $filePath = $this->vouchersPath . '/' . $filename;
        file_put_contents($filePath, $html);

        // Registrar no banco
        $voucherId = $this->voucherModel->create([
            'booking_id' => $bookingId,
            'booking_item_id' => $bookingItemId,
            'reference_code' => $reference,
            'type' => 'trip',
            'file_path' => $filename,
        ]);

        // Log
        $this->db->insert('voucher_log', [
            'booking_id' => $bookingId,
            'reference_code' => $reference,
            'email' => $booking['billing_email'],
            'trip_name' => $item['trip_title'],
            'file_path' => $filename,
        ]);

        return [
            'id' => $voucherId,
            'reference' => $reference,
            'file_path' => $filePath,
            'filename' => $filename,
            'html' => $html,
        ];
    }

    /**
     * Gera voucher HTML para um transfer booking.
     */
    public function generateTransferVoucher(int $transferBookingId): array
    {
        $transfer = $this->db->fetchOne(
            "SELECT tb.*, tv.title as vehicle_title, tv.image as vehicle_image,
                    tlo.title as origin_title, tld.title as destination_title
             FROM transfer_bookings tb
             INNER JOIN transfer_vehicles tv ON tb.vehicle_id = tv.id
             INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
             INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
             WHERE tb.id = ?",
            [$transferBookingId]
        );

        if (!$transfer) {
            throw new \RuntimeException('Transfer booking não encontrado.');
        }

        $reference = $this->voucherModel->generateReference();

        $html = View::render('vouchers/transfer-voucher', [
            'transfer' => $transfer,
            'reference' => $reference,
            'logo' => App::getInstance()->setting('voucher_logo', ''),
            'footer_text' => App::getInstance()->setting('voucher_footer_text', ''),
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($reference),
        ]);

        $filename = "voucher-transfer-{$transferBookingId}-{$reference}.html";
        $filePath = $this->vouchersPath . '/' . $filename;
        file_put_contents($filePath, $html);

        $voucherId = $this->voucherModel->create([
            'booking_id' => $transfer['booking_id'],
            'transfer_booking_id' => $transferBookingId,
            'reference_code' => $reference,
            'type' => 'transfer',
            'file_path' => $filename,
        ]);

        return [
            'id' => $voucherId,
            'reference' => $reference,
            'file_path' => $filePath,
            'filename' => $filename,
            'html' => $html,
        ];
    }

    /**
     * Envia vouchers por email ao cliente.
     */
    public function sendVouchersByEmail(int $bookingId): bool
    {
        $vouchers = $this->voucherModel->getByBooking($bookingId);
        if (empty($vouchers)) return false;

        $booking = $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
        if (!$booking) return false;

        $emailService = new EmailService();
        $attachments = [];

        foreach ($vouchers as $voucher) {
            $filePath = $this->vouchersPath . '/' . $voucher['file_path'];
            if (file_exists($filePath)) {
                $attachments[] = $filePath;
            }
        }

        $sent = $emailService->sendTemplate(
            $booking['billing_email'],
            $booking['billing_first_name'] . ' ' . $booking['billing_last_name'],
            'Seus Vouchers - Punta Cana para Brasileiros',
            'voucher-email',
            ['booking' => $booking, 'vouchers' => $vouchers],
            $attachments
        );

        if ($sent) {
            foreach ($vouchers as $voucher) {
                $this->voucherModel->markEmailSent((int) $voucher['id']);
            }
        }

        return $sent;
    }

    /**
     * Retorna o caminho completo do arquivo de voucher.
     */
    public function getVoucherFilePath(string $filename): ?string
    {
        $filePath = $this->vouchersPath . '/' . $filename;
        return file_exists($filePath) ? $filePath : null;
    }

    /**
     * Limpa vouchers antigos (mais de X dias).
     */
    public function cleanupOldVouchers(): int
    {
        $days = (int) App::getInstance()->setting('voucher_cleanup_days', '90');
        $expired = $this->voucherModel->getExpired($days);
        $count = 0;

        foreach ($expired as $voucher) {
            $filePath = $this->vouchersPath . '/' . $voucher['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->voucherModel->delete((int) $voucher['id']);
            $count++;
        }

        return $count;
    }
}
