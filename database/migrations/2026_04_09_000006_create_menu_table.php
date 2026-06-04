<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('idmenu');
            $table->string('nama_menu', 255);
            $table->integer('harga');
            $table->string('path_gambar', 255)->nullable();
            $table->unsignedInteger('idvendor');
            $table->timestamps();

            $table->foreign('idvendor')->references('idvendor')->on('vendor')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
