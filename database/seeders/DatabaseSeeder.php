<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kelompok Tani
        DB::table('kelompok_tani')->insert([
            ['id_kelompok' => 1, 'nama_kelompok' => 'Tani Makmur', 'created_at' => now(), 'updated_at' => now()],
            ['id_kelompok' => 2, 'nama_kelompok' => 'Gabah Sejahtera', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Petani
        DB::table('petani')->insert([
            ['id_petani' => 1, 'id_kelompok' => 1, 'nama_petani' => 'Slamet Riyadi', 'luas_lahan' => 2.5, 'created_at' => now(), 'updated_at' => now()],
            ['id_petani' => 2, 'id_kelompok' => 1, 'nama_petani' => 'Nurhayati', 'luas_lahan' => 1.8, 'created_at' => now(), 'updated_at' => now()],
            ['id_petani' => 3, 'id_kelompok' => 2, 'nama_petani' => 'Bambang Suprapto', 'luas_lahan' => 3.2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Panen
        DB::table('panen')->insert([
            ['id_panen' => 1, 'id_petani' => 1, 'tanggal_panen' => '2025-03-10', 'created_at' => now(), 'updated_at' => now()],
            ['id_panen' => 2, 'id_petani' => 2, 'tanggal_panen' => '2025-03-12', 'created_at' => now(), 'updated_at' => now()],
            ['id_panen' => 3, 'id_petani' => 3, 'tanggal_panen' => '2025-03-15', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Jenis Gabah
        DB::table('jenis_gabah')->insert([
            ['id_jenis_gabah' => 1, 'nama_jenis' => 'IR64', 'created_at' => now(), 'updated_at' => now()],
            ['id_jenis_gabah' => 2, 'nama_jenis' => 'Ciherang', 'created_at' => now(), 'updated_at' => now()],
            ['id_jenis_gabah' => 3, 'nama_jenis' => 'Mekongga', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 5. Detail Panen
        DB::table('detail_panen')->insert([
            ['id_detail' => 1, 'id_panen' => 1, 'id_jenis_gabah' => 1, 'jumlah_panen' => 800, 'created_at' => now(), 'updated_at' => now()],
            ['id_detail' => 2, 'id_panen' => 1, 'id_jenis_gabah' => 2, 'jumlah_panen' => 450, 'created_at' => now(), 'updated_at' => now()],
            ['id_detail' => 3, 'id_panen' => 2, 'id_jenis_gabah' => 2, 'jumlah_panen' => 600, 'created_at' => now(), 'updated_at' => now()],
            ['id_detail' => 4, 'id_panen' => 3, 'id_jenis_gabah' => 3, 'jumlah_panen' => 1000, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. Lumbung
        DB::table('lumbung')->insert([
            ['id_lumbung' => 1, 'nama_lumbung' => 'Lumbung Desa A', 'created_at' => now(), 'updated_at' => now()],
            ['id_lumbung' => 2, 'nama_lumbung' => 'Lumbung Pusat', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7. Slot Lumbung
        DB::table('slot_lumbung')->insert([
            ['id_slot' => 1, 'id_lumbung' => 1, 'kode_slot' => 'A1', 'kapasitas' => 2000, 'kapasitas_tersedia' => 1200, 'created_at' => now(), 'updated_at' => now()],
            ['id_slot' => 2, 'id_lumbung' => 1, 'kode_slot' => 'A2', 'kapasitas' => 1500, 'kapasitas_tersedia' => 1500, 'created_at' => now(), 'updated_at' => now()],
            ['id_slot' => 3, 'id_lumbung' => 2, 'kode_slot' => 'B1', 'kapasitas' => 5000, 'kapasitas_tersedia' => 3500, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 8. Penyimpanan Gabah
        DB::table('penyimpanan_gabah')->insert([
            ['id_penyimpanan' => 1, 'id_detail' => 1, 'id_slot' => 1, 'jumlah' => 800, 'tanggal_masuk' => '2025-03-11', 'status' => 'tersimpan', 'created_at' => now(), 'updated_at' => now()],
            ['id_penyimpanan' => 2, 'id_detail' => 3, 'id_slot' => 3, 'jumlah' => 600, 'tanggal_masuk' => '2025-03-13', 'status' => 'tersimpan', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 9. Instruksi Penyimpanan
        DB::table('instruksi_penyimpanan')->insert([
            ['id_instruksi' => 1, 'id_detail' => 2, 'id_slot' => 2, 'jumlah' => 450, 'tanggal_instruksi' => '2025-03-12', 'status' => 'selesai', 'created_at' => now(), 'updated_at' => now()],
            ['id_instruksi' => 2, 'id_detail' => 4, 'id_slot' => 3, 'jumlah' => 1000, 'tanggal_instruksi' => '2025-03-16', 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 10. Permintaan Pengambilan
        DB::table('permintaan_pengambilan')->insert([
            ['id_permintaan' => 1, 'id_petani' => 1, 'id_penyimpanan' => 1, 'tanggal_permintaan' => '2025-04-01', 'status' => 'disetujui', 'created_at' => now(), 'updated_at' => now()],
            ['id_permintaan' => 2, 'id_petani' => 2, 'id_penyimpanan' => 2, 'tanggal_permintaan' => '2025-04-05', 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 11. Detail Pengambilan
        DB::table('detail_pengambilan')->insert([
            ['id_detail_ambil' => 1, 'id_permintaan' => 1, 'id_penyimpanan' => 1, 'jumlah' => 300, 'alasan' => 'Kebutuhan konsumsi rumah tangga', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Update status penyimpanan jadi sebagian diambil
        DB::table('penyimpanan_gabah')->where('id_penyimpanan', 1)->update(['jumlah' => 500, 'status' => 'tersimpan']);

        // 12. Pengelola
        DB::table('pengelola')->insert([
            ['id_pengelola' => 1, 'nama_pengelola' => 'Dewi Lestari', 'no_hp' => '081234567890', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 13. Admin
        DB::table('admin')->insert([
            ['id_admin' => 1, 'nama_admin' => 'Admin Utama', 'jabatan' => 'Super Administrator', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 14. Login (password: rahasia123 -> hash Argon2)
        $argonHash = Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]);
        DB::table('login')->insert([
            ['id_login' => 1, 'id_petani' => 1, 'id_pengelola' => null, 'id_admin' => null, 'username' => 'slamet.tani', 'password' => $argonHash, 'role' => 'petani', 'created_at' => now(), 'updated_at' => now()],
            ['id_login' => 2, 'id_petani' => 2, 'id_pengelola' => null, 'id_admin' => null, 'username' => 'nurhayati', 'password' => $argonHash, 'role' => 'petani', 'created_at' => now(), 'updated_at' => now()],
            ['id_login' => 3, 'id_petani' => null, 'id_pengelola' => 1, 'id_admin' => null, 'username' => 'pengelola.dewi', 'password' => $argonHash, 'role' => 'pengelola', 'created_at' => now(), 'updated_at' => now()],
            ['id_login' => 4, 'id_petani' => null, 'id_pengelola' => null, 'id_admin' => 1, 'username' => 'adminpusat', 'password' => $argonHash, 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}