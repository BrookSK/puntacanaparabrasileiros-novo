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
            "SELECT bi.*, t.title as trip_title, t.meeting_point, t.featured_image, t.duration, t.duration_unit, t.documents as trip_documents
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
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode(rtrim(App::getInstance()->setting('site_url', 'https://puntacananovo.lrvweb.com.br'), '/') . '/voucher/' . $reference . '/confirmar'),
        ]);

        // Salvar arquivo
        $safeTitle = preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($item['trip_title'] ?? 'passeio'));
        $filename = "Voucher-Viagem-{$safeTitle}-{$reference}.html";
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

        // Buscar booking para dados de pagamento
        $booking = $transfer['booking_id'] ? $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [(int) $transfer['booking_id']]) : null;

        $html = View::render('vouchers/transfer-voucher', [
            'transfer' => $transfer,
            'booking' => $booking,
            'reference' => $reference,
            'logo' => App::getInstance()->setting('voucher_logo', ''),
            'footer_text' => App::getInstance()->setting('voucher_footer_text', ''),
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode(rtrim(App::getInstance()->setting('site_url', 'https://puntacananovo.lrvweb.com.br'), '/') . '/voucher/' . $reference . '/confirmar'),
        ]);

        $safeRoute = preg_replace('/[^a-z0-9]+/', '-', mb_strtolower(($transfer['origin_title'] ?? '') . '-' . ($transfer['destination_title'] ?? '')));
        $filename = "Voucher-Transfer-{$safeRoute}-{$reference}.html";
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
     * Envia email de confirmação de transfer ao cliente.
     *
     * Detecta automaticamente o tipo de reserva (ida e volta, somente ida,
     * múltiplos) e monta as variáveis adequadas para o template
     * resources/views/emails/transfer-confirmation.php.
     */
    public function sendTransferConfirmationEmail(int $bookingId): bool
    {
        $booking = $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
        if (!$booking) return false;

        // Busca todos os transfers deste booking com dados de localização e veículo
        $transfers = $this->db->fetchAll(
            "SELECT tb.*, tv.title as vehicle_title,
                    tlo.title as origin_title, tld.title as destination_title
             FROM transfer_bookings tb
             INNER JOIN transfer_vehicles tv ON tb.vehicle_id = tv.id
             INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
             INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
             WHERE tb.booking_id = ?
             ORDER BY tb.date ASC, tb.time ASC",
            [$bookingId]
        );

        if (empty($transfers)) return false;

        // Determina o tipo de transfer para o template
        $count = count($transfers);
        if ($count >= 3) {
            $transferType = 'multiple';
        } elseif ($count === 2) {
            // Dois trechos = ida e volta
            $transferType = 'round_trip';
        } else {
            $transferType = 'one_way';
        }

        // Usa o group_id para separar grupos se disponível:
        // se houver exatamente 2 trechos com group_id iguais, é ida e volta
        if ($count === 2 && !empty($transfers[0]['group_id']) && $transfers[0]['group_id'] === $transfers[1]['group_id']) {
            $transferType = 'round_trip';
        }

        // Monta links dos vouchers já gerados para este booking
        $vouchers = $this->voucherModel->getByBooking($bookingId);
        $voucherLinks = [];
        foreach ($vouchers as $v) {
            if ($v['type'] !== 'transfer') continue;
            // Busca origin/destination a partir do transfer_booking_id
            $tb = null;
            if (!empty($v['transfer_booking_id'])) {
                $tb = $this->db->fetchOne(
                    "SELECT tlo.title as origin_title, tld.title as destination_title
                     FROM transfer_bookings tb
                     INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
                     INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
                     WHERE tb.id = ?",
                    [(int) $v['transfer_booking_id']]
                );
            }
            $voucherLinks[] = [
                'reference_code' => $v['reference_code'],
                'origin'         => $tb['origin_title'] ?? '',
                'destination'    => $tb['destination_title'] ?? '',
            ];
        }

        // Calcula total dos transfers
        $totalAmount = array_sum(array_column($transfers, 'price'));

        // Tipo de serviço predominante
        $serviceType = $transfers[0]['service_type'] ?? 'private';

        $emailService = new EmailService();
        return $emailService->sendTemplate(
            $booking['billing_email'],
            $booking['billing_first_name'] . ' ' . $booking['billing_last_name'],
            'Seu Transfer Confirmado — Punta Cana para Brasileiros',
            'transfer-confirmation',
            [
                'clientName'    => $booking['billing_first_name'] . ' ' . $booking['billing_last_name'],
                'bookingNumber' => $booking['booking_number'],
                'transferType'  => $transferType,
                'totalAmount'   => $totalAmount,
                'serviceType'   => $serviceType,
                'transfers'     => $transfers,
                'voucherLinks'  => $voucherLinks,
            ]
        );
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

        // Preparar dados dos vouchers com nomes dos passeios e trajetos
        foreach ($vouchers as &$v) {
            if ($v['type'] === 'trip' && !empty($v['booking_item_id'])) {
                $item = $this->db->fetchOne(
                    "SELECT t.title as trip_name FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.id = ?",
                    [(int) $v['booking_item_id']]
                );
                $v['trip_name'] = $item['trip_name'] ?? '';
            }
            if ($v['type'] === 'transfer' && !empty($v['transfer_booking_id'])) {
                $transfer = $this->db->fetchOne(
                    "SELECT tlo.title as origin_title, tld.title as destination_title
                     FROM transfer_bookings tb
                     LEFT JOIN transfer_locations tlo ON tb.origin_id = tlo.id
                     LEFT JOIN transfer_locations tld ON tb.destination_id = tld.id
                     WHERE tb.id = ?",
                    [(int) $v['transfer_booking_id']]
                );
                $v['route_name'] = ($transfer['origin_title'] ?? '') . ' → ' . ($transfer['destination_title'] ?? '');
            }
        }
        unset($v);

        $sent = $emailService->sendTemplate(
            $booking['billing_email'],
            $booking['billing_first_name'] . ' ' . $booking['billing_last_name'],
            'Seus Vouchers - Punta Cana para Brasileiros',
            'voucher-email',
            ['booking' => $booking, 'vouchers' => $vouchers, 'tripDocuments' => $this->getTripDocuments($bookingId)]
        );

        if ($sent) {
            foreach ($vouchers as $voucher) {
                $this->voucherModel->markEmailSent((int) $voucher['id']);
            }
        }

        return $sent;
    }

    /**
     * Envia vouchers por WhatsApp ao cliente — consolidado em poucas mensagens.
     */
    public function sendVouchersByWhatsApp(int $bookingId): bool
    {
        $vouchers = $this->voucherModel->getByBooking($bookingId);
        if (empty($vouchers)) return false;

        $booking = $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
        if (!$booking || empty($booking['billing_phone'])) return false;

        // Buscar instância WhatsApp ativa
        $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE connection_status = 'open' LIMIT 1");
        if (!$instance) {
            $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE is_default = 1 LIMIT 1");
            if (!$instance) {
                error_log("[VoucherService] WhatsApp: Nenhuma instância disponível para enviar vouchers booking #{$bookingId}");
                return false;
            }
        }

        $evolutionApi = EvolutionApi::fromInstance($instance);
        $phone = EvolutionApi::normalizePhone($booking['billing_phone']);
        $customerName = trim(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? ''));
        $siteUrl = rtrim(App::getInstance()->setting('site_url', 'https://puntacananovo.lrvweb.com.br'), '/');

        // ─── MENSAGEM 1: Documentos importantes + boas-vindas ───
        $msg1 = "*Punta Cana para Brasileiros* 🌴\n\n";
        $msg1 .= "Olá, *{$customerName}*! Sua reserva *{$booking['booking_number']}* foi confirmada! ✅\n\n";
        $msg1 .= "Apresente os vouchers abaixo pelo celular. *Não é necessário imprimir.*\n\n";
        $msg1 .= "📄 *Documentos Importantes:*\n";
        $msg1 .= "• Termos e Condições: {$siteUrl}/termos-e-condicoes\n";
        $msg1 .= "• Política de Cancelamento: {$siteUrl}/politicas-de-cancelamento\n\n";
        $msg1 .= "📱 Acesse online: {$siteUrl}/minha-conta/reservas\n\n";
        $msg1 .= "Dúvidas? Estamos à disposição!\n";
        $msg1 .= "Av. Barceló, nº 91, Local 7 - Plaza Arrecife, Verón, Punta Cana";

        $evolutionApi->sendText($phone, $msg1);
        usleep(1000000); // 1s entre mensagens

        // ─── Preparar dados dos vouchers com info completa ───
        $bookingItems = $this->db->fetchAll(
            "SELECT bi.*, t.title as trip_title FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
            [$bookingId]
        );
        $transferBookings = $this->db->fetchAll(
            "SELECT tb.*, tv.title as vehicle_title, tlo.title as origin_title, tld.title as destination_title
             FROM transfer_bookings tb
             INNER JOIN transfer_vehicles tv ON tb.vehicle_id = tv.id
             INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
             INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
             WHERE tb.booking_id = ?",
            [$bookingId]
        );

        // ─── ENVIAR CADA VOUCHER COM PDF + INFO ───
        $sentCount = 0;
        foreach ($vouchers as $voucher) {
            $filePath = $this->vouchersPath . '/' . $voucher['file_path'];
            if (!file_exists($filePath)) continue;

            // Montar caption com informações completas
            if ($voucher['type'] === 'trip') {
                // Buscar info do passeio
                $tripInfo = null;
                foreach ($bookingItems as $bi) {
                    if ((int)$bi['id'] === (int)($voucher['booking_item_id'] ?? 0)) {
                        $tripInfo = $bi;
                        break;
                    }
                }
                $tripName = $tripInfo['trip_title'] ?? ($voucher['trip_name'] ?? 'Passeio');
                $tripDate = $tripInfo['trip_date'] ?? '';
                $tripTime = $tripInfo['trip_time'] ?? '';
                $tripPax = (int)($tripInfo['total_pax'] ?? 1);

                $caption = "🎯 *VOUCHER PASSEIO*\n\n";
                $caption .= "📍 *{$tripName}*\n";
                $caption .= "📅 Data: {$tripDate}\n";
                $caption .= "⏰ Horário: {$tripTime}\n";
                $caption .= "👥 {$tripPax} passageiro(s)\n";
                $caption .= "🔢 Código: {$voucher['reference_code']}";

                $fileName = 'Voucher-Passeio-' . $voucher['reference_code'] . '.html';
            } else {
                // Buscar info do transfer
                $transferInfo = null;
                foreach ($transferBookings as $tb) {
                    if ((int)$tb['id'] === (int)($voucher['transfer_booking_id'] ?? 0)) {
                        $transferInfo = $tb;
                        break;
                    }
                }
                $vehicleName = $transferInfo['vehicle_title'] ?? 'Transfer';
                $origin = $transferInfo['origin_title'] ?? '';
                $destination = $transferInfo['destination_title'] ?? '';
                $date = $transferInfo['date'] ?? '';
                $time = $transferInfo['time'] ?? '';
                $pax = (int)($transferInfo['adults'] ?? 0) + (int)($transferInfo['children'] ?? 0) + (int)($transferInfo['infants'] ?? 0);

                $caption = "🚐 *VOUCHER TRANSFER*\n\n";
                $caption .= "🚗 *{$vehicleName}*\n";
                $caption .= "📍 {$origin} → {$destination}\n";
                $caption .= "📅 Data: {$date}\n";
                $caption .= "⏰ Horário: {$time}\n";
                $caption .= "👥 {$pax} passageiro(s)\n";
                $caption .= "🔢 Código: {$voucher['reference_code']}";

                $fileName = 'Voucher-Transfer-' . $voucher['reference_code'] . '.html';
            }

            // Enviar como documento PDF
            $fileContent = file_get_contents($filePath);

            // Converter HTML para PDF
            $pdfContent = null;
            if (class_exists('\\Dompdf\\Dompdf')) {
                try {
                    $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);
                    $dompdf->loadHtml($fileContent);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    $pdfContent = $dompdf->output();
                    $fileName = str_replace('.html', '.pdf', $fileName);
                    $mimeType = 'application/pdf';
                } catch (\Throwable $e) {
                    error_log("[VoucherService] PDF generation error: " . $e->getMessage());
                    $pdfContent = null;
                }
            }

            // Se não conseguiu gerar PDF, envia HTML como fallback
            if ($pdfContent) {
                $fileBase64 = base64_encode($pdfContent);
            } else {
                $fileBase64 = base64_encode($fileContent);
                $mimeType = 'text/html';
            }

            try {
                $evolutionApi->sendMedia($phone, 'document', $fileBase64, $caption, $fileName, $mimeType);
                $this->voucherModel->markWhatsAppSent((int) $voucher['id']);
                $sentCount++;
            } catch (\Throwable $e) {
                error_log("[VoucherService] WhatsApp send error: " . $e->getMessage());
            }

            usleep(1000000); // 1s entre cada voucher
        }

        return $sentCount > 0;
    }

    /**
     * Retorna documentos extras dos passeios de um booking.
     */
    private function getTripDocuments(int $bookingId): array
    {
        $items = $this->db->fetchAll(
            "SELECT t.title, t.documents FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
            [$bookingId]
        );
        $docs = [];
        foreach ($items as $item) {
            if (!empty($item['documents'])) {
                $tripDocs = json_decode($item['documents'], true);
                if (is_array($tripDocs)) {
                    foreach ($tripDocs as $doc) {
                        $doc['trip_name'] = $item['title'];
                        $docs[] = $doc;
                    }
                }
            }
        }
        return $docs;
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
