<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_barang', function (Blueprint $table) {
            $table->string('id_barang', 15)->primary();  // BRG-YYMMDD-NNN = 14 chars
            $table->string('nama_barang', 100);
            $table->decimal('harga', 15, 0);
            $table->string('satuan', 30)->default('pcs');
            $table->string('kategori', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->timestamps();
        });

        // Trigger: auto-generate id_barang
        // Format: BRG-YYMMDD-NNN (contoh: BRG-260305-001)
        // Prefix length = 11 chars -> sequence starts at position 12
        DB::unprepared("
            CREATE TRIGGER trg_id_barang
            BEFORE INSERT ON tb_barang
            FOR EACH ROW
            BEGIN
                DECLARE max_seq INT DEFAULT 0;
                DECLARE today_prefix VARCHAR(12);
                SET today_prefix = CONCAT('BRG-', DATE_FORMAT(NOW(), '%y%m%d'), '-');
                SELECT COALESCE(MAX(CAST(SUBSTRING(id_barang, 12) AS UNSIGNED)), 0)
                INTO max_seq
                FROM tb_barang
                WHERE id_barang LIKE CONCAT(today_prefix, '%') COLLATE utf8mb4_unicode_ci;
                SET NEW.id_barang = CONCAT(today_prefix, LPAD(max_seq + 1, 3, '0'));
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_id_barang');
        Schema::dropIfExists('tb_barang');
    }
};
