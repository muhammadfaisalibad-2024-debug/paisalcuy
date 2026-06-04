<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('idpesanan');
            $table->string('nama', 255);
            $table->timestamp('timestamp');
            $table->integer('total');
            $table->tinyInteger('metode_bayar')->nullable()->comment('1=Virtual Account, 2=QRIS');
            $table->smallInteger('status_bayar')->default(0)->comment('0=Belum Lunas, 1=Lunas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
