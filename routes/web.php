<?php

use Illuminate\Support\Facades\Route;

// Public routes
Auth::routes();

// Google OAuth routes
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('google.callback');

// OTP routes
Route::get('otp', [App\Http\Controllers\Auth\OtpController::class, 'show'])->name('otp.show');
Route::post('otp/generate', [App\Http\Controllers\Auth\OtpController::class, 'generate'])->name('otp.generate');
Route::post('otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verify'])->name('otp.verify');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Demo Select & Select2 (Tugas 4) — HARUS di atas Route::resource('kategori')
    // agar tidak ditangkap oleh route kategori/{kategori} (show)
    Route::get('kategori/select-demo', function () {
        return view('kategori.select-demo');
    })->name('kategori.select-demo');

    Route::resource('kategori', App\Http\Controllers\KategoriController::class);
    Route::resource('buku', App\Http\Controllers\BukuController::class);

    // Demo CRUD DataTables (Tugas 2B + 3B) — tampilkan view buku/index-datatables
    Route::get('buku-datatables', function () {
        return view('buku.index-datatables');
    })->name('buku.datatables');

    // PDF routes - tampil di browser
    Route::get('pdf/sertifikat', [App\Http\Controllers\PdfController::class, 'sertifikat'])->name('pdf.sertifikat');
    Route::get('pdf/undangan', [App\Http\Controllers\PdfController::class, 'undangan'])->name('pdf.undangan');

    // PDF routes - download
    Route::get('pdf/sertifikat/download', [App\Http\Controllers\PdfController::class, 'downloadSertifikat'])->name('pdf.sertifikat.download');
    Route::get('pdf/undangan/download', [App\Http\Controllers\PdfController::class, 'downloadUndangan'])->name('pdf.undangan.download');

    // Barang UMKM — custom routes HARUS di atas Route::resource()
    // agar tidak bentrok dengan barang/{barang} (show)
    Route::post('barang/cetak-pdf', [App\Http\Controllers\BarangController::class, 'cetakPdf'])->name('barang.cetak-pdf');
    Route::resource('barang', App\Http\Controllers\BarangController::class);
});
