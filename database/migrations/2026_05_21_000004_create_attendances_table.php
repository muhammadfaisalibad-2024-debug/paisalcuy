<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nfc_card_id')->nullable();
            $table->string('serial')->nullable();
            $table->string('status')->default('present');
            $table->text('note')->nullable();
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();

            $table->foreign('nfc_card_id')->references('id')->on('nfc_cards')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
