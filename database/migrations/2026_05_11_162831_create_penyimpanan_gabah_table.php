<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyimpanan_gabah', function (Blueprint $table) {
            $table->id('id_penyimpanan');
            $table->foreignId('id_detail')->constrained('detail_panen', 'id_detail');
            $table->foreignId('id_slot')->constrained('slot_lumbung', 'id_slot');
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal_masuk');
            $table->enum('status', ['tersimpan', 'diambil', 'habis'])->default('tersimpan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyimpanan_gabah');
    }
};