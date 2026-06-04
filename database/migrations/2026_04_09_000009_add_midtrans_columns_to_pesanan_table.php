<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('midtrans_order_id', 64)->nullable()->after('status_bayar');
            $table->string('payment_reference', 64)->nullable()->after('midtrans_order_id');
            $table->string('payment_type', 30)->nullable()->after('payment_reference');
            $table->text('payment_payload')->nullable()->after('payment_type');
            $table->timestamp('paid_at')->nullable()->after('payment_payload');

            $table->index('midtrans_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropIndex(['midtrans_order_id']);
            $table->dropColumn([
                'midtrans_order_id',
                'payment_reference',
                'payment_type',
                'payment_payload',
                'paid_at',
            ]);
        });
    }
};
