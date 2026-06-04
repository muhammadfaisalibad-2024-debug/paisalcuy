<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\MenuKantin;
use App\Models\Pesanan;
use App\Models\VendorKantin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;

class KantinCustomerController extends Controller
{
    public function index()
    {
        $vendors = VendorKantin::orderBy('nama_vendor')->get();

        return view('kantin.customer', compact('vendors'));
    }

    public function vendors()
    {
        return response()->json([
            'data' => VendorKantin::orderBy('nama_vendor')->get(),
        ]);
    }

    public function menusByVendor(int $idvendor)
    {
        $menus = MenuKantin::where('idvendor', $idvendor)->orderBy('nama_menu')->get();

        return response()->json([
            'data' => $menus,
        ]);
    }

    public function simpanPesanan(Request $request)
    {
        $validated = $request->validate([
            'nama_customer' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.idmenu' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        $items = collect($validated['items']);
        $menuIds = $items->pluck('idmenu')->unique()->values();

        $result = DB::transaction(function () use ($validated, $items, $menuIds) {
            $menus = MenuKantin::whereIn('idmenu', $menuIds)->get()->keyBy('idmenu');

            if ($menus->count() !== $menuIds->count()) {
                throw ValidationException::withMessages([
                    'items' => ['Ada menu yang tidak valid.'],
                ]);
            }

            $namaCustomer = trim((string) ($validated['nama_customer'] ?? ''));
            if ($namaCustomer === '') {
                $namaCustomer = $this->generateGuestName();
            }

            $total = 0;
            $detailRows = [];

            foreach ($items as $item) {
                $menu = $menus->get($item['idmenu']);
                $jumlah = (int) $item['jumlah'];
                $harga = (int) $menu->harga;
                $subtotal = $harga * $jumlah;

                $detailRows[] = [
                    'idmenu' => $menu->idmenu,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'timestamp' => now(),
                    'catatan' => $item['catatan'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $total += $subtotal;
            }

            $pesanan = Pesanan::create([
                'nama' => $namaCustomer,
                'timestamp' => now(),
                'total' => $total,
                'metode_bayar' => null,
                'status_bayar' => 0,
            ]);

            foreach ($detailRows as $detail) {
                DetailPesanan::create(array_merge($detail, [
                    'idpesanan' => $pesanan->idpesanan,
                ]));
            }

            return [
                'idpesanan' => $pesanan->idpesanan,
                'nama_customer' => $pesanan->nama,
                'total' => $total,
            ];
        });

        return response()->json([
            'message' => 'Pesanan berhasil dibuat. Silakan pilih metode pembayaran.',
            'data' => $result,
        ]);
    }

    public function bayar(Request $request, int $idpesanan)
    {
        $validated = $request->validate([
            'metode_bayar' => 'required|in:1,2',
        ]);

        $pesanan = Pesanan::findOrFail($idpesanan);

        if ((int) $pesanan->status_bayar === 1) {
            return response()->json([
                'message' => 'Pesanan ini sudah lunas.',
                'data' => [
                    'status_bayar' => 1,
                    'transaction_status' => 'settlement',
                ],
            ], 422);
        }

        $serverKey = (string) config('services.midtrans.server_key');
        if ($serverKey === '') {
            return response()->json([
                'message' => 'MIDTRANS_SERVER_KEY belum diatur di file .env',
            ], 422);
        }

        if ($pesanan->midtrans_order_id) {
            return $this->cekStatusBayar($idpesanan);
        }

        $orderId = 'KNT-' . $pesanan->idpesanan . '-' . now()->format('YmdHis');
        $metode = (int) $validated['metode_bayar'];

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama,
            ],
        ];

        if ($metode === 1) {
            $payload['payment_type'] = 'bank_transfer';
            $payload['bank_transfer'] = [
                'bank' => config('services.midtrans.bank_va', 'bca'),
            ];
        } else {
            $payload['payment_type'] = 'qris';
            $payload['qris'] = [
                'acquirer' => config('services.midtrans.qris_acquirer', 'gopay'),
            ];
        }

        $charge = $this->midtransRequest('POST', '/v2/charge', $payload);

        if (!$charge->successful()) {
            return response()->json([
                'message' => data_get($charge->json(), 'status_message', 'Gagal membuat transaksi Midtrans.'),
                'midtrans' => $charge->json(),
            ], 422);
        }

        $chargeData = $charge->json();

        $pesanan->update([
            'metode_bayar' => $metode,
            'midtrans_order_id' => $orderId,
            'payment_reference' => data_get($chargeData, 'transaction_id'),
            'payment_type' => data_get($chargeData, 'payment_type'),
            'payment_payload' => json_encode($chargeData),
        ]);

        $statusRaw = data_get($chargeData, 'transaction_status', 'pending');
        $isPaid = in_array($statusRaw, ['settlement', 'capture'], true);

        if ($isPaid) {
            $this->ensureQrCode($pesanan);
            $pesanan->update([
                'status_bayar' => 1,
                'paid_at' => now(),
            ]);
        }

        return response()->json([
            'message' => $isPaid
                ? 'Pembayaran berhasil. Status pesanan menjadi lunas.'
                : 'Transaksi Midtrans berhasil dibuat. Silakan selesaikan pembayaran lalu cek status.',
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'status_bayar' => $isPaid ? 1 : 0,
                'transaction_status' => $statusRaw,
                'midtrans_order_id' => $orderId,
                'payment_type' => data_get($chargeData, 'payment_type'),
                'va_number' => data_get($chargeData, 'va_numbers.0.va_number'),
                'qris_url' => collect(data_get($chargeData, 'actions', []))
                    ->firstWhere('name', 'generate-qr-code')['url'] ?? null,
                'qr_code_url' => $pesanan->qr_code_path ? Storage::disk('public')->url($pesanan->qr_code_path) : null,
            ],
        ]);
    }

    public function simulasiLunas(Request $request, int $idpesanan)
    {
        $validated = $request->validate([
            'metode_bayar' => 'required|in:1,2',
            'rekening_sumber' => 'required|string|min:6|max:30',
            'nama_pengirim' => 'required|string|min:3|max:100',
            'nominal_bayar' => 'required|integer|min:1',
            'kode_otorisasi' => 'nullable|string|max:30',
        ]);

        $pesanan = Pesanan::findOrFail($idpesanan);

        if ((bool) config('services.midtrans.is_production', false)) {
            return response()->json([
                'message' => 'Simulasi hanya boleh digunakan pada mode sandbox.',
            ], 422);
        }

        if ((int) $pesanan->status_bayar === 1) {
            return response()->json([
                'message' => 'Pesanan ini sudah lunas.',
            ], 422);
        }

        if (!$pesanan->midtrans_order_id) {
            return response()->json([
                'message' => 'Belum ada transaksi Midtrans untuk pesanan ini.',
            ], 422);
        }

        if ((int) $validated['nominal_bayar'] !== (int) $pesanan->total) {
            return response()->json([
                'message' => 'Nominal bayar harus sama dengan total pesanan (Rp ' . number_format($pesanan->total, 0, ',', '.') . ').',
            ], 422);
        }

        $payloadLama = json_decode((string) $pesanan->payment_payload, true);
        if (!is_array($payloadLama)) {
            $payloadLama = [];
        }

        $simPayload = [
            'simulated_payment' => [
                'metode_bayar' => (int) $validated['metode_bayar'],
                'rekening_sumber' => $validated['rekening_sumber'],
                'nama_pengirim' => $validated['nama_pengirim'],
                'nominal_bayar' => (int) $validated['nominal_bayar'],
                'kode_otorisasi' => $validated['kode_otorisasi'] ?? null,
                'dibayar_pada' => now()->toDateTimeString(),
            ],
        ];

        $pesanan->update([
            'metode_bayar' => (int) $validated['metode_bayar'],
            'status_bayar' => 1,
            'payment_reference' => $validated['kode_otorisasi'] ?? ('SIM-' . now()->format('YmdHis')),
            'payment_type' => (int) $validated['metode_bayar'] === 1 ? 'simulated-bank-transfer' : 'simulated-qris',
            'payment_payload' => json_encode(array_merge($payloadLama, $simPayload)),
            'paid_at' => now(),
        ]);

        $this->ensureQrCode($pesanan);

        return response()->json([
            'message' => 'Simulasi lunas berhasil untuk kebutuhan tugas. Catatan: status di dashboard Midtrans bisa tetap pending karena ini simulasi lokal.',
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'status_bayar' => 1,
                'transaction_status' => 'simulated-settlement',
                'nominal_bayar' => (int) $validated['nominal_bayar'],
                'nama_pengirim' => $validated['nama_pengirim'],
                'qr_code_url' => $pesanan->qr_code_path ? Storage::disk('public')->url($pesanan->qr_code_path) : null,
            ],
        ]);
    }

    public function cekStatusBayar(int $idpesanan)
    {
        $pesanan = Pesanan::findOrFail($idpesanan);

        if (!$pesanan->midtrans_order_id) {
            return response()->json([
                'message' => 'Belum ada transaksi Midtrans untuk pesanan ini.',
            ], 422);
        }

        $status = $this->midtransRequest('GET', '/v2/' . $pesanan->midtrans_order_id . '/status');

        if (!$status->successful()) {
            return response()->json([
                'message' => data_get($status->json(), 'status_message', 'Gagal mengambil status pembayaran Midtrans.'),
                'midtrans' => $status->json(),
            ], 422);
        }

        $statusData = $status->json();
        $statusRaw = data_get($statusData, 'transaction_status', 'pending');
        $isPaid = in_array($statusRaw, ['settlement', 'capture'], true);

        $pesanan->update([
            'payment_payload' => json_encode($statusData),
            'payment_reference' => data_get($statusData, 'transaction_id', $pesanan->payment_reference),
            'payment_type' => data_get($statusData, 'payment_type', $pesanan->payment_type),
            'status_bayar' => $isPaid ? 1 : 0,
            'paid_at' => $isPaid ? now() : null,
        ]);

        if ($isPaid) {
            $this->ensureQrCode($pesanan);
        }

        return response()->json([
            'message' => $isPaid ? 'Pembayaran sudah lunas.' : 'Pembayaran belum lunas, silakan selesaikan terlebih dahulu.',
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'status_bayar' => $isPaid ? 1 : 0,
                'transaction_status' => $statusRaw,
                'midtrans_order_id' => $pesanan->midtrans_order_id,
                'payment_type' => data_get($statusData, 'payment_type', $pesanan->payment_type),
                'va_number' => data_get($statusData, 'va_numbers.0.va_number'),
                'qris_url' => collect(data_get($statusData, 'actions', []))
                    ->firstWhere('name', 'generate-qr-code')['url'] ?? null,
                'qr_code_url' => $pesanan->qr_code_path ? Storage::disk('public')->url($pesanan->qr_code_path) : null,
            ],
        ]);
    }

    public function showQrCode(int $idpesanan)
    {
        $pesanan = Pesanan::with('detailPesanan.menu')->findOrFail($idpesanan);

        if (!$pesanan->qr_code_path) {
            $this->ensureQrCode($pesanan);
            $pesanan->refresh();
        }

        $qrCodeUrl = $pesanan->qr_code_path ? Storage::disk('public')->url($pesanan->qr_code_path) : null;

        return view('kantin.qr-code', compact('pesanan', 'qrCodeUrl'));
    }

    /**
     * Show camera capture UI for customers (public)
     */
    public function cameraView()
    {
        return view('kantin.camera');
    }

    /**
     * Upload base64 image from camera and optionally attach to pesanan
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'idpesanan' => 'nullable|integer',
        ]);

        $data = $request->input('image');
        if (!preg_match('/^data:image\/([a-zA-Z]+);base64,/', $data, $m)) {
            return response()->json(['message' => 'Invalid image format.'], 422);
        }

        $type = strtolower($m[1]);
        $allowed = ['png','jpg','jpeg'];
        if (!in_array($type, $allowed, true)) {
            return response()->json(['message' => 'Unsupported image type.'], 422);
        }

        $data = substr($data, strpos($data, ',') + 1);
        $decoded = base64_decode($data);
        if ($decoded === false) {
            return response()->json(['message' => 'Base64 decode failed.'], 422);
        }

        $filename = 'photos/' . uniqid('cam_') . '.' . ($type === 'jpeg' ? 'jpg' : $type);
        Storage::disk('public')->put($filename, $decoded);
        $url = Storage::url($filename);

        // Optionally attach to pesanan
        if ($request->filled('idpesanan')) {
            $pesanan = Pesanan::find($request->input('idpesanan'));
            if ($pesanan) {
                $pesanan->photo_path = $filename;
                $pesanan->save();
            }
        }

        return response()->json(['success' => true, 'path' => $filename, 'url' => $url]);
    }

    private function generateGuestName(): string
    {
        $lastGuest = Pesanan::where('nama', 'like', 'Guest_%')
            ->orderByDesc('idpesanan')
            ->value('nama');

        if (!$lastGuest) {
            return 'Guest_000001';
        }

        $lastNumber = (int) str_replace('Guest_', '', $lastGuest);
        $nextNumber = $lastNumber + 1;

        return 'Guest_' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

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

    private function ensureQrCode(Pesanan $pesanan): void
    {
        if ($pesanan->qr_code_path) {
            return;
        }

        $result = Builder::create()
            ->data((string) $pesanan->idpesanan)
            ->size(420)
            ->margin(10)
            ->build();

        $filename = 'qrcodes/pesanan-' . $pesanan->idpesanan . '.png';
        Storage::disk('public')->put($filename, $result->getString());

        $pesanan->update(['qr_code_path' => $filename]);
    }
}
