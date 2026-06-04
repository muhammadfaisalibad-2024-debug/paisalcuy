<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor', function (Blueprint $table) {
            $table->increments('idvendor');
            $table->string('nama_vendor', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};
