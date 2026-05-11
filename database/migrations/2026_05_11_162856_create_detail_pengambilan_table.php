<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pengambilan', function (Blueprint $table) {
            $table->id('id_detail_ambil');
            $table->foreignId('id_permintaan')->constrained('permintaan_pengambilan', 'id_permintaan');
            $table->foreignId('id_penyimpanan')->constrained('penyimpanan_gabah', 'id_penyimpanan');
            $table->decimal('jumlah', 10, 2);
            $table->text('alasan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengambilan');
    }
};