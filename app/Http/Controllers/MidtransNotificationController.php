<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;

class MidtransNotificationController extends Controller
{
    /**
     * Handle Midtrans Webhook Notification
     * 
     * Endpoint: POST /midtrans/notification
     * 
     * Midtrans akan mengirim:
     * - order_id
     * - transaction_id
     * - transaction_status (pending|settlement|capture|cancel|deny|expire)
     * - payment_type
     * - signature_key (untuk verifikasi)
     * - settlement_time (ketika status = settlement)
     */
    public function handleNotification(Request $request)
    {
        $data = $request->all();

        Log::channel('midtrans')->info('Midtrans Notification Received', [
            'order_id' => data_get($data, 'order_id'),
            'transaction_status' => data_get($data, 'transaction_status'),
            'transaction_id' => data_get($data, 'transaction_id'),
        ]);

        // Verifikasi signature
        if (!$this->verifySignature($data)) {
            Log::channel('midtrans')->warning('Invalid Signature', [
                'order_id' => data_get($data, 'order_id'),
                'received_signature' => data_get($data, 'signature_key'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
            ], 403);
        }

        $orderId = data_get($data, 'order_id');
        $transactionStatus = data_get($data, 'transaction_status');
        $transactionId = data_get($data, 'transaction_id');

        // Find pesanan by order_id
        $pesanan = Pesanan::where('midtrans_order_id', $orderId)->first();

        if (!$pesanan) {
            Log::channel('midtrans')->warning('Pesanan Not Found', ['order_id' => $orderId]);
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        // Map transaction_status ke status_bayar
        $isPaid = in_array($transactionStatus, ['settlement', 'capture'], true);

        // Update pesanan
        $pesanan->update([
            'payment_reference' => $transactionId,
            'status_bayar' => $isPaid ? 1 : 0,
            'paid_at' => $isPaid ? now() : null,
            'payment_payload' => json_encode($data),
        ]);

        Log::channel('midtrans')->info('Pesanan Updated from Notification', [
            'order_id' => $orderId,
            'idpesanan' => $pesanan->idpesanan,
            'status_bayar' => $isPaid ? 1 : 0,
            'transaction_status' => $transactionStatus,
            'settlement_time' => data_get($data, 'settlement_time'),
        ]);

        // Handle sesuai status
        $this->handleTransactionStatus($pesanan, $transactionStatus, $data);

        return response()->json([
            'success' => true,
            'message' => 'Notification processed.',
        ], 200);
    }

    /**
     * Verifikasi signature dari Midtrans
     * 
     * Formula: Hash SHA512 dari: order_id + transaction_status + gross_amount + SERVER_KEY
     */
    private function verifySignature(array $data): bool
    {
        $orderId = data_get($data, 'order_id');
        $transactionStatus = data_get($data, 'transaction_status');
        $grossAmount = data_get($data, 'gross_amount');
        $receivedSignature = data_get($data, 'signature_key');

        $serverKey = config('services.midtrans.server_key');

        // Build signature
        $signatureString = $orderId . $transactionStatus . $grossAmount . $serverKey;
        $calculatedSignature = hash('sha512', $signatureString);

        return hash_equals($calculatedSignature, $receivedSignature ?? '');
    }

    /**
     * Handle sesuai transaction status
     */
    private function handleTransactionStatus(Pesanan $pesanan, string $status, array $data): void
    {
        switch ($status) {
            case 'settlement':
            case 'capture':
                Log::channel('midtrans')->info('Transaction Settled', [
                    'idpesanan' => $pesanan->idpesanan,
                    'order_id' => $pesanan->midtrans_order_id,
                    'settled_at' => data_get($data, 'settlement_time'),
                ]);
                // Business logic on settlement:
                // - Send notification to vendor
                // - Update inventory
                // - Generate receipt, QR code, etc.

                // Generate QR code for the order and save to storage (if not exists)
                try {
                    if (empty($pesanan->qr_code_path)) {
                        $result = Builder::create()
                            ->data((string) $pesanan->idpesanan)
                            ->size(400)
                            ->build();

                        $png = $result->getString();
                        $filename = "qrcodes/order-{$pesanan->idpesanan}.png";
                        // store on public disk
                        Storage::disk('public')->put($filename, $png);
                        $pesanan->qr_code_path = $filename;
                        $pesanan->save();

                        Log::channel('midtrans')->info('QR code generated for pesanan', [
                            'idpesanan' => $pesanan->idpesanan,
                            'qr_code_path' => $filename,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::channel('midtrans')->error('Failed to generate QR code', [
                        'idpesanan' => $pesanan->idpesanan,
                        'error' => $e->getMessage(),
                    ]);
                }
                break;

            case 'pending':
                Log::channel('midtrans')->info('Transaction Pending', [
                    'idpesanan' => $pesanan->idpesanan,
                    'order_id' => $pesanan->midtrans_order_id,
                    'payment_type' => data_get($data, 'payment_type'),
                ]);
                break;

            case 'cancel':
            case 'deny':
            case 'expire':
                Log::channel('midtrans')->warning('Transaction Failed', [
                    'idpesanan' => $pesanan->idpesanan,
                    'order_id' => $pesanan->midtrans_order_id,
                    'transaction_status' => $status,
                    'reason' => data_get($data, 'status_message'),
                ]);
                break;
        }
    }
}
