<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add id_pengelola foreign key ke tabel lumbung.
     * Migration ini diperlukan untuk mendukung relasi Pengelola -> Lumbung
     */
    public function up(): void
    {
        Schema::table('lumbung', function (Blueprint $table) {
            $table->foreignId('id_pengelola')
                ->nullable()
                ->constrained('pengelola', 'id_pengelola')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('lumbung', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['id_pengelola_foreign']);
            $table->dropColumn('id_pengelola');
        });
    }
};