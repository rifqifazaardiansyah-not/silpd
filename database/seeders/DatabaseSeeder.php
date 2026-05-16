<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Password default untuk semua akun dummy: "rahasia123"
        // Catatan: Hash::make() dipanggil per-user agar setiap hash berbeda (lebih realistik)
        // Meskipun password sama, hash akan berbeda karena salt di-generate random
        // Tapi Hash::check() tetap bisa verify dengan benar

        // =====================================================================
        // 1. KELOMPOK TANI
        // =====================================================================
        DB::table('kelompok_tani')->insert([
            [
                'id_kelompok'  => 1,
                'nama_kelompok' => 'Tani Makmur',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_kelompok'  => 2,
                'nama_kelompok' => 'Gabah Sejahtera',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        // =====================================================================
        // 2. PETANI
        // =====================================================================
        DB::table('petani')->insert([
            [
                'id_petani'   => 1,
                'id_kelompok' => 1,
                'nama_petani' => 'Slamet Riyadi',
                'luas_lahan'  => 2.5,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_petani'   => 2,
                'id_kelompok' => 1,
                'nama_petani' => 'Nurhayati',
                'luas_lahan'  => 1.8,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_petani'   => 3,
                'id_kelompok' => 2,
                'nama_petani' => 'Bambang Suprapto',
                'luas_lahan'  => 3.2,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // =====================================================================
        // 3. JENIS GABAH
        // =====================================================================
        DB::table('jenis_gabah')->insert([
            ['id_jenis_gabah' => 1, 'nama_jenis' => 'IR64',     'created_at' => now(), 'updated_at' => now()],
            ['id_jenis_gabah' => 2, 'nama_jenis' => 'Ciherang', 'created_at' => now(), 'updated_at' => now()],
            ['id_jenis_gabah' => 3, 'nama_jenis' => 'Mekongga', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =====================================================================
        // 4. PANEN
        // =====================================================================
        DB::table('panen')->insert([
            ['id_panen' => 1, 'id_petani' => 1, 'tanggal_panen' => '2025-03-10', 'created_at' => now(), 'updated_at' => now()],
            ['id_panen' => 2, 'id_petani' => 2, 'tanggal_panen' => '2025-03-12', 'created_at' => now(), 'updated_at' => now()],
            ['id_panen' => 3, 'id_petani' => 3, 'tanggal_panen' => '2025-03-15', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =====================================================================
        // 5. DETAIL PANEN
        // Catatan: jumlah_panen dalam kg. Sistem akan hitung 3% untuk lumbung.
        // =====================================================================
        DB::table('detail_panen')->insert([
            // Panen Slamet: 800 kg IR64 → 24 kg lumbung
            ['id_detail' => 1, 'id_panen' => 1, 'id_jenis_gabah' => 1, 'jumlah_panen' => 800,  'created_at' => now(), 'updated_at' => now()],
            // Panen Slamet: 450 kg Ciherang → 13.5 kg lumbung
            ['id_detail' => 2, 'id_panen' => 1, 'id_jenis_gabah' => 2, 'jumlah_panen' => 450,  'created_at' => now(), 'updated_at' => now()],
            // Panen Nurhayati: 600 kg Ciherang → 18 kg lumbung
            ['id_detail' => 3, 'id_panen' => 2, 'id_jenis_gabah' => 2, 'jumlah_panen' => 600,  'created_at' => now(), 'updated_at' => now()],
            // Panen Bambang: 1000 kg Mekongga → 30 kg lumbung
            ['id_detail' => 4, 'id_panen' => 3, 'id_jenis_gabah' => 3, 'jumlah_panen' => 1000, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =====================================================================
        // 6. PENGELOLA
        // Dua pengelola untuk mendemonstrasikan relasi many-to-many dengan lumbung.
        // =====================================================================
        DB::table('pengelola')->insert([
            [
                'id_pengelola'   => 1,
                'nama_pengelola' => 'Dewi Lestari',
                'no_hp'          => '081234567890',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'id_pengelola'   => 2,
                'nama_pengelola' => 'Budi Santoso',
                'no_hp'          => '082345678901',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        // =====================================================================
        // 7. LUMBUNG
        // Tidak ada kolom id_pengelola — relasi pengelola ditangani
        // sepenuhnya oleh tabel pivot lumbung_pengelola.
        // =====================================================================
        DB::table('lumbung')->insert([
            [
                'id_lumbung'  => 1,
                'nama_lumbung' => 'Lumbung Desa A',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_lumbung'  => 2,
                'nama_lumbung' => 'Lumbung Pusat',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // =====================================================================
        // 8. LUMBUNG_PENGELOLA (PIVOT — relasi many-to-many)
        //
        // Skenario data dummy:
        //
        //   Lumbung Desa A:
        //     - Dewi Lestari  → pemilik_akun  (penanggungjawab utama)
        //     - Budi Santoso  → anggota       (membantu pengelolaan)
        //
        //   Lumbung Pusat:
        //     - Budi Santoso  → pemilik_akun  (penanggungjawab utama)
        //     - Dewi Lestari  → anggota       (membantu pengelolaan)
        //
        // Ini mendemonstrasikan bahwa:
        //   - Satu pengelola bisa ada di banyak lumbung (dengan peran berbeda)
        //   - Satu lumbung bisa punya banyak pengelola (termasuk banyak pemilik_akun)
        // =====================================================================
        DB::table('lumbung_pengelola')->insert([
            [
                'id_lumbung_pengelola' => 1,
                'id_lumbung'           => 1,
                'id_pengelola'         => 1,  // Dewi Lestari
                'peran'                => 'pemilik_akun',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'id_lumbung_pengelola' => 2,
                'id_lumbung'           => 1,
                'id_pengelola'         => 2,  // Budi Santoso
                'peran'                => 'anggota',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'id_lumbung_pengelola' => 3,
                'id_lumbung'           => 2,
                'id_pengelola'         => 2,  // Budi Santoso
                'peran'                => 'pemilik_akun',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'id_lumbung_pengelola' => 4,
                'id_lumbung'           => 2,
                'id_pengelola'         => 1,  // Dewi Lestari
                'peran'                => 'anggota',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);

        // =====================================================================
        // 9. SLOT LUMBUNG
        // =====================================================================
        DB::table('slot_lumbung')->insert([
            [
                'id_slot'            => 1,
                'id_lumbung'         => 1,
                'kode_slot'          => 'A1',
                'kapasitas'          => 2000,
                'kapasitas_tersedia' => 1200,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_slot'            => 2,
                'id_lumbung'         => 1,
                'kode_slot'          => 'A2',
                'kapasitas'          => 1500,
                'kapasitas_tersedia' => 1500,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_slot'            => 3,
                'id_lumbung'         => 2,
                'kode_slot'          => 'B1',
                'kapasitas'          => 5000,
                'kapasitas_tersedia' => 3500,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);

        // =====================================================================
        // 10. PENYIMPANAN GABAH
        // Data penyimpanan yang sudah dikonfirmasi pengelola.
        // =====================================================================
        DB::table('penyimpanan_gabah')->insert([
            [
                'id_penyimpanan' => 1,
                'id_detail'      => 1,   // IR64 milik Slamet
                'id_slot'        => 1,   // Slot A1 - Lumbung Desa A
                'jumlah'         => 800,
                'tanggal_masuk'  => '2025-03-11',
                'status'         => 'tersimpan',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'id_penyimpanan' => 2,
                'id_detail'      => 3,   // Ciherang milik Nurhayati
                'id_slot'        => 3,   // Slot B1 - Lumbung Pusat
                'jumlah'         => 600,
                'tanggal_masuk'  => '2025-03-13',
                'status'         => 'tersimpan',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        // Update: sebagian gabah Slamet sudah pernah diambil (300 kg),
        // sisa stok menjadi 500 kg
        DB::table('penyimpanan_gabah')
            ->where('id_penyimpanan', 1)
            ->update(['jumlah' => 500]);

        // =====================================================================
        // 11. INSTRUKSI PENYIMPANAN
        // Instruksi yang dikirim sistem ke pengelola.
        // =====================================================================
        DB::table('instruksi_penyimpanan')->insert([
            [
                'id_instruksi'      => 1,
                'id_detail'         => 2,   // Ciherang milik Slamet (13.5 kg)
                'id_slot'           => 2,   // Slot A2 - Lumbung Desa A
                'jumlah'            => 13.50,
                'tanggal_instruksi' => '2025-03-12',
                'status'            => 'selesai',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'id_instruksi'      => 2,
                'id_detail'         => 4,   // Mekongga milik Bambang (30 kg)
                'id_slot'           => 3,   // Slot B1 - Lumbung Pusat
                'jumlah'            => 30.00,
                'tanggal_instruksi' => '2025-03-16',
                'status'            => 'pending',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);

        // =====================================================================
        // 12. PERMINTAAN PENGAMBILAN
        // =====================================================================
        DB::table('permintaan_pengambilan')->insert([
            [
                'id_permintaan'      => 1,
                'id_petani'          => 1,   // Slamet
                'id_penyimpanan'     => 1,   // IR64 di slot A1
                'tanggal_permintaan' => '2025-04-01',
                'status'             => 'disetujui',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_permintaan'      => 2,
                'id_petani'          => 2,   // Nurhayati
                'id_penyimpanan'     => 2,   // Ciherang di slot B1
                'tanggal_permintaan' => '2025-04-05',
                'status'             => 'pending',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);

        // =====================================================================
        // 13. DETAIL PENGAMBILAN
        // =====================================================================
        DB::table('detail_pengambilan')->insert([
            [
                'id_detail_ambil' => 1,
                'id_permintaan'   => 1,
                'id_penyimpanan'  => 1,
                'jumlah'          => 300,
                'alasan'          => 'Kebutuhan konsumsi rumah tangga',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);

        // =====================================================================
        // 14. ADMIN
        // =====================================================================
        DB::table('admin')->insert([
            [
                'id_admin'   => 1,
                'nama_admin' => 'Admin Utama',
                'jabatan'    => 'Super Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =====================================================================
        // 15. LOGIN
        // Semua akun menggunakan password: rahasia123
        //
        // Setiap user memiliki 2 opsi login:
        // 1. Username handle (slamet.tani, pengelola.dewi, dll)
        // 2. Email format (slamet.riyadi@desa.local, dewi.lestari@desa.local, dll)
        //
        // ┌──────────────┬──────────────────────────────┬────────────────────────────────┐
        // │ Username     │ Email                        │ Role      │ Pemilik            │
        // ├──────────────┼──────────────────────────────┼────────────────────────────────┤
        // │ slamet.tani  │ slamet.riyadi@desa.local     │ petani    │ Slamet Riyadi      │
        // │ nurhayati    │ nurhayati@desa.local         │ petani    │ Nurhayati          │
        // │ pengelola.d… │ dewi.lestari@desa.local      │ pengelola │ Dewi Lestari       │
        // │ pengelola.b… │ budi.santoso@desa.local      │ pengelola │ Budi Santoso       │
        // │ adminpusat   │ admin.desa@desa.local        │ admin     │ Admin Utama        │
        // └──────────────┴──────────────────────────────┴────────────────────────────────┘
        // =====================================================================
        DB::table('login')->insert([
            // Petani 1 - Slamet Riyadi
            [
                'id_login'     => 1,
                'id_petani'    => 1,
                'id_pengelola' => null,
                'id_admin'     => null,
                'username'     => 'slamet.tani',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'petani',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_login'     => 2,
                'id_petani'    => 1,
                'id_pengelola' => null,
                'id_admin'     => null,
                'username'     => 'slamet.riyadi@desa.local',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'petani',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // Petani 2 - Nurhayati
            [
                'id_login'     => 3,
                'id_petani'    => 2,
                'id_pengelola' => null,
                'id_admin'     => null,
                'username'     => 'nurhayati',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'petani',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_login'     => 4,
                'id_petani'    => 2,
                'id_pengelola' => null,
                'id_admin'     => null,
                'username'     => 'nurhayati@desa.local',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'petani',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // Pengelola 1 - Dewi Lestari
            [
                'id_login'     => 5,
                'id_petani'    => null,
                'id_pengelola' => 1,
                'id_admin'     => null,
                'username'     => 'pengelola.dewi',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'pengelola',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_login'     => 6,
                'id_petani'    => null,
                'id_pengelola' => 1,
                'id_admin'     => null,
                'username'     => 'dewi.lestari@desa.local',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'pengelola',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // Pengelola 2 - Budi Santoso
            [
                'id_login'     => 7,
                'id_petani'    => null,
                'id_pengelola' => 2,
                'id_admin'     => null,
                'username'     => 'pengelola.budi',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'pengelola',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_login'     => 8,
                'id_petani'    => null,
                'id_pengelola' => 2,
                'id_admin'     => null,
                'username'     => 'budi.santoso@desa.local',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'pengelola',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // Admin - Admin Utama
            [
                'id_login'     => 9,
                'id_petani'    => null,
                'id_pengelola' => null,
                'id_admin'     => 1,
                'username'     => 'adminpusat',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_login'     => 10,
                'id_petani'    => null,
                'id_pengelola' => null,
                'id_admin'     => 1,
                'username'     => 'admin.desa@desa.local',
                'password'     => Hash::make('rahasia123', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}