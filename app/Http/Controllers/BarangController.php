<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
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
}
