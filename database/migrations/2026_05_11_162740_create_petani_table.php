<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petani', function (Blueprint $table) {
            $table->id('id_petani');
            $table->foreignId('id_kelompok')->constrained('kelompok_tani', 'id_kelompok');
            $table->string('nama_petani', 100);
            $table->decimal('luas_lahan', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petani');
    }
};