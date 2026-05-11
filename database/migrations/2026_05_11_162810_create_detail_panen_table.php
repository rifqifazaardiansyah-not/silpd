<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_panen', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_panen')->constrained('panen', 'id_panen');
            $table->foreignId('id_jenis_gabah')->constrained('jenis_gabah', 'id_jenis_gabah');
            $table->decimal('jumlah_panen', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_panen');
    }
};