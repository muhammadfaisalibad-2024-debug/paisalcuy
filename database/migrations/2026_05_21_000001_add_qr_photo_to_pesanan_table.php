<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('qr_code_path')->nullable()->after('payment_payload');
            $table->string('photo_path')->nullable()->after('qr_code_path');
        });
    }

    public function down()
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['qr_code_path', 'photo_path']);
        });
    }
};
