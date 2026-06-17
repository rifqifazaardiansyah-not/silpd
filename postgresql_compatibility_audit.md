# Laporan Audit Kompatibilitas PostgreSQL (Supabase) - Project SILPD

Dokumen ini berisi hasil analisis menyeluruh terhadap codebase Laravel **SILPD** untuk mengidentifikasi query database, migrasi, seeder, dan fungsionalitas SQL lainnya yang tidak kompatibel (non-compact) dengan PostgreSQL (Supabase), karena proyek ini sebelumnya dikembangkan menggunakan MySQL.

---

## Ringkasan Temuan

Secara umum, karena project ini menggunakan **Eloquent ORM** dan **Query Builder** bawaan Laravel untuk sebagian besar operasinya, sekitar 95% query Anda akan otomatis berjalan dengan baik setelah berpindah ke PostgreSQL. 

Namun, ada beberapa isu penting (kategori Kritis dan Peringatan) yang ditemukan dan perlu disesuaikan agar aplikasi dapat berjalan 100% normal tanpa error di Supabase.

---

## 1. Temuan Kritis (Wajib Diperbaiki agar Tidak Error)

### A. Penggunaan Fungsi `FIELD()` di Query Builder
PostgreSQL tidak memiliki fungsi bawaan `FIELD()` seperti pada MySQL. Penggunaan fungsi ini akan memicu SQL syntax error di PostgreSQL.

*   **Temuan 1: Pengurutan Status Permintaan**
    *   **File:** [PermintaanController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/PermintaanController.php#L57) (Line 57)
    *   **Kode MySQL saat ini:**
        ```php
        $permintaanList = $query
            ->orderByRaw("FIELD(status, 'pending', 'disetujui', 'selesai', 'ditolak')")
            ->orderBy('tanggal_permintaan')
            ...
        ```
    *   **Solusi Kompatibel (PostgreSQL & MySQL):**
        Gunakan klausa `CASE WHEN` standar SQL:
        ```php
        $permintaanList = $query
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'disetujui' THEN 2 
                WHEN status = 'selesai' THEN 3 
                WHEN status = 'ditolak' THEN 4 
                ELSE 5 
            END")
            ->orderBy('tanggal_permintaan')
            ...
        ```

*   **Temuan 2: Pengurutan Status Instruksi**
    *   **File:** [InstruksiController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/InstruksiController.php#L54) (Line 54)
    *   **Kode MySQL saat ini:**
        ```php
        $instruksiList = $query
            ->orderByRaw("FIELD(status, 'pending', 'selesai')")
            ->orderBy('tanggal_instruksi')
            ...
        ```
    *   **Solusi Kompatibel (PostgreSQL & MySQL):**
        ```php
        $instruksiList = $query
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'selesai' THEN 2 
                ELSE 3 
            END")
            ->orderBy('tanggal_instruksi')
            ...
        ```

---

### B. Sinkronisasi Sequence Primary Key Pasca Seeding
Di MySQL, memasukkan primary key secara manual (hardcoded) saat seeding otomatis memperbarui penunjuk `AUTO_INCREMENT` ke ID tertinggi berikutnya. Di PostgreSQL, sequence pencatat auto-increment **tidak otomatis sinkron**. Hal ini akan menyebabkan error `Duplicate key / Unique constraint violation` saat user mencoba menambah data baru dari aplikasi setelah seeder dijalankan.

*   **File Terkait:** [DatabaseSeeder.php](file:///e:/laragon2/www/silpd/database/seeders/DatabaseSeeder.php) (seluruh penyisipan data dummy dengan id eksplisit).
*   **Masalah:** 
    Aplikasi memasukkan ID manual seperti `'id_petani' => 1`, `'id_petani' => 2`, dll. Saat aplikasi riil mencoba menyimpan petani baru, PostgreSQL memanggil `nextval('petani_id_petani_seq')` yang menghasilkan angka `1`, yang berujung pada error duplikasi kunci.
*   **Solusi Kompatibel:**
    Tambahkan kode di bagian akhir `run()` di file `DatabaseSeeder.php` untuk mereset sequence PostgreSQL ke nilai tertinggi saat ini jika koneksi database menggunakan driver `pgsql`:
    ```php
    if (config('database.default') === 'pgsql') {
        $tables = [
            'kelompok_tani'          => 'id_kelompok',
            'petani'                 => 'id_petani',
            'jenis_gabah'            => 'id_jenis_gabah',
            'panen'                  => 'id_panen',
            'detail_panen'           => 'id_detail',
            'pengelola'              => 'id_pengelola',
            'lumbung'                => 'id_lumbung',
            'lumbung_pengelola'      => 'id_lumbung_pengelola',
            'slot_lumbung'           => 'id_slot',
            'instruksi_penyimpanan'  => 'id_instruksi',
            'penyimpanan_gabah'      => 'id_penyimpanan',
            'permintaan_pengambilan' => 'id_permintaan',
            'detail_pengambilan'     => 'id_detail_ambil',
            'admin'                  => 'id_admin',
            'login'                  => 'id_login'
        ];

        foreach ($tables as $table => $primaryKey) {
            $maxId = DB::table($table)->max($primaryKey) ?: 0;
            $seqName = "{$table}_{$primaryKey}_seq";
            DB::statement("SELECT setval(pg_get_serial_sequence(?, ?), ?)", [$table, $primaryKey, $maxId]);
        }
    }
    ```

---

## 2. Temuan Menengah & Perbedaan Perilaku (Sangat Disarankan untuk Diperbaiki)

### A. Perilaku Operator `LIKE` (Case Sensitivity)
Pada MySQL, operator `LIKE` secara default bersifat case-insensitive (tidak sensitif huruf besar/kecil). Sedangkan pada PostgreSQL, operator `LIKE` bersifat **case-sensitive** (sensitif huruf besar/kecil). 

Jika user mencari petani bernama "budi", tetapi di database tersimpan "Budi", pencarian dengan `like` di PostgreSQL akan menghasilkan **nol (tidak ditemukan)**.

*   **Lokasi Temuan:**
    1.  [SlotLumbungController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/SlotLumbungController.php#L29) (Line 29): `->where('kode_slot', 'like', ...)`
    2.  [PetaniController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/PetaniController.php#L22) (Line 22 & 183): `->where('nama_petani', 'like', ...)`
    3.  [PengelolaController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/PengelolaController.php#L25) (Line 25): `->where('nama_pengelola', 'like', ...)`
    4.  [LumbungController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/LumbungController.php#L22) (Line 22): `->where('nama_lumbung', 'like', ...)`
    5.  [LaporanController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/LaporanController.php#L58) (Line 58): `->where('nama_petani', 'like', ...)`
    6.  [KelompokTaniController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/KelompokTaniController.php#L21) (Line 21): `->where('nama_kelompok', 'like', ...)`
    7.  [JenisGabahController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/JenisGabahController.php#L20) (Line 20): `->where('nama_jenis', 'like', ...)`
    8.  [AkunController.php](file:///e:/laragon2/www/silpd/app/Http/Controllers/Admin/AkunController.php#L35) (Line 35-38): `->where('username', 'like', ...)`
*   **Solusi:**
    *   **Opsi Khusus PostgreSQL:** Ubah semua operator `'like'` menjadi `'ilike'` (operator case-insensitive khusus pgsql).
    *   **Opsi Database-Agnostic (Bisa MySQL & PostgreSQL):** Gunakan query dengan fungsi `LOWER()` untuk menyeragamkan perbandingan string:
        ```php
        // Contoh untuk PetaniController.php
        $query->where(DB::raw('LOWER(nama_petani)'), 'like', '%' . strtolower($request->search) . '%');
        ```

---

### B. Pengubahan Kolom `ENUM` di Masa Mendatang
Pada PostgreSQL, Laravel menangani schema tipe data `ENUM` dengan membuat custom type khusus di database. Hal ini membuat proses pengubahan/modifikasi nilai enum melalui migration di kemudian hari (`->change()`) menjadi sangat sulit dan sering error di driver pgsql Laravel.

*   **Lokasi Temuan (Migrations):**
    *   [lumbung_pengelola_table.php](file:///e:/laragon2/www/silpd/database/migrations/2026_05_11_162805_create_lumbung_pengelola_table.php#L64): `peran` (`['pemilik_akun', 'anggota']`)
    *   [login_table.php](file:///e:/laragon2/www/silpd/database/migrations/2026_05_11_162800_create_login_table.php#L18): `role` (`['petani', 'pengelola', 'admin']`)
    *   [permintaan_pengambilan_table.php](file:///e:/laragon2/www/silpd/database/migrations/2026_05_11_162780_create_permintaan_pengambilan_table.php#L16): `status` (`['pending', 'disetujui', 'ditolak', 'selesai']`)
    *   [instruksi_penyimpanan_table.php](file:///e:/laragon2/www/silpd/database/migrations/2026_05_11_162775_create_instruksi_penyimpanan_table.php#L17): `status` (`['pending', 'selesai']`)
    *   [penyimpanan_gabah_table.php](file:///e:/laragon2/www/silpd/database/migrations/2026_05_11_162770_create_penyimpanan_gabah_table.php#L17): `status` (`['tersimpan', 'diambil', 'habis']`)
*   **Rekomendasi (Opsional):**
    Jika Anda berencana memperluas status atau peran di masa depan, disarankan mengubah tipe kolom migration ini menjadi tipe data string biasa (`$table->string('status', 30)`) dan menerapkan validasi nilai di tingkat kode aplikasi Laravel menggunakan Validation Rule (seperti `Rule::in([...])`). Namun, jika status ini sudah final, biarkan seperti saat ini.

---

## 3. Hasil Audit Query Lainnya (Dinyatakan Aman ✅)

Berikut adalah syntax-syntax raw yang sering bermasalah saat migrasi ke PostgreSQL, tetapi setelah diaudit, penggunaannya di project SILPD dinyatakan **Aman dan Kompatibel**:

1.  **Fungsi Kalkulasi dan Casting Matematika**
    *   `DashboardController.php` (Line 73 & 102): `kapasitas_tersedia / kapasitas * 100` dan `CAST(SUM(...) AS DECIMAL(10,2))`
    *   *Analisis:* Aman. Syntax pembagian aritmatika dan `CAST` tipe data standar SQL ini sepenuhnya didukung oleh PostgreSQL.
2.  **Fungsi Agregasi di selectRaw dan groupBy**
    *   `LaporanController.php` (Line 215): `selectRaw('tanggal_panen, COUNT(*) as jumlah_transaksi...')` yang diikuti oleh `groupBy('tanggal_panen')`.
    *   *Analisis:* Aman. PostgreSQL sangat ketat dengan aturan `GROUP BY`, namun karena query ini telah mengelompokkan hasil berdasarkan kolom non-agregasi yang dipilih (`tanggal_panen`), query ini kompatibel.
3.  **Penggunaan Helper Tanggal**
    *   `PanenController.php` & `DashboardController.php`: `whereMonth('tanggal_panen', ...)` dan `whereYear('tanggal_panen', ...)`
    *   *Analisis:* Aman. Laravel secara otomatis menangani perbedaan fungsi ekstrak tanggal (seperti `MONTH()` di MySQL vs `EXTRACT(MONTH FROM ...)` di PostgreSQL) sesuai driver database aktif.
