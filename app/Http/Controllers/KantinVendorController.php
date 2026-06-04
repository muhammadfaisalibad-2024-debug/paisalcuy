<?php

namespace App\Http\Controllers;

use App\Models\MenuKantin;
use App\Models\Pesanan;
use App\Models\VendorKantin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KantinVendorController extends Controller
{
    public function menuIndex()
    {
        $vendors = VendorKantin::orderBy('nama_vendor')->get();
        $menus = MenuKantin::query()
            ->join('vendor', 'menu.idvendor', '=', 'vendor.idvendor')
            ->select('menu.*', 'vendor.nama_vendor')
            ->orderBy('vendor.nama_vendor')
            ->orderBy('menu.nama_menu')
            ->get();

        return view('kantin.vendor-menu', compact('vendors', 'menus'));
    }

    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
        ]);

        VendorKantin::create($validated);

        return redirect()->route('kantin.vendor.menu')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'idvendor' => 'required|integer|exists:vendor,idvendor',
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|integer|min:1',
            'path_gambar' => 'nullable|string|max:255',
        ]);

        MenuKantin::create($validated);

        return redirect()->route('kantin.vendor.menu')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function pesananLunas()
    {
        $orders = DB::table('pesanan')
            ->where('status_bayar', 1)
            ->orderByDesc('idpesanan')
            ->get();

        $details = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.idmenu', '=', 'menu.idmenu')
            ->join('vendor', 'menu.idvendor', '=', 'vendor.idvendor')
            ->select(
                'detail_pesanan.idpesanan',
                'menu.nama_menu',
                'vendor.nama_vendor',
                'detail_pesanan.jumlah',
                'detail_pesanan.harga',
                'detail_pesanan.subtotal',
                'detail_pesanan.catatan'
            )
            ->orderByDesc('detail_pesanan.iddetail_pesanan')
            ->get()
            ->groupBy('idpesanan');

        return view('kantin.vendor-paid-orders', compact('orders', 'details'));
    }

    public function scanQrView()
    {
        return view('kantin.vendor-scan-qr');
    }

    public function scanPesanan(int $idpesanan)
    {
        $pesanan = Pesanan::with(['detailPesanan.menu'])->find($idpesanan);

        if (!$pesanan) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $items = $pesanan->detailPesanan->map(function ($detail) {
            return [
                'nama_menu' => $detail->menu->nama_menu ?? '-',
                'jumlah' => (int) $detail->jumlah,
                'harga' => (int) $detail->harga,
                'subtotal' => (int) $detail->subtotal,
                'catatan' => $detail->catatan,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'nama_customer' => $pesanan->nama,
                'status_bayar' => (int) $pesanan->status_bayar,
                'status_bayar_label' => (int) $pesanan->status_bayar === 1 ? 'Lunas' : 'Belum Lunas',
                'total' => (int) $pesanan->total,
                'paid_at' => optional($pesanan->paid_at)->toDateTimeString(),
                'qr_code_url' => $pesanan->qr_code_path ? asset('storage/' . $pesanan->qr_code_path) : null,
                'items' => $items,
            ],
        ]);
    }
}
