<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->string('id_barang', 15)->change(); // BRG-YYMMDD-NNN = 14 chars
        });
    }

    public function down(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->string('id_barang', 12)->change();
        });
    }
};
