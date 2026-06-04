# AUDIT PROMPT — SILPD Laravel Project Auditor
## Untuk: Claude Haiku 4.5 di VS Code
## Tujuan: Audit konsistensi dan fungsionalitas keseluruhan project SILPD

---

## CARA PAKAI

Audit ini dibagi menjadi **6 audit berlapis**. Jalankan **satu per satu secara berurutan**.
Setiap audit fokus pada satu lapisan — jangan digabung agar model tidak kehilangan konteks.

Urutan audit:
1. **Audit 1** → Database: Migrations + Models + Relasi
2. **Audit 2** → Auth: Middleware + LoginController + Session
3. **Audit 3** → Routes ↔ Controllers (sinkronisasi nama & parameter)
4. **Audit 4** → Controllers ↔ Views (variabel yang dikirim vs yang dipakai)
5. **Audit 5** → Frontend Consistency (Blade rules, old(), CSRF, route helper)
6. **Audit 6** → Business Logic (FIFO, kalkulasi 3%, kapasitas slot, status flow)

Setiap audit menghasilkan **laporan temuan** berformat checklist.
Jika ada temuan, AI akan langsung memperbaiki file yang bersangkutan.

---

## ═══════════════════════════════════════════
## CONTEXT BLOCK AUDIT (Wajib ada di SETIAP audit)
## ═══════════════════════════════════════════

```
Kamu adalah senior Laravel auditor. Kamu sedang mengaudit project SILPD (Sistem Informasi Lumbung Padi Desa) — sistem manajemen lumbung pangan desa berbasis Laravel 10 + Blade + Tailwind CSS.

== STACK ==
- Laravel 10.50.2
- Blade templating (bukan React/Vue)
- Tailwind CSS
- Database: MySQL, semua primary key berbentuk id_xxx (bukan id)

== STRUKTUR FOLDER UTAMA ==
app/
  Http/
    Controllers/
      Auth/LoginController.php
      Admin/
        DashboardController.php, PetaniController.php, KelompokTaniController.php,
        JenisGabahController.php, LumbungController.php, SlotLumbungController.php,
        PengelolaController.php, AkunController.php, PanenController.php,
        InstruksiController.php, PermintaanController.php, LaporanController.php
      Pengelola/
        DashboardController.php, InstruksiPenyimpananController.php,
        PengeluaranGabahController.php, StokController.php
      Petani/
        DashboardController.php, StokController.php, PermintaanController.php
    Middleware/
      RoleMiddleware.php
  Models/
    Login.php, Admin.php, Petani.php, KelompokTani.php, Pengelola.php,
    Lumbung.php, LumbungPengelola.php, SlotLumbung.php, JenisGabah.php,
    Panen.php, DetailPanen.php, PenyimpananGabah.php, InstruksiPenyimpanan.php,
    PermintaanPengambilan.php, DetailPengambilan.php
resources/views/
  layouts/admin.blade.php, pengelola.blade.php, petani.blade.php
  auth/login.blade.php
  admin/ (dashboard, petani, kelompok, jenis-gabah, lumbung, slot, pengelola, akun, panen, instruksi, permintaan, laporan)
  pengelola/ (dashboard, instruksi, pengeluaran, stok)
  petani/ (dashboard, stok, permintaan)
routes/web.php

== SKEMA DATABASE (ringkasan) ==
- kelompok_tani: id_kelompok, nama_kelompok
- petani: id_petani, id_kelompok, nama_petani, luas_lahan
- jenis_gabah: id_jenis_gabah, nama_jenis
- panen: id_panen, id_petani, tanggal_panen
- detail_panen: id_detail, id_panen, id_jenis_gabah, jumlah_panen
- pengelola: id_pengelola, nama_pengelola, no_hp
- lumbung: id_lumbung, nama_lumbung
- lumbung_pengelola: id_lumbung_pengelola, id_lumbung, id_pengelola, peran (pemilik_akun|anggota)  ← PIVOT many-to-many
- slot_lumbung: id_slot, id_lumbung, kode_slot, kapasitas, kapasitas_tersedia
- penyimpanan_gabah: id_penyimpanan, id_detail, id_slot, jumlah, tanggal_masuk, status (tersimpan|habis)
- instruksi_penyimpanan: id_instruksi, id_detail, id_slot, jumlah, tanggal_instruksi, status (pending|selesai)
- permintaan_pengambilan: id_permintaan, id_petani, id_penyimpanan, tanggal_permintaan, status (pending|disetujui|ditolak|selesai)
- detail_pengambilan: id_detail_ambil, id_permintaan, id_penyimpanan, jumlah, alasan
- admin: id_admin, nama_admin, jabatan
- login: id_login, id_petani(nullable), id_pengelola(nullable), id_admin(nullable), username, password, role (admin|pengelola|petani)

== SESSION VARIABLES ==
session('login_id')  → id dari tabel login
session('role')      → 'admin' | 'pengelola' | 'petani'
session('nama')      → nama tampilan pengguna
session('ref_id')    → id_petani / id_pengelola / id_admin sesuai role

== BUSINESS RULES KRITIS ==
1. Setiap panen otomatis hitung 3% dari jumlah_panen → jumlah untuk lumbung
2. Penempatan gabah ke slot dikontrol via instruksi_penyimpanan (admin buat → pengelola konfirmasi)
3. FIFO: gabah yang masuk lebih dulu (tanggal_masuk lebih lama) harus diambil lebih dulu
4. kapasitas_tersedia slot harus dikurangi saat instruksi dikonfirmasi, ditambah saat gabah diambil
5. lumbung ↔ pengelola adalah MANY-TO-MANY via lumbung_pengelola — TIDAK ADA kolom id_pengelola di tabel lumbung

== FORMAT LAPORAN TEMUAN ==
Untuk setiap temuan, tulis:
[SEVERITY] FILE: deskripsi masalah → rekomendasi perbaikan

Severity:
  🔴 KRITIS   — sistem tidak bisa jalan (missing method, wrong variable, broken route)
  🟡 PERINGATAN — bisa jalan tapi salah logika atau tidak konsisten
  🟢 SARAN    — best practice, minor improvement

Setelah laporan, langsung perbaiki semua temuan 🔴 KRITIS dan 🟡 PERINGATAN.
Untuk 🟢 SARAN, tanyakan dulu sebelum memperbaiki.
```

---

## ═══════════════════════════════════════════
## AUDIT 1 — Database: Migrations + Models + Relasi
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK AUDIT DI ATAS TERLEBIH DAHULU]

Baca file-file berikut:
- Semua file di database/migrations/
- Semua file di app/Models/

Lakukan audit dengan checklist berikut:

== CHECKLIST MIGRATIONS ==
□ Setiap tabel yang ada di skema database sudah punya migration
□ Urutan migration benar (foreign key tidak dibuat sebelum tabel induknya)
□ Semua foreign key pakai ->constrained() atau ->references() yang benar
□ Tabel pivot lumbung_pengelola ada dengan kolom: id_lumbung_pengelola, id_lumbung, id_pengelola, peran, timestamps
□ Kolom peran di lumbung_pengelola punya ->default('anggota') atau validasi enum
□ Semua primary key custom (id_petani, id_lumbung, dst) menggunakan ->primary() bukan default id()
□ Tabel login punya kolom nullable yang benar: id_petani nullable, id_pengelola nullable, id_admin nullable

== CHECKLIST MODELS ==
□ Setiap model punya $table yang benar (karena nama tabel tidak mengikuti konvensi Laravel)
□ Setiap model punya $primaryKey yang benar (id_petani, id_lumbung, dst)
□ $incrementing = true dan $keyType = 'int' sudah disetel di semua model
□ $fillable sudah lengkap untuk semua kolom yang perlu mass assignment
□ $hidden berisi 'password' di model Login

== CHECKLIST RELASI ==
□ Petani → belongsTo(KelompokTani::class, 'id_kelompok')
□ Petani → hasMany(Panen::class, 'id_petani')
□ Petani → hasMany(PermintaanPengambilan::class, 'id_petani')
□ Panen → hasMany(DetailPanen::class, 'id_panen')
□ DetailPanen → belongsTo(JenisGabah::class, 'id_jenis_gabah')
□ DetailPanen → hasOne(InstruksiPenyimpanan::class, 'id_detail')
□ DetailPanen → hasOne(PenyimpananGabah::class, 'id_detail')
□ Lumbung → belongsToMany(Pengelola::class, 'lumbung_pengelola', 'id_lumbung', 'id_pengelola')->withPivot('peran')->withTimestamps()
□ Pengelola → belongsToMany(Lumbung::class, 'lumbung_pengelola', 'id_pengelola', 'id_lumbung')->withPivot('peran')->withTimestamps()
□ Lumbung → hasMany(SlotLumbung::class, 'id_lumbung')
□ SlotLumbung → hasMany(PenyimpananGabah::class, 'id_slot')
□ SlotLumbung → hasMany(InstruksiPenyimpanan::class, 'id_slot')
□ PenyimpananGabah → hasMany(PermintaanPengambilan::class, 'id_penyimpanan')
□ PermintaanPengambilan → hasMany(DetailPengambilan::class, 'id_permintaan')
□ Login → belongsTo(Petani::class, 'id_petani') [nullable]
□ Login → belongsTo(Pengelola::class, 'id_pengelola') [nullable]
□ Login → belongsTo(Admin::class, 'id_admin') [nullable]

Buat laporan temuan, lalu langsung perbaiki semua 🔴 KRITIS dan 🟡 PERINGATAN yang ditemukan.
```

---

## ═══════════════════════════════════════════
## AUDIT 2 — Auth: Middleware + LoginController + Session
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK AUDIT DI ATAS TERLEBIH DAHULU]

Baca file-file berikut:
- app/Http/Middleware/RoleMiddleware.php
- app/Http/Controllers/Auth/LoginController.php
- app/Http/Kernel.php (atau bootstrap/app.php jika Laravel 11+)
- routes/web.php (bagian middleware saja)

Lakukan audit dengan checklist berikut:

== CHECKLIST MIDDLEWARE ==
□ RoleMiddleware terdaftar di Kernel.php dengan alias 'role'
□ RoleMiddleware membaca session('role') dan membandingkan dengan parameter yang diberikan
□ Jika role tidak cocok, redirect ke route yang benar (bukan 403 mentah)
□ Middleware tidak mengizinkan akses jika session('login_id') kosong (belum login)
□ Route group middleware: role:admin, role:pengelola, role:petani sudah ada di web.php

== CHECKLIST LOGIN CONTROLLER ==
□ Method showLoginForm() mengembalikan view('auth.login')
□ Method login() memvalidasi: username required, password required
□ Query login menggunakan tabel 'login', cari berdasarkan kolom 'username'
□ Password diverifikasi dengan Hash::check($request->password, $login->password)
□ Setelah login berhasil, session yang di-set: login_id, role, nama, ref_id
□ session('nama') diambil dari tabel yang tepat sesuai role:
    role=petani    → query ke tabel petani, ambil nama_petani
    role=pengelola → query ke tabel pengelola, ambil nama_pengelola
    role=admin     → query ke tabel admin, ambil nama_admin
□ Redirect setelah login menggunakan match(session('role')) ke dashboard masing-masing role
□ Method logout() menjalankan Session::flush() atau $request->session()->invalidate() lalu redirect ke login

== CHECKLIST SESSION CONSISTENCY ==
□ session('ref_id') berisi id_petani / id_pengelola / id_admin (bukan id_login)
□ Tidak ada controller yang menggunakan Auth::user() — sistem ini menggunakan session manual, bukan Laravel Auth

Buat laporan temuan, lalu langsung perbaiki semua 🔴 KRITIS dan 🟡 PERINGATAN.
```

---

## ═══════════════════════════════════════════
## AUDIT 3 — Routes ↔ Controllers
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK AUDIT DI ATAS TERLEBIH DAHULU]

Baca file-file berikut:
- routes/web.php
- Semua file di app/Http/Controllers/Admin/
- Semua file di app/Http/Controllers/Pengelola/
- Semua file di app/Http/Controllers/Petani/

Lakukan audit dengan checklist berikut:

== CHECKLIST ROUTES ↔ CONTROLLERS ==
□ Setiap route yang terdaftar di web.php punya method yang sesuai di controller-nya
□ Tidak ada method controller yang tidak punya route (orphan methods)
□ Parameter route konsisten: id, idLumbung, idSlot, idDetail — tidak boleh campur snake_case dan camelCase
□ Route resource yang pakai ->only([...]) tidak kehilangan method CRUD yang dibutuhkan view
□ Named routes konsisten antara web.php dan yang dipakai di controller (redirect()->route('...'))

== CHECKLIST KHUSUS LUMBUNG/PENGELOLA ==
□ Tidak ada route yang memanggil metode dengan asumsi $lumbung->id_pengelola (kolom ini tidak ada)
□ Controller LumbungController@store dan @update menggunakan sync() atau attach()/detach() untuk relasi lumbung_pengelola
□ Controller LumbungController@store memproses input pengelola[] array dari form (bukan single select)

== CHECKLIST NESTED RESOURCES ==
□ Route lumbung.slot.* menggunakan parameter idLumbung dan idSlot secara konsisten
□ Controller SlotLumbungController menerima $idLumbung dan $idSlot sebagai parameter method

== CHECKLIST LAPORAN ==
□ Route laporan.ekspor.stok dan laporan.ekspor.panen ada di web.php
□ Method eksporStokCsv() dan eksporPanenCsv() ada di LaporanController

Buat laporan temuan, lalu langsung perbaiki semua 🔴 KRITIS dan 🟡 PERINGATAN.
```

---

## ═══════════════════════════════════════════
## AUDIT 4 — Controllers ↔ Views (Variabel)
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK AUDIT DI ATAS TERLEBIH DAHULU]

Audit ini dilakukan per sub-grup. Jalankan dua kali jika perlu:
  - Sub-audit 4A: Admin controllers + views
  - Sub-audit 4B: Pengelola controllers + views + Petani controllers + views

Untuk setiap pasangan controller@method ↔ view, periksa:

== CARA AUDIT ==
Untuk setiap controller method yang memanggil return view('...', [...]):
1. Catat semua variabel yang di-pass ke view (key dari array compact() atau ->with())
2. Buka file view yang bersangkutan
3. Periksa semua variabel yang DIPAKAI di view ({{ $xxx }}, @foreach($xxx), @if($xxx), dsb)
4. Tandai jika ada variabel yang dipakai di view tapi TIDAK dikirim controller → 🔴 KRITIS
5. Tandai jika ada variabel yang dikirim controller tapi TIDAK dipakai di view → 🟢 SARAN

== CHECKLIST KHUSUS YANG SERING BERMASALAH ==

Admin:
□ LumbungController@index mengirim $lumbungList with('pengelola') (relasi many-to-many, bukan single)
□ LumbungController@show mengirim $lumbung->load('pengelola') (collection, bukan single object)
□ LumbungController@create dan @edit mengirim $pengelolaList (semua pengelola, untuk checkbox)
□ PanenController@create mengirim $persenLumbung (nilai 3 atau 0.03 — konsisten dengan view)
□ InstruksiController@show mengirim $instruksi->load('slotLumbung.lumbung.pengelola') (many-to-many)
□ PermintaanController@show mengirim $adaPelanggaranFifo dan $rekomendasiFifo
□ LaporanController@stok mengirim $rekapPerLumbung, $rekapPerJenis, $totalStokKeseluruhan

Pengelola:
□ InstruksiPenyimpananController@show mengirim validasi kapasitas (kapasitas_tersedia vs jumlah instruksi)
□ PengeluaranGabahController@show mengirim preview sisa stok setelah pengeluaran

Petani:
□ PermintaanController@create mengirim $stokTersimpan dalam urutan FIFO (order by tanggal_masuk ASC)
□ PermintaanController@show mengirim relasi lengkap untuk status tracker

Buat laporan temuan, lalu langsung perbaiki semua 🔴 KRITIS dan 🟡 PERINGATAN.
```

---

## ═══════════════════════════════════════════
## AUDIT 5 — Frontend Consistency (Blade Rules)
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK AUDIT DI ATAS TERLEBIH DAHULU]

Baca semua file di resources/views/ (semua subfolder).
Audit ini fokus pada konsistensi Blade dan UI — bukan logika bisnis.

== CHECKLIST WAJIB (setiap pelanggaran = 🔴 KRITIS) ==
□ Setiap form POST/PUT/DELETE punya @csrf
□ Setiap form edit/delete punya @method('PUT') atau @method('DELETE')
□ Setiap input punya value="{{ old('field', $model->field ?? '') }}"
□ Tidak ada hard-coded URL — semua pakai route()
□ Setiap halaman meng-@extends layout yang sesuai rolenya (admin→layouts.admin, dst)
□ Setiap tombol hapus punya onclick="return confirm('...')"
□ Setiap input punya @error('field') di bawahnya

== CHECKLIST KONSISTENSI PIVOT (many-to-many pengelola↔lumbung) ==
□ Tidak ada view yang mengakses $lumbung->pengelola->nama_pengelola secara langsung (harus loop)
□ Tidak ada view yang mengakses $lumbung->id_pengelola atau $pengelola->id_lumbung
□ Semua view yang menampilkan peran pengelola menggunakan $pengelola->pivot->peran
□ Badge peran konsisten: pemilik_akun→bg-indigo-50 text-indigo-700, anggota→bg-gray-100 text-gray-600

== CHECKLIST FORMAT & DISPLAY ==
□ Semua angka kg menggunakan {{ number_format($value) }} atau {{ number_format($value, 2) }}
□ Semua tanggal menggunakan \Carbon\Carbon::parse($date)->translatedFormat('d M Y')
□ Status badge menggunakan warna yang konsisten (sesuai SILPD-DESIGN.md)
□ Capacity bar menggunakan threshold: 0-59% emerald, 60-79% amber, 80-100% red

== CHECKLIST LAYOUT SIDEBAR ==
□ Active state sidebar menggunakan request()->routeIs('role.section.*')
□ session('nama') ditampilkan di topbar semua layout
□ Tombol logout menggunakan form POST ke route('logout') dengan @csrf

Buat laporan temuan, lalu langsung perbaiki semua 🔴 KRITIS dan 🟡 PERINGATAN.
Kelompokkan temuan per file view agar mudah dilacak.
```

---

## ═══════════════════════════════════════════
## AUDIT 6 — Business Logic
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK AUDIT DI ATAS TERLEBIH DAHULU]

Baca file-file berikut:
- app/Http/Controllers/Admin/PanenController.php
- app/Http/Controllers/Admin/InstruksiController.php
- app/Http/Controllers/Admin/PermintaanController.php
- app/Http/Controllers/Pengelola/InstruksiPenyimpananController.php
- app/Http/Controllers/Pengelola/PengeluaranGabahController.php
- app/Http/Controllers/Petani/PermintaanController.php
- app/Models/ (semua model relevan)

Lakukan audit business logic berikut:

== KALKULASI 3% LUMBUNG ==
□ PanenController@store: setiap detail_panen dihitung 3% → round($jumlah_panen * 0.03, 2)
□ Hasil kalkulasi disimpan ke instruksi_penyimpanan.jumlah (bukan ke detail_panen)
□ Instruksi dibuat otomatis saat panen disimpan, dengan status 'pending'
□ Jika tidak ada slot tersedia, instruksi tetap dibuat (admin akan assign manual)

== KONFIRMASI INSTRUKSI (PENGELOLA) ==
□ InstruksiPenyimpananController@konfirmasi:
  - Cek kapasitas_tersedia slot >= jumlah instruksi sebelum konfirmasi
  - Jika kapasitas tidak cukup → return back()->withErrors([...])
  - Jika cukup → buat record penyimpanan_gabah dengan status 'tersimpan'
  - Kurangi slot_lumbung.kapasitas_tersedia sebesar jumlah instruksi (DB transaction)
  - Update instruksi_penyimpanan.status → 'selesai'
  - Semua operasi di dalam DB::transaction()

== FIFO ENFORCEMENT ==
□ Petani\PermintaanController@create: $stokTersimpan diquery dengan orderBy('tanggal_masuk', 'asc')
□ Admin\PermintaanController@show: deteksi pelanggaran FIFO
  - Ambil semua stok jenis gabah yang sama milik petani yang sama
  - Cek apakah ada stok dengan tanggal_masuk lebih lama dari yang diminta
  - Jika ada → $adaPelanggaranFifo = true, $rekomendasiFifo = stok yang lebih lama

== PENGAMBILAN GABAH ==
□ PengeluaranGabahController@konfirmasi:
  - Update penyimpanan_gabah.jumlah dikurangi jumlah yang diambil
  - Jika jumlah hasil = 0, update status penyimpanan → 'habis'
  - Tambah kembali slot_lumbung.kapasitas_tersedia sebesar jumlah yang diambil
  - Update permintaan_pengambilan.status → 'selesai'
  - Semua operasi di dalam DB::transaction()

== STATUS FLOW PERMINTAAN ==
□ Status permintaan hanya bisa bergerak sesuai alur:
  pending → disetujui (oleh admin)
  pending → ditolak (oleh admin)
  disetujui → selesai (setelah pengelola konfirmasi pengeluaran)
  disetujui → ditolak (admin bisa batalkan — kasus khusus)
□ Tidak ada status yang bisa di-set langsung ke 'selesai' tanpa melalui konfirmasi pengelola

== KAPASITAS SLOT ==
□ kapasitas_tersedia tidak pernah negatif (ada validasi di controller)
□ kapasitas_tersedia tidak pernah melebihi kapasitas (ada validasi saat edit slot)
□ Kalkulasi: persentase_terpakai = (kapasitas - kapasitas_tersedia) / kapasitas * 100

Buat laporan temuan, lalu langsung perbaiki semua 🔴 KRITIS dan 🟡 PERINGATAN.
Untuk temuan yang melibatkan DB::transaction(), pastikan perbaikan mempertahankan atomicity.
```

---

## CATATAN PENTING UNTUK AI AGENT (AUDITOR)

1. **Baca file asli sebelum menilai** — jangan asumsikan isi file berdasarkan nama. Selalu gunakan tool baca file.
2. **Perbaikan harus atomic** — jika ada dua file yang saling bergantung (misal controller dan view), perbaiki keduanya sekaligus.
3. **Jangan ubah nama route** — nama route di web.php adalah kontrak antara semua bagian sistem. Jika ada yang salah, perbaiki referensi di controller/view, bukan nama route-nya.
4. **Lumbung↔Pengelola = many-to-many** — ini adalah aturan keras. Jika ditemukan kode yang mengasumsikan relasi 1-to-1, itu selalu 🔴 KRITIS.
5. **DB::transaction() wajib** untuk operasi yang mengubah lebih dari satu tabel (konfirmasi instruksi, konfirmasi pengeluaran, pengambilan gabah).
6. **Setelah setiap audit selesai**, ringkas: berapa temuan KRITIS, PERINGATAN, SARAN — dan berapa yang sudah diperbaiki.
