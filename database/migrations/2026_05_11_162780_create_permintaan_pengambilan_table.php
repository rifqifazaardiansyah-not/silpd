<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_pengambilan', function (Blueprint $table) {
            $table->id('id_permintaan');
            $table->foreignId('id_petani')->constrained('petani', 'id_petani');
            $table->foreignId('id_penyimpanan')->constrained('penyimpanan_gabah', 'id_penyimpanan');
            $table->date('tanggal_permintaan');
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_pengambilan');
    }
};