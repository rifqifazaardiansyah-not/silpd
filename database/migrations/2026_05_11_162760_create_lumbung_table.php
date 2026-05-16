<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lumbung', function (Blueprint $table) {
            $table->id('id_lumbung');
            $table->string('nama_lumbung', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lumbung');
    }
};