<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instruksi_penyimpanan', function (Blueprint $table) {
            $table->id('id_instruksi');
            $table->foreignId('id_detail')->constrained('detail_panen', 'id_detail');
            $table->foreignId('id_slot')->constrained('slot_lumbung', 'id_slot');
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal_instruksi');
            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instruksi_penyimpanan');
    }
};