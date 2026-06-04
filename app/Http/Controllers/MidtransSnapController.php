<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransSnapController extends Controller
{
    /**
     * Generate Snap Token dari Midtrans
     * 
     * Endpoint: POST /kantin/snap/token/{idpesanan}
     * Response: { token: "string", redirect_url: "string", ... }
     */
    public function generateToken(Request $request, int $idpesanan)
    {
        $pesanan = Pesanan::findOrFail($idpesanan);

        // Validasi pesanan tidak sudah lunas
        if ((int) $pesanan->status_bayar === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah lunas.',
            ], 422);
        }

        // Selalu gunakan order_id unik baru agar retry pembayaran tidak mentok "order_id has already been taken"
        $orderId = 'KNT-' . $pesanan->idpesanan . '-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(4));
        $pesanan->update(['midtrans_order_id' => $orderId]);

        $payload = $this->buildTransactionPayload($pesanan, $orderId);

        Log::channel('midtrans')->info('Generating Snap Token', [
            'order_id' => $orderId,
            'gross_amount' => $pesanan->total,
            'customer_name' => $pesanan->nama,
        ]);

        $isProduction = (bool) config('services.midtrans.is_production', false);
        $baseUrl = $isProduction ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
        $serverKey = (string) config('services.midtrans.server_key');

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($baseUrl . '/snap/v1/transactions', $payload);

        if (!$response->successful()) {
            $errorData = $response->json();
            if (!is_array($errorData)) {
                $errorData = ['raw_body' => $response->body()];
            }
            Log::channel('midtrans')->error('Snap Token Error', [
                'order_id' => $orderId,
                'status_code' => $response->status(),
                'error' => data_get($errorData, 'status_message'),
                'full_response' => $errorData,
            ]);

            return response()->json([
                'success' => false,
                'message' => data_get($errorData, 'status_message', 'Gagal generate token Snap.'),
                'error_details' => $errorData,
            ], 422);
        }

        $transactionData = $response->json();
        
        // Simpan transaction_id dan snap_token ke database
        $pesanan->update([
            'payment_reference' => data_get($transactionData, 'token'),
            'payment_type' => 'snap',
            'payment_payload' => json_encode($transactionData),
        ]);

        Log::channel('midtrans')->info('Snap Token Generated Successfully', [
            'order_id' => $orderId,
            'transaction_id' => data_get($transactionData, 'transaction_id'),
            'snap_token' => data_get($transactionData, 'token'),
            'transaction_status' => data_get($transactionData, 'transaction_status'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Snap Token berhasil dibuat.',
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'snap_token' => data_get($transactionData, 'token'),
                'redirect_url' => data_get($transactionData, 'redirect_url'),
                'transaction_id' => data_get($transactionData, 'transaction_id'),
                'order_id' => $orderId,
                'transaction_status' => data_get($transactionData, 'transaction_status'),
            ],
        ]);
    }

    /**
     * Cek status transaksi dari Midtrans
     * 
     * Endpoint: GET /kantin/snap/status/{idpesanan}
     * Response: { status_bayar: 0/1, transaction_status: "pending|settlement|...", ... }
     */
    public function checkTransactionStatus(int $idpesanan)
    {
        $pesanan = Pesanan::findOrFail($idpesanan);

        if (!$pesanan->midtrans_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada transaksi Midtrans untuk pesanan ini.',
            ], 422);
        }

        Log::channel('midtrans')->info('Checking Transaction Status', [
            'order_id' => $pesanan->midtrans_order_id,
            'payment_reference' => $pesanan->payment_reference,
        ]);

        $response = $this->midtransRequest('GET', '/v2/' . $pesanan->midtrans_order_id . '/status');

        if (!$response->successful()) {
            $errorData = $response->json();
            Log::channel('midtrans')->error('Status Check Error', [
                'order_id' => $pesanan->midtrans_order_id,
                'error' => data_get($errorData, 'status_message'),
            ]);

            return response()->json([
                'success' => false,
                'message' => data_get($errorData, 'status_message', 'Gagal mengecek status.'),
            ], 422);
        }

        $statusData = $response->json();
        $transactionStatus = data_get($statusData, 'transaction_status', 'unknown');

        // Mapping transaction_status ke status_bayar (0=pending, 1=settlement)
        $isPaid = in_array($transactionStatus, ['settlement', 'capture'], true);

        $pesanan->update([
            'payment_payload' => json_encode($statusData),
            'status_bayar' => $isPaid ? 1 : 0,
            'paid_at' => $isPaid ? now() : null,
        ]);

        Log::channel('midtrans')->info('Transaction Status Updated', [
            'order_id' => $pesanan->midtrans_order_id,
            'status_bayar' => $isPaid ? 1 : 0,
            'transaction_status' => $transactionStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => $isPaid ? 'Pembayaran sudah lunas.' : 'Pembayaran belum selesai.',
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'status_bayar' => $isPaid ? 1 : 0,
                'transaction_status' => $transactionStatus,
                'order_id' => $pesanan->midtrans_order_id,
                'transaction_id' => data_get($statusData, 'transaction_id'),
                'payment_type' => data_get($statusData, 'payment_type'),
                'gross_amount' => (int) data_get($statusData, 'gross_amount'),
                'paid_at' => $pesanan->paid_at ? $pesanan->paid_at->toDateTimeString() : null,
            ],
        ]);
    }

    /**
     * Build transaction payload untuk Midtrans Snap
     */
    private function buildTransactionPayload(Pesanan $pesanan, string $orderId): array
    {
        $publicBaseUrl = rtrim((string) config('services.midtrans.public_base_url', config('app.url')), '/');

        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama,
                'email' => 'customer@kantin.local',
                'phone' => '081234567890',
            ],
            'item_details' => $this->buildItemDetails($pesanan),
            'callbacks' => [
                'finish' => $publicBaseUrl . '/midtrans/callback/finish',
                'error' => $publicBaseUrl . '/midtrans/callback/error',
                'pending' => $publicBaseUrl . '/midtrans/callback/pending',
            ],
            // Sandbox auto-settlement untuk testing
            'custom_expiry' => [
                'expiry_time' => 3600, // 1 jam
                'unit' => 'second',
            ],
        ];
    }

    /**
     * Build item details dari order pesanan
     */
    private function buildItemDetails(Pesanan $pesanan): array
    {
        $items = [];
        
        foreach ($pesanan->detailPesanan as $detail) {
            $items[] = [
                'id' => (string) $detail->idmenu,
                'price' => (int) $detail->harga,
                'quantity' => (int) $detail->jumlah,
                'name' => $detail->menu->nama_menu ?? 'Menu Item',
            ];
        }

        return $items;
    }

    /**
     * Midtrans API Request Helper
     */
    private function midtransRequest(string $method, string $path, array $payload = [])
    {
        $isProduction = (bool) config('services.midtrans.is_production', false);
        $baseUrl = $isProduction ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
        $serverKey = (string) config('services.midtrans.server_key');

        $req = Http::withBasicAuth($serverKey, '')->acceptJson();

        if ($method === 'GET') {
            return $req->get($baseUrl . $path);
        }

        return $req->post($baseUrl . $path, $payload);
    }
}
