<?php

use Illuminate\Support\Facades\Route;

// Public routes
Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    Route::resource('kategori', App\Http\Controllers\KategoriController::class);
    
    Route::resource('buku', App\Http\Controllers\BukuController::class);
});
