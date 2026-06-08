<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tokos', function (Blueprint $table) {
        $table->id();
        $table->string('barcode')->unique();
        $table->string('nama_toko');
        $table->text('alamat')->nullable();
        $table->double('latitude',15,8);
        $table->double('longitude',15,8);
        $table->double('accuracy')->default(0);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokos');
    }
};
