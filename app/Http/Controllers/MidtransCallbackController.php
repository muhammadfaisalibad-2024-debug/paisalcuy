<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    /**
     * Callback setelah transaksi selesai (settlement)
     * Endpoint: GET /midtrans/callback/finish
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionId = $request->query('transaction_id');
        $transactionStatus = $request->query('transaction_status');

        Log::channel('midtrans')->info('Callback Finish', [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'transaction_status' => $transactionStatus,
        ]);

        return view('midtrans.callback.finish', [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'message' => 'Pembayaran berhasil! Pesanan Anda sudah lunas.',
        ]);
    }

    /**
     * Callback saat terjadi error
     * Endpoint: GET /midtrans/callback/error
     */
    public function error(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionId = $request->query('transaction_id');

        Log::channel('midtrans')->warning('Callback Error', [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
        ]);

        return view('midtrans.callback.error', [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'message' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
        ]);
    }

    /**
     * Callback saat status masih pending
     * Endpoint: GET /midtrans/callback/pending
     */
    public function pending(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionId = $request->query('transaction_id');

        Log::channel('midtrans')->info('Callback Pending', [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
        ]);

        return view('midtrans.callback.pending', [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'message' => 'Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi dari bank.',
        ]);
    }
}
