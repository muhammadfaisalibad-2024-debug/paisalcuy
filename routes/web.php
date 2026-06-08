<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\AntrianController;

Auth::routes();


Route::get('auth/google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('google.callback');


Route::get('otp', [App\Http\Controllers\Auth\OtpController::class, 'show'])->name('otp.show');
Route::post('otp/generate', [App\Http\Controllers\Auth\OtpController::class, 'generate'])->name('otp.generate');
Route::post('otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verify'])->name('otp.verify');

// Customer kantin (tanpa login)
Route::get('kantin/customer', [App\Http\Controllers\KantinCustomerController::class, 'index'])->name('kantin.customer');
Route::get('kantin/api/vendors', [App\Http\Controllers\KantinCustomerController::class, 'vendors'])->name('kantin.api.vendors');
Route::get('kantin/api/vendors/{idvendor}/menus', [App\Http\Controllers\KantinCustomerController::class, 'menusByVendor'])->name('kantin.api.menus-by-vendor');
Route::post('kantin/api/pesanan', [App\Http\Controllers\KantinCustomerController::class, 'simpanPesanan'])->name('kantin.api.pesanan');
Route::post('kantin/api/pesanan/{idpesanan}/bayar', [App\Http\Controllers\KantinCustomerController::class, 'bayar'])->name('kantin.api.bayar');
Route::get('kantin/api/pesanan/{idpesanan}/status-bayar', [App\Http\Controllers\KantinCustomerController::class, 'cekStatusBayar'])->name('kantin.api.status-bayar');
Route::post('kantin/api/pesanan/{idpesanan}/simulasi-lunas', [App\Http\Controllers\KantinCustomerController::class, 'simulasiLunas'])->name('kantin.api.simulasi-lunas');
Route::get('kantin/camera', [App\Http\Controllers\KantinCustomerController::class, 'cameraView'])->name('kantin.camera');
Route::post('kantin/api/photo/upload', [App\Http\Controllers\KantinCustomerController::class, 'uploadPhoto'])->name('kantin.api.photo.upload');
// Web NFC (Module 11)
Route::get('nfc/scanner', [App\Http\Controllers\NfcController::class, 'scannerView'])->name('nfc.scanner');
Route::post('nfc/cards/register', [App\Http\Controllers\NfcController::class, 'registerCard'])->name('nfc.cards.register');
Route::post('nfc/attendance', [App\Http\Controllers\NfcController::class, 'recordAttendance'])->name('nfc.attendance.record');
Route::get('nfc/attendance', [App\Http\Controllers\NfcController::class, 'attendanceList'])->name('nfc.attendance.list');
Route::get('kantin/pesanan/{idpesanan}/qrcode', [App\Http\Controllers\KantinCustomerController::class, 'showQrCode'])->name('kantin.pesanan.qrcode');

// Midtrans Snap (Payment Gateway)
Route::post('kantin/snap/token/{idpesanan}', [App\Http\Controllers\MidtransSnapController::class, 'generateToken'])->name('kantin.snap.token');
Route::get('kantin/snap/status/{idpesanan}', [App\Http\Controllers\MidtransSnapController::class, 'checkTransactionStatus'])->name('kantin.snap.status');

// Midtrans Callbacks (setelah Snap popup ditutup)
Route::get('midtrans/callback/finish', [App\Http\Controllers\MidtransCallbackController::class, 'finish'])->name('kantin.snap.callback.finish');
Route::get('midtrans/callback/error', [App\Http\Controllers\MidtransCallbackController::class, 'error'])->name('kantin.snap.callback.erro                                                                                                                                   r');
Route::get('midtrans/callback/pending', [App\Http\Controllers\MidtransCallbackController::class, 'pending'])->name('kantin.snap.callback.pending');

// Midtrans Webhook Notification (dari server Midtrans)
Route::post('midtrans/notification', [App\Http\Controllers\MidtransNotificationController::class, 'handleNotification'])->name('midtrans.notification');
Route::get(
    '/guest',
    [AntrianController::class,'guest']
)->name('antrian.guest');

Route::post(
    '/guest',
    [AntrianController::class,'daftar']
)->name('antrian.daftar');

Route::get(
    '/papan',
    [AntrianController::class,'papan']
)->name('antrian.papan');

Route::get(
    '/sse/antrian',
    [AntrianController::class,'stream']
)->name('antrian.stream');
Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    
    
    Route::get('kategori/select-demo', function () {
        return view('kategori.select-demo');
    })->name('kategori.select-demo');

    Route::resource('kategori', App\Http\Controllers\KategoriController::class);
    Route::resource('buku', App\Http\Controllers\BukuController::class);

    
    Route::get('buku-datatables', function () {
        return view('buku.index-datatables');
    })->name('buku.datatables');

    
    Route::get('pdf/sertifikat', [App\Http\Controllers\PdfController::class, 'sertifikat'])->name('pdf.sertifikat');
    Route::get('pdf/undangan', [App\Http\Controllers\PdfController::class, 'undangan'])->name('pdf.undangan');

    
    Route::get('pdf/sertifikat/download', [App\Http\Controllers\PdfController::class, 'downloadSertifikat'])->name('pdf.sertifikat.download');
    Route::get('pdf/undangan/download', [App\Http\Controllers\PdfController::class, 'downloadUndangan'])->name('pdf.undangan.download');

    
    
    Route::get('barang/kasir', [App\Http\Controllers\BarangController::class, 'kasir'])->name('barang.kasir');
    Route::get('barang/scanner', [App\Http\Controllers\BarangController::class, 'scanner'])->name('barang.scanner');
    Route::get('barang/api/{kode}', [App\Http\Controllers\BarangController::class, 'findByKode'])->name('barang.api.find');
    Route::post('barang/transaksi', [App\Http\Controllers\BarangController::class, 'simpanTransaksi'])->name('barang.transaksi.store');
    Route::post('barang/cetak-pdf', [App\Http\Controllers\BarangController::class, 'cetakPdf'])->name('barang.cetak-pdf');
    Route::get('barang/cetak-barcode', [App\Http\Controllers\BarangController::class, 'cetakBarcode'])->name('barang.cetak-barcode');
    Route::resource('barang', App\Http\Controllers\BarangController::class);
    Route::get('barang/{id}/label', [App\Http\Controllers\BarangController::class, 'labelPdf'])->name('barang.label');
    Route::get('barang/{id}/label-html', [App\Http\Controllers\BarangController::class, 'labelHtml'])->name('barang.label.html');

    // Vendor kantin (perlu login)
    Route::get('kantin/vendor/menu', [App\Http\Controllers\KantinVendorController::class, 'menuIndex'])->name('kantin.vendor.menu');
    Route::post('kantin/vendor/vendor-store', [App\Http\Controllers\KantinVendorController::class, 'storeVendor'])->name('kantin.vendor.store-vendor');
    Route::post('kantin/vendor/menu-store', [App\Http\Controllers\KantinVendorController::class, 'storeMenu'])->name('kantin.vendor.store-menu');
    Route::get('kantin/vendor/pesanan-lunas', [App\Http\Controllers\KantinVendorController::class, 'pesananLunas'])->name('kantin.vendor.pesanan-lunas');
    Route::get('kantin/vendor/scan-qr', [App\Http\Controllers\KantinVendorController::class, 'scanQrView'])->name('kantin.vendor.scan-qr');
    Route::get('kantin/vendor/api/pesanan/{idpesanan}', [App\Http\Controllers\KantinVendorController::class, 'scanPesanan'])->name('kantin.vendor.api.pesanan');
    Route::get(
    '/admin-antrian',
    [AntrianController::class,'admin']
    )->name('antrian.admin');

    Route::post(
        '/antrian/panggil',
        [AntrianController::class,'panggil']
    )->name('antrian.panggil');
    Route::get('/toko/{id}/qr',
        [TokoController::class,'qr']
        )->name('toko.qr');
    Route::resource('toko', TokoController::class);

    Route::get('/kunjungan-toko',
        [KunjunganController::class,'index'])
        ->name('kunjungan.index');

    Route::post('/kunjungan-toko',
        [KunjunganController::class,'store'])
        ->name('kunjungan.store');
});
