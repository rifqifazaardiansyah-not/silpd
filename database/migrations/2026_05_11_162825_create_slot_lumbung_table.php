<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_lumbung', function (Blueprint $table) {
            $table->id('id_slot');
            $table->foreignId('id_lumbung')->constrained('lumbung', 'id_lumbung');
            $table->string('kode_slot', 20);
            $table->decimal('kapasitas', 10, 2);
            $table->decimal('kapasitas_tersedia', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_lumbung');
    }
};