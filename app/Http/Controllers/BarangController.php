<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Endroid\QrCode\Builder\Builder;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarangController extends Controller
{
    private const QR_SIZE = 600;
    private const QR_MARGIN = 12;

    public function index()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function kasir()
    {
        return view('barang.kasir');
    }

    public function scanner()
    {
        return view('barang.scanner');
    }

    public function findByKode(string $kode)
    {
        $barang = Barang::where('id_barang', $kode)->first();

        if (!$barang) {
            return response()->json([
                'message' => 'Barang dengan kode tersebut tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'harga' => (int) $barang->harga,
                'stok' => (int) $barang->stok,
            ],
        ]);
    }

    public function simpanTransaksi(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|string|max:15',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $items = collect($validated['items']);
        $ids = $items->pluck('id_barang')->unique()->values();

        try {
            $result = DB::transaction(function () use ($items, $ids, $request) {
                $barangs = Barang::whereIn('id_barang', $ids)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_barang');

                if ($barangs->count() !== $ids->count()) {
                    throw ValidationException::withMessages([
                        'items' => ['Terdapat kode barang yang tidak valid.'],
                    ]);
                }

                $total = 0;
                $totalItem = 0;
                $details = [];

                foreach ($items as $item) {
                    $barang = $barangs->get($item['id_barang']);
                    $jumlah = (int) $item['jumlah'];

                    if ($jumlah > (int) $barang->stok) {
                        throw ValidationException::withMessages([
                            'items' => ["Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}."],
                        ]);
                    }

                    $harga = (int) $barang->harga;
                    $subtotal = $harga * $jumlah;

                    $details[] = [
                        'id_barang' => $barang->id_barang,
                        'nama_barang' => $barang->nama_barang,
                        'harga' => $harga,
                        'jumlah' => $jumlah,
                        'subtotal' => $subtotal,
                    ];

                    $total += $subtotal;
                    $totalItem += $jumlah;
                }

                $penjualanId = DB::table('penjualan')->insertGetId([
                    'timestamp' => now(),
                    'total' => $total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_penjualan');

                foreach ($details as $detail) {
                    DB::table('penjualan_detail')->insert([
                        'id_penjualan' => $penjualanId,
                        'id_barang' => $detail['id_barang'],
                        'jumlah' => $detail['jumlah'],
                        'subtotal' => $detail['subtotal'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('tb_barang')
                        ->where('id_barang', $detail['id_barang'])
                        ->decrement('stok', $detail['jumlah']);
                }

                return [
                    'id_penjualan' => $penjualanId,
                    'total' => $total,
                    'total_item' => $totalItem,
                ];
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan transaksi.',
            ], 500);
        }

        return response()->json([
            'message' => 'Pembayaran transaksi berhasil disimpan.',
            'data' => $result,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|max:100',
            'harga'       => 'required|numeric|min:0',
            'satuan'      => 'required|max:30',
            'kategori'    => 'nullable|max:50',
            'deskripsi'   => 'nullable',
            'stok'        => 'required|integer|min:0',
        ]);

        Barang::create([
            'id_barang'   => 'TEMP',   // akan ditimpa trigger trg_id_barang
            'nama_barang' => $request->nama_barang,
            'harga'       => $request->harga,
            'satuan'      => $request->satuan,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'stok'        => $request->input('stok', 0),
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|max:100',
            'harga'       => 'required|numeric|min:0',
            'satuan'      => 'required|max:30',
            'kategori'    => 'nullable|max:50',
            'deskripsi'   => 'nullable',
            'stok'        => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'nama_barang' => $request->nama_barang,
            'harga'       => $request->harga,
            'satuan'      => $request->satuan,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'stok'        => $request->input('stok', 0),
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Barang::findOrFail($id)->delete();
        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Generate dan download PDF tag harga (TnJ No. 108 — 5×8 = 40 label per halaman).
     */
    public function cetakPdf(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'string',
            'x'     => 'required|integer|between:1,5',
            'y'     => 'required|integer|between:1,8',
        ]);

        $items      = Barang::whereIn('id_barang', $request->ids)->orderBy('nama_barang')->get();
        // generate square QR image for each item (base64 PNG)
        // keep barcode fallbacks for compatibility with older views/templates
        $generatorPng = new BarcodeGeneratorPNG();
        $generatorHtml = new BarcodeGeneratorHTML();
        $generatorSvg = new BarcodeGeneratorSVG();
        foreach ($items as $it) {
            try {
                $result = Builder::create()
                    ->data((string) $it->id_barang)
                    ->size(self::QR_SIZE)
                    ->margin(self::QR_MARGIN)
                    ->build();
                $it->qrCodeBase64 = $result->getDataUri();
            } catch (\Throwable $e) {
                $it->qrCodeBase64 = null;
            }
            try {
                $barcodeData = $generatorPng->getBarcode($it->id_barang, $generatorPng::TYPE_CODE_128);
                $it->barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeData);
            } catch (\Throwable $e) {
                $it->barcodeBase64 = null;
            }
            try {
                $it->barcodeHtml = $generatorHtml->getBarcode($it->id_barang, $generatorHtml::TYPE_CODE_128);
            } catch (\Throwable $e) {
                $it->barcodeHtml = null;
            }
            try {
                // generate inline SVG markup (no XML header) for embedding
                $it->barcodeSvg = $generatorSvg->getBarcode($it->id_barang, $generatorSvg::TYPE_CODE_128);
                // prepare embeddable SVG data-uri (base64) for PDF image embedding
                $it->barcodeSvgBase64 = 'data:image/svg+xml;base64,' . base64_encode($it->barcodeSvg);
            } catch (\Throwable $e) {
                $it->barcodeSvg = null;
                $it->barcodeSvgBase64 = null;
            }
        }
        $posisiAwal = ($request->y - 1) * 5 + ($request->x - 1);
        $totalSlot  = 40;
        $slotAwal   = $totalSlot - $posisiAwal;

        $halaman = [];

        $chunk1  = $items->slice(0, $slotAwal)->values();
        $sisanya = $items->slice($slotAwal)->values();

        if ($chunk1->isNotEmpty()) {
            $halaman[] = ['offset' => $posisiAwal, 'items' => $chunk1];
        }

        foreach ($sisanya->chunk(40) as $chunk) {
            $halaman[] = ['offset' => 0, 'items' => $chunk->values()];
        }

        if (empty($halaman)) {
            $halaman[] = ['offset' => 0, 'items' => collect()];
        }

        $pdf = Pdf::loadView('barang.cetak', compact('halaman'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('tag-harga.pdf');
    }

    /**
     * Generate single-item barcode PDF (label) for a barang
     */
    public function labelPdf($id)
    {
        $barang = Barang::findOrFail($id);

        $qrCodeBase64 = null;
        $barcodeBase64 = null;
        $barcodeSvg = null;
        $barcodeHtml = null;

        try {
            $result = Builder::create()
                ->data((string) $barang->id_barang)
                ->size(self::QR_SIZE)
                ->margin(self::QR_MARGIN)
                ->build();
            $qrCodeBase64 = $result->getDataUri();
        } catch (\Throwable $e) {
            $qrCodeBase64 = null;
        }

        // Try PNG (requires GD or Imagick). If unavailable, fall back to SVG/HTML.
        $generatorPng = new BarcodeGeneratorPNG();
        try {
            $barcodeData = $generatorPng->getBarcode($barang->id_barang, $generatorPng::TYPE_CODE_128);
            $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeData);
        } catch (\Throwable $e) {
            $generatorSvg = new BarcodeGeneratorSVG();
            $generatorHtml = new BarcodeGeneratorHTML();
            try { $barcodeSvg = $generatorSvg->getBarcode($barang->id_barang, $generatorSvg::TYPE_CODE_128); } catch (\Throwable $e2) { $barcodeSvg = null; }
            try { $barcodeHtml = $generatorHtml->getBarcode($barang->id_barang, $generatorHtml::TYPE_CODE_128); } catch (\Throwable $e2) { $barcodeHtml = null; }
        }

        $pdf = Pdf::loadView('barang.label', compact('barang', 'qrCodeBase64', 'barcodeBase64', 'barcodeSvg', 'barcodeHtml'));
        return $pdf->stream("label-{$barang->id_barang}.pdf");
    }

    // Debug: show label as HTML (not PDF) so barcode rendering can be inspected in browser
    public function labelHtml($id)
    {
        $barang = Barang::findOrFail($id);
        $qrCodeBase64 = null;
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = null;
        try {
            $barcodeData = $generator->getBarcode($barang->id_barang, $generator::TYPE_CODE_128);
        } catch (\Throwable $e) {
            $barcodeData = null;
        }
        $barcodeBase64 = $barcodeData ? 'data:image/png;base64,' . base64_encode($barcodeData) : null;

        try {
            $result = Builder::create()
                ->data((string) $barang->id_barang)
                ->size(self::QR_SIZE)
                ->margin(self::QR_MARGIN)
                ->build();
            $qrCodeBase64 = $result->getDataUri();
        } catch (\Throwable $e) {
            $qrCodeBase64 = null;
        }

        // also try SVG/HTML fallbacks
        $generatorSvg = new BarcodeGeneratorSVG();
        $generatorHtml = new BarcodeGeneratorHTML();
        try { $barcodeSvg = $generatorSvg->getBarcode($barang->id_barang, $generatorSvg::TYPE_CODE_128); } catch (\Throwable $e) { $barcodeSvg = null; }
        try { $barcodeHtml = $generatorHtml->getBarcode($barang->id_barang, $generatorHtml::TYPE_CODE_128); } catch (\Throwable $e) { $barcodeHtml = null; }

        return view('barang.label', compact('barang','qrCodeBase64','barcodeBase64','barcodeSvg','barcodeHtml'));
    }

    // Simple GET endpoint to print large barcode/QR for given ids via querystring
    public function cetakBarcode(Request $request)
    {
        $ids = $request->query('ids');
        if (!$ids) {
            abort(400, 'Missing ids query parameter, e.g. ?ids[]=BRG-260305-001');
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $items = Barang::whereIn('id_barang', $ids)->orderBy('nama_barang')->get();

        foreach ($items as $it) {
            try {
                $result = Builder::create()
                    ->data((string) $it->id_barang)
                    ->size(self::QR_SIZE)
                    ->margin(self::QR_MARGIN)
                    ->build();
                $it->qrCodeBase64 = $result->getDataUri();
            } catch (\Throwable $e) {
                $it->qrCodeBase64 = null;
            }
        }

        $pdf = Pdf::loadView('barang.cetak_barcode', compact('items'));
        return $pdf->stream('cetak-barcode.pdf');
    }
}
