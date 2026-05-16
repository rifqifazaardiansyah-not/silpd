<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini melakukan dua hal sekaligus dalam satu transaksi:
 *
 * 1. Menghapus kolom id_pengelola dari tabel lumbung
 *    (relasi 1:N lama dihapus karena digantikan many-to-many)
 *
 * 2. Membuat tabel pivot lumbung_pengelola untuk relasi many-to-many
 *    antara lumbung dan pengelola, dengan atribut tambahan `peran`
 *    yang menandakan peran pengelola di lumbung tersebut.
 *
 * Penamaan tabel pivot mengikuti konvensi Laravel:
 * nama kedua model diurutkan alphabetis → lumbung_pengelola
 */
return new class extends Migration
{
    public function up(): void
    {
        // // =====================================================================
        // // LANGKAH 1 — Hapus kolom id_pengelola dari tabel lumbung
        // // =====================================================================
        // Schema::table('lumbung', function (Blueprint $table) {
        //     // Drop foreign key constraint sebelum drop kolom.
        //     // Nama constraint mengikuti konvensi Laravel:
        //     // {tabel}_{kolom}_foreign → lumbung_id_pengelola_foreign
        //     // Jika nama constraint berbeda di database Anda, sesuaikan baris ini.
        //     if (Schema::hasColumn('lumbung', 'id_pengelola')) {
        //         $table->dropForeign(['id_pengelola']);
        //         $table->dropColumn('id_pengelola');
        //     }
        // });

        // =====================================================================
        // LANGKAH 2 — Buat tabel pivot lumbung_pengelola
        // =====================================================================
        Schema::create('lumbung_pengelola', function (Blueprint $table) {
            // Gunakan composite primary key agar satu pasang
            // (id_lumbung + id_pengelola) tidak bisa duplikat
            $table->id('id_lumbung_pengelola');

            $table->foreignId('id_lumbung')
                ->constrained('lumbung', 'id_lumbung')
                ->onDelete('cascade'); // Hapus relasi jika lumbung dihapus

            $table->foreignId('id_pengelola')
                ->constrained('pengelola', 'id_pengelola')
                ->onDelete('cascade'); // Hapus relasi jika pengelola dihapus

            /**
             * Peran pengelola di lumbung ini:
             *
             * pemilik_akun — pengelola yang bertanggung jawab penuh atas lumbung,
             *                memiliki akun login dan akses penuh ke fitur konfirmasi.
             *                Satu lumbung bisa punya lebih dari satu pemilik_akun.
             *
             * anggota      — pengelola yang ikut mengelola lumbung sebagai asisten,
             *                tetap bisa akses dashboard dan lihat stok.
             */
            $table->enum('peran', ['pemilik_akun', 'anggota'])->default('anggota');

            $table->timestamps();

            // Pastikan satu pengelola tidak bisa punya dua entri untuk lumbung yang sama
            $table->unique(['id_lumbung', 'id_pengelola'], 'unique_lumbung_pengelola');
        });
    }

    public function down(): void
    {
        // Kembalikan ke kondisi semula (rollback)

        // 1. Hapus tabel pivot
        Schema::dropIfExists('lumbung_pengelola');

        // // 2. Kembalikan kolom id_pengelola ke tabel lumbung
        // Schema::table('lumbung', function (Blueprint $table) {
        //     $table->foreignId('id_pengelola')
        //         ->nullable()
        //         ->constrained('pengelola', 'id_pengelola')
        //         ->nullOnDelete();
        // });
    }
};