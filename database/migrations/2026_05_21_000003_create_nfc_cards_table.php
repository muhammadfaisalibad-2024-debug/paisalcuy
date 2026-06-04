<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nfc_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial')->unique();
            $table->string('owner_name')->nullable();
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfc_cards');
    }
};
