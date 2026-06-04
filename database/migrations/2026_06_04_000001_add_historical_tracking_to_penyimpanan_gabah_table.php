<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan tracking historical untuk penyimpanan gabah:
     * - id_instruksi: Link ke instruksi_penyimpanan (trace asal gabah)
     * - jumlah_masuk: Jumlah original saat pertama masuk (tidak berubah)
     * - jumlah: Tetap digunakan untuk stok real-time (berubah saat ada pengambilan)
     */
    public function up(): void
    {
        Schema::table('penyimpanan_gabah', function (Blueprint $table) {
            // Link ke instruksi penyimpanan
            $table->unsignedBigInteger('id_instruksi')->nullable()->after('id_detail');
            $table->foreign('id_instruksi')
                  ->references('id_instruksi')
                  ->on('instruksi_penyimpanan')
                  ->onDelete('set null');
            
            // Jumlah original saat pertama masuk (historical, tidak berubah)
            $table->decimal('jumlah_masuk', 10, 2)->nullable()->after('id_instruksi');
        });

        // Migrate existing data: set jumlah_masuk = jumlah untuk data yang sudah ada
        DB::table('penyimpanan_gabah')
            ->whereNull('jumlah_masuk')
            ->update(['jumlah_masuk' => DB::raw('jumlah')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyimpanan_gabah', function (Blueprint $table) {
            $table->dropForeign(['id_instruksi']);
            $table->dropColumn(['id_instruksi', 'jumlah_masuk']);
        });
    }
};
