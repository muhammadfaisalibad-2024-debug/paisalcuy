<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->bigIncrements('idpenjualan_detail');
            $table->unsignedBigInteger('id_penjualan');
            $table->string('id_barang', 15);
            $table->smallInteger('jumlah');
            $table->integer('subtotal');
            $table->timestamps();

            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->cascadeOnDelete();
            $table->index('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('tb_barang')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
    }
};
