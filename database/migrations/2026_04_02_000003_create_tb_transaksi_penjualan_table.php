<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->bigIncrements('id_penjualan');
            $table->timestamp('timestamp');
            $table->integer('total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
