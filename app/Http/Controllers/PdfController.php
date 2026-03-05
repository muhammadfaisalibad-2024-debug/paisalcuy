<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getSertifikatData(): array
    {
        return [
            'nama'          => Auth::user()->name,
            'email'         => Auth::user()->email,
            'tanggal'       => now()->locale('id')->isoFormat('D MMMM Y'),
            'nomor'         => 'SERT-ANGG/' . now()->format('Y') . '/' . str_pad(Auth::id(), 4, '0', STR_PAD_LEFT),
            'totalBuku'     => Buku::count(),
            'totalKategori' => Kategori::count(),
            'bukuTerbaru'   => Buku::with('kategori')->latest('idbuku')->take(5)->get(),
        ];
    }

    private function getUndanganData(): array
    {
        return [
            'nama'          => Auth::user()->name,
            'email'         => Auth::user()->email,
            'tanggal'       => now()->locale('id')->isoFormat('D MMMM Y'),
            'nomor'         => 'UND-KOLK/' . now()->format('Y/m') . '/' . str_pad(Auth::id(), 3, '0', STR_PAD_LEFT),
            'hari'          => now()->locale('id')->isoFormat('dddd'),
            'waktu'         => '09.00 WIB',
            'tempat'        => 'Ruang Baca Utama, Perpustakaan Fakultas Ilmu Komputer',
            'acara'         => 'Peluncuran & Pameran Koleksi Buku Digital 2026',
            'totalBuku'     => Buku::count(),
            'totalKategori' => Kategori::count(),
            'daftarBuku'    => Buku::with('kategori')->latest('idbuku')->take(10)->get(),
            'kategoriList'  => Kategori::withCount('buku')->get(),
        ];
    }

    /** Tampil di browser - Sertifikat (Landscape A4) */
    public function sertifikat()
    {
        $pdf = Pdf::loadView('pdf.sertifikat', $this->getSertifikatData())
                  ->setPaper('a4', 'landscape');
        return $pdf->stream('sertifikat-anggota.pdf');
    }

    /** Download - Sertifikat (Landscape A4) */
    public function downloadSertifikat()
    {
        $pdf = Pdf::loadView('pdf.sertifikat', $this->getSertifikatData())
                  ->setPaper('a4', 'landscape');
        return $pdf->download('sertifikat-anggota-' . Auth::user()->name . '.pdf');
    }

    /** Tampil di browser - Undangan (Portrait A4) */
    public function undangan()
    {
        $pdf = Pdf::loadView('pdf.undangan', $this->getUndanganData())
                  ->setPaper('a4', 'portrait');
        return $pdf->stream('undangan-koleksi-buku.pdf');
    }

    /** Download - Undangan (Portrait A4) */
    public function downloadUndangan()
    {
        $pdf = Pdf::loadView('pdf.undangan', $this->getUndanganData())
                  ->setPaper('a4', 'portrait');
        return $pdf->download('undangan-koleksi-buku-' . Auth::user()->name . '.pdf');
    }
}
