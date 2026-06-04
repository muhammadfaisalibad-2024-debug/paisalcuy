<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('iddetail_pesanan');
            $table->unsignedInteger('idmenu');
            $table->unsignedInteger('idpesanan');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('subtotal');
            $table->timestamp('timestamp');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->foreign('idmenu')->references('idmenu')->on('menu')->cascadeOnUpdate();
            $table->foreign('idpesanan')->references('idpesanan')->on('pesanan')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
