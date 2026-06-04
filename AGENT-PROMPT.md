# AGENT PROMPT — SILPD Views & Routes Generator
## Untuk: Claude Haiku 4.5 di VS Code
## Instruksi: Paste prompt di bawah ini ke AI agent VS Code Anda

---

## CARA PAKAI

Prompt ini dirancang untuk dijalankan **bertahap dalam 5 sesi terpisah**.
Jangan jalankan semua sekaligus — model akan kehilangan konteks.

Urutan yang disarankan:
1. Sesi 1 → Layouts + Auth + Routes
2. Sesi 2 → Admin: Dashboard + Data Master (Petani, Kelompok, Jenis Gabah)
3. Sesi 3 → Admin: Lumbung, Slot, Pengelola, Akun
4. Sesi 4 → Admin: Panen, Instruksi, Permintaan, Laporan
5. Sesi 5 → Pengelola views + Petani views

Setiap sesi dimulai dengan **CONTEXT BLOCK** yang sama, diikuti **TARGET BLOCK** spesifik sesi tersebut.

---

## ═══════════════════════════════════════════
## CONTEXT BLOCK (Wajib ada di SETIAP sesi)
## ═══════════════════════════════════════════

```
Kamu adalah expert Laravel developer. Kamu sedang membangun views dan routes untuk sistem bernama SILPD (Sistem Informasi Lumbung Padi Desa) — sistem manajemen lumbung pangan desa berbasis Laravel 10 + Blade + Tailwind CSS.

SISTEM INI BUKAN marketplace, bukan e-commerce. Ini adalah sistem pencatatan dan pengelolaan gabah desa.

== STACK ==
- Laravel 10.50.2
- Blade templating (bukan React/Vue)
- Tailwind CSS (CDN atau via Vite, sudah terpasang)
- Heroicons SVG inline untuk icons
- Font: Inter dari Google Fonts

== SESSION VARIABLES (tersedia di semua view) ==
session('login_id')  → id dari tabel login
session('role')      → 'admin' | 'pengelola' | 'petani'
session('nama')      → nama tampilan pengguna yang login
session('ref_id')    → id_petani / id_pengelola / id_admin sesuai role

== ROLE & WARNA ==
- Admin     → indigo  (bg-indigo-600, sidebar indigo, ring-indigo-500)
- Pengelola → emerald (bg-emerald-600, sidebar emerald, ring-emerald-500)
- Petani    → amber   (bg-amber-600, sidebar amber, ring-amber-500)

== STATUS BADGE COLORS ==
- tersimpan  → bg-emerald-50 text-emerald-700
- pending    → bg-amber-50 text-amber-700
- disetujui  → bg-indigo-50 text-indigo-700
- ditolak    → bg-red-50 text-red-700
- selesai    → bg-gray-100 text-gray-500
- habis      → bg-gray-50 text-gray-400
- kadaluarsa → bg-red-50 text-red-700
- hampir_penuh → bg-orange-50 text-orange-700

== KAPASITAS BAR COLORS ==
- 0–59%:   bg-emerald-500
- 60–79%:  bg-amber-400
- 80–100%: bg-red-500

== RELASI LUMBUNG ↔ PENGELOLA (MANY-TO-MANY) ==
Relasi antara lumbung dan pengelola adalah MANY-TO-MANY melalui tabel pivot `lumbung_pengelola`.
- Satu lumbung bisa punya banyak pengelola (pemilik_akun + anggota)
- Satu pengelola bisa mengelola banyak lumbung dengan peran berbeda

Kolom pivot: `peran` → nilai: 'pemilik_akun' | 'anggota'

Akses dari Blade:
  $lumbung->pengelola           → collection Pengelola (with pivot)
  $pengelola->pivot->peran      → 'pemilik_akun' atau 'anggota'
  $pengelola->lumbung           → collection Lumbung (with pivot)
  $lumbung->pivot->peran        → peran pengelola di lumbung tersebut

Badge peran yang konsisten:
  pemilik_akun → bg-indigo-50 text-indigo-700  label: "Pemilik Akun"
  anggota      → bg-gray-100 text-gray-600      label: "Anggota"

JANGAN gunakan $lumbung->id_pengelola atau $lumbung->pengelola_id — kolom ini TIDAK ADA.

== ATURAN BLADE WAJIB ==
2. Form edit HARUS punya @method('PUT') atau @method('DELETE')
3. Semua input HARUS punya old() untuk repopulate: value="{{ old('field', $model->field ?? '') }}"
4. Semua URL HARUS pakai route() helper — tidak boleh hard-code
5. Setiap halaman HARUS @extends layout yang sesuai rolenya
6. Tombol hapus WAJIB konfirmasi: onclick="return confirm('Yakin ingin menghapus?')"
7. @error('field') harus ada di bawah setiap input

== LAYOUT STRUKTUR ==
Sidebar (w-64, fixed, warna role) + Main content (ml-64, bg-gray-50 min-h-screen)
Topbar: breadcrumb kiri + nama user + tombol logout kanan
Page header: judul h1 + subtitle + action buttons

== TABEL PATTERN ==
- Container: bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden
- Header: bg-gray-50 border-b border-gray-200, text uppercase text-[11px] tracking-wider
- Row: hover:bg-gray-50 transition-colors, divided by divide-y divide-gray-100
- Kolom aksi: flex gap-2, icon-only dengan tooltip title=""

== FORM PATTERN ==
- Input: w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[role-color]-500 focus:border-transparent
- Label: block text-sm font-medium text-gray-700 mb-1.5
- Error: mt-1.5 text-xs text-red-600
- Submit button: warna sesuai role (indigo/emerald/amber)

== FLASH MESSAGE PATTERN ==
@if(session('success'))
  <div class="flex items-start gap-3 p-4 mb-6 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
    {{ session('success') }}
  </div>
@endif
@if($errors->any())
  <div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-lg">
    <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
      @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
  </div>
@endif

== PAGINATION PATTERN ==
<div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
  <p class="text-xs text-gray-500">
    Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} data
  </p>
  {{ $items->withQueryString()->links('vendor.pagination.tailwind') }}
</div>

Baca file SILPD-DESIGN.md di root project untuk panduan lengkap komponen.
```

---

## ═══════════════════════════════════════════
## SESI 1 — Layouts + Auth + Routes
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK DI ATAS TERLEBIH DAHULU]

Sekarang buat file-file berikut:

== FILE 1: resources/views/layouts/admin.blade.php ==
Layout utama untuk role admin. Berisi:
- <html>, <head> dengan meta charset, viewport, title @yield('title', 'SILPD'), Inter font dari Google Fonts, Tailwind CDN
- Sidebar indigo-600 fixed w-64 berisi:
  - Logo/brand: ikon 🌾 + teks "SILPD" putih bold + subtitle "Admin Desa" text-indigo-200
  - Nav groups dengan section header text-indigo-300 text-[11px] uppercase tracking-wider:
    GROUP "Utama": Dashboard (route: admin.dashboard)
    GROUP "Data Master": Petani (admin.petani.index), Kelompok Tani (admin.kelompok.index), Jenis Gabah (admin.jenis-gabah.index), Lumbung (admin.lumbung.index), Pengelola (admin.pengelola.index)
    GROUP "Operasional": Input Panen (admin.panen.index), Instruksi Simpan (admin.instruksi.index), Permintaan Ambil (admin.permintaan.index), Manajemen Akun (admin.akun.index)
    GROUP "Laporan": Stok Gabah (admin.laporan.stok), Laporan Panen (admin.laporan.panen), Laporan Pengambilan (admin.laporan.pengambilan), Rekap Petani (admin.laporan.rekap-petani)
  - Active state: request()->routeIs('admin.petani.*') → bg-white/20 text-white font-medium, inactive → text-white/70 hover:bg-white/10
  - Logout button di bawah sidebar
- Main content ml-64 bg-gray-50 min-h-screen:
  - Topbar: bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center
    Kiri: @yield('breadcrumb')
    Kanan: nama user (session('nama')) + badge "Admin" indigo + logout
  - Content area: p-6 @yield('content')
- @yield('scripts') sebelum </body>

== FILE 2: resources/views/layouts/pengelola.blade.php ==
Sama dengan admin layout tapi:
- Sidebar warna emerald-600
- Brand subtitle "Pengelola Lumbung"
- Nav items:
  Dashboard (pengelola.dashboard)
  Instruksi Penyimpanan (pengelola.instruksi.index)
  Pengeluaran Gabah (pengelola.pengeluaran.index)
  Monitor Stok (pengelola.stok.index)
- Badge "Pengelola" warna emerald
- Active state: bg-white/20 berdasarkan route pengelola.*

== FILE 3: resources/views/layouts/petani.blade.php ==
Sama dengan admin layout tapi:
- Sidebar warna amber-600
- Brand subtitle "Portal Petani"
- Nav items:
  Dashboard (petani.dashboard)
  Stok Gabah Saya (petani.stok.index)
  Permintaan Pengambilan (petani.permintaan.index)
- Badge "Petani" warna amber
- Active state: bg-white/20 berdasarkan route petani.*

== FILE 4: resources/views/auth/login.blade.php ==
Halaman login standalone (tanpa layout sidebar). Berisi:
- Full screen bg-gray-50 flex items-center justify-center min-h-screen
- Card login: bg-white rounded-2xl shadow-lg border border-gray-200 w-full max-w-md p-8
- Header card: 🌾 logo + "SILPD" text-2xl font-semibold tracking-tight + subtitle "Sistem Informasi Lumbung Padi Desa" text-gray-500
- Form POST ke route('login') dengan @csrf:
  - Input username (type text, autocomplete off)
  - Input password (type password, ada toggle show/hide dengan button + eye icon)
  - Tombol submit "Masuk ke Sistem" full width bg-indigo-600
- @if($errors->any()) tampilkan error alert merah
- Footer card: "Akun dibuat oleh Admin Desa" text-xs text-gray-400 text-center

== FILE 5: routes/web.php ==
Buat file routes lengkap dengan struktur:

// Auth routes (tanpa middleware)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes - middleware role:admin
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
  // Dashboard
  Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

  // Petani
  Route::resource('petani', Admin\PetaniController::class)->parameters(['petani' => 'id']);
  Route::get('petani/{id}', [Admin\PetaniController::class, 'show'])->name('petani.show');

  // Kelompok Tani
  Route::resource('kelompok', Admin\KelompokTaniController::class)->parameters(['kelompok' => 'id']);
  Route::get('kelompok/{id}', [Admin\KelompokTaniController::class, 'show'])->name('kelompok.show');

  // Jenis Gabah
  Route::resource('jenis-gabah', Admin\JenisGabahController::class)->parameters(['jenis-gabah' => 'id']);
  Route::get('jenis-gabah/{id}', [Admin\JenisGabahController::class, 'show'])->name('jenis-gabah.show');

  // Lumbung + Slot (nested resource)
  Route::resource('lumbung', Admin\LumbungController::class)->parameters(['lumbung' => 'id']);
  Route::resource('lumbung.slot', Admin\SlotLumbungController::class)
    ->parameters(['lumbung' => 'idLumbung', 'slot' => 'idSlot']);
  Route::get('lumbung/{idLumbung}/slot/{idSlot}/detail', [Admin\SlotLumbungController::class, 'showSlot'])->name('lumbung.slot.detail');

  // Pengelola
  Route::resource('pengelola', Admin\PengelolaController::class)->parameters(['pengelola' => 'id']);
  Route::post('pengelola/{id}/buat-akun', [Admin\PengelolaController::class, 'buatAkun'])->name('pengelola.buat-akun');
  Route::post('pengelola/{id}/reset-password', [Admin\PengelolaController::class, 'resetPassword'])->name('pengelola.reset-password');
  Route::delete('pengelola/{id}/hapus-akun', [Admin\PengelolaController::class, 'hapusAkun'])->name('pengelola.hapus-akun');

  // Akun
  Route::resource('akun', Admin\AkunController::class)->parameters(['akun' => 'id']);
  Route::post('akun/{id}/reset-password', [Admin\AkunController::class, 'resetPassword'])->name('akun.reset-password');
  Route::get('akun/ganti-password', [Admin\AkunController::class, 'formGantiPasswordSendiri'])->name('akun.ganti-password');
  Route::post('akun/ganti-password', [Admin\AkunController::class, 'gantiPasswordSendiri'])->name('akun.ganti-password.post');

  // Panen
  Route::resource('panen', Admin\PanenController::class)->parameters(['panen' => 'id'])->only(['index','create','store','show','destroy']);
  Route::get('panen/detail/{idDetail}/instruksi-manual', [Admin\PanenController::class, 'formInstruksiManual'])->name('panen.instruksi-manual');
  Route::post('panen/detail/{idDetail}/instruksi-manual', [Admin\PanenController::class, 'buatInstruksiManual'])->name('panen.instruksi-manual.post');
  Route::delete('panen/instruksi/{idInstruksi}/batal', [Admin\PanenController::class, 'batalInstruksi'])->name('panen.instruksi.batal');

  // Instruksi Penyimpanan (monitoring admin)
  Route::resource('instruksi', Admin\InstruksiController::class)->parameters(['instruksi' => 'id'])->only(['index','show','destroy']);
  Route::get('instruksi/{id}/pindah-slot', [Admin\InstruksiController::class, 'formPindahSlot'])->name('instruksi.pindah-slot');
  Route::post('instruksi/{id}/pindah-slot', [Admin\InstruksiController::class, 'pindahSlot'])->name('instruksi.pindah-slot.post');

  // Permintaan Pengambilan
  Route::resource('permintaan', Admin\PermintaanController::class)->parameters(['permintaan' => 'id'])->only(['index','show']);
  Route::post('permintaan/{id}/setujui', [Admin\PermintaanController::class, 'setujui'])->name('permintaan.setujui');
  Route::post('permintaan/{id}/tolak', [Admin\PermintaanController::class, 'tolak'])->name('permintaan.tolak');
  Route::post('permintaan/{id}/batal-setujui', [Admin\PermintaanController::class, 'batalSetujui'])->name('permintaan.batal-setujui');
  Route::post('permintaan/{id}/tolak-setelah-disetujui', [Admin\PermintaanController::class, 'tolakSetelahDisetujui'])->name('permintaan.tolak-setelah-disetujui');

  // Laporan
  Route::get('laporan/stok', [Admin\LaporanController::class, 'stok'])->name('laporan.stok');
  Route::get('laporan/panen', [Admin\LaporanController::class, 'panen'])->name('laporan.panen');
  Route::get('laporan/pengambilan', [Admin\LaporanController::class, 'pengambilan'])->name('laporan.pengambilan');
  Route::get('laporan/rekap-petani', [Admin\LaporanController::class, 'rekapPetani'])->name('laporan.rekap-petani');
  Route::get('laporan/ekspor/stok', [Admin\LaporanController::class, 'eksporStokCsv'])->name('laporan.ekspor.stok');
  Route::get('laporan/ekspor/panen', [Admin\LaporanController::class, 'eksporPanenCsv'])->name('laporan.ekspor.panen');
});

// Pengelola routes - middleware role:pengelola
Route::middleware('role:pengelola')->prefix('pengelola')->name('pengelola.')->group(function () {
  Route::get('/dashboard', [Pengelola\DashboardController::class, 'index'])->name('dashboard');
  Route::resource('instruksi', Pengelola\InstruksiPenyimpananController::class)->parameters(['instruksi' => 'id'])->only(['index','show']);
  Route::post('instruksi/{id}/konfirmasi', [Pengelola\InstruksiPenyimpananController::class, 'konfirmasi'])->name('instruksi.konfirmasi');
  Route::resource('pengeluaran', Pengelola\PengeluaranGabahController::class)->parameters(['pengeluaran' => 'id'])->only(['index','show']);
  Route::post('pengeluaran/{id}/konfirmasi', [Pengelola\PengeluaranGabahController::class, 'konfirmasi'])->name('pengeluaran.konfirmasi');
  Route::get('stok', [Pengelola\StokController::class, 'index'])->name('stok.index');
  Route::get('stok/slot/{idSlot}', [Pengelola\StokController::class, 'showSlot'])->name('stok.slot');
});

// Petani routes - middleware role:petani
Route::middleware('role:petani')->prefix('petani')->name('petani.')->group(function () {
  Route::get('/dashboard', [Petani\DashboardController::class, 'index'])->name('dashboard');
  Route::get('stok', [Petani\StokController::class, 'index'])->name('stok.index');
  Route::resource('permintaan', Petani\PermintaanController::class)->parameters(['permintaan' => 'id'])->only(['index','create','store','show']);
  Route::post('permintaan/{id}/batal', [Petani\PermintaanController::class, 'batal'])->name('permintaan.batal');
});

// Root redirect
Route::get('/', function() {
  if (!session('login_id')) return redirect()->route('login');
  return match(session('role')) {
    'admin' => redirect()->route('admin.dashboard'),
    'pengelola' => redirect()->route('pengelola.dashboard'),
    'petani' => redirect()->route('petani.dashboard'),
    default => redirect()->route('login'),
  };
});

Tambahkan semua use statement yang diperlukan di atas file:
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Pengelola;
use App\Http\Controllers\Petani;

Pastikan semua route diberi nama yang konsisten dengan yang digunakan di controller.
Buat semua 5 file tersebut sekarang.
```

---

## ═══════════════════════════════════════════
## SESI 2 — Admin: Dashboard + Petani + Kelompok + Jenis Gabah
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK TERLEBIH DAHULU]

Buat views berikut untuk role ADMIN. Semua menggunakan @extends('layouts.admin').
Controller telah ada di app/Http/Controllers/Admin/. Gunakan variabel yang dikirim controller.

== FILE 1: resources/views/admin/dashboard.blade.php ==
Variabel tersedia dari DashboardController@index:
$totalPetani, $totalLumbung, $totalSlot, $totalKelompok, $totalStokAktif, $panenBulanIni,
$permintaanPending (collection), $jumlahPermintaanPending, $instruksiPending (collection),
$jumlahInstruksiPending, $ringkasanLumbung (collection), $slotHampirPenuh (collection),
$gabahKadaluarsa (collection), $trenPanen (collection), $stokPerJenis (collection)

Susun halaman:
1. PAGE HEADER: "Dashboard" + subtitle "Selamat datang, {{ session('nama') }}"
2. STAT CARDS (4 kolom grid):
   - Total Petani ($totalPetani) → icon user-group, indigo
   - Total Stok Aktif ($totalStokAktif kg) → icon archive-box, emerald
   - Panen Bulan Ini ($panenBulanIni transaksi) → icon calendar, amber
   - Total Lumbung ($totalLumbung) → icon building-storefront, purple
3. ALERT NOTIFIKASI (jika ada):
   - @if($slotHampirPenuh->isNotEmpty()) → warning card "{{ $slotHampirPenuh->count() }} slot hampir penuh"
   - @if($gabahKadaluarsa->isNotEmpty()) → warning card "{{ $gabahKadaluarsa->count() }} lot gabah melewati batas simpan"
4. GRID 2 KOLOM:
   Kiri: Tabel $permintaanPending (5 row) dengan kolom: Petani, Jenis Gabah, Jumlah Diminta, Tanggal → link "Lihat Semua" ke admin.permintaan.index
   Kanan: Tabel $instruksiPending (5 row) dengan kolom: Petani, Slot, Jumlah, Tanggal → link "Lihat Semua" ke admin.instruksi.index
5. RINGKASAN KAPASITAS: Loop $ringkasanLumbung, tiap item tampilkan nama lumbung + capacity bar + angka
6. STOK PER JENIS: Simple table/list dari $stokPerJenis

== FILE 2: resources/views/admin/petani/index.blade.php ==
Variabel: $petaniList (paginate), $kelompokList (untuk filter dropdown)
Konten:
- Page header "Daftar Petani" + tombol "Tambah Petani" → route admin.petani.create
- Filter bar: search input (cari), dropdown kelompok (id_kelompok) → form GET
- Tabel: No, Nama Petani, Kelompok Tani, Luas Lahan (ha), Status Akun (login ada/tidak), Aksi (show/edit/delete)
- Status akun: badge "Punya Akun" emerald jika $petani->login ada, "Belum Ada Akun" gray jika tidak
- Tombol hapus: konfirmasi + form DELETE
- Pagination

== FILE 3: resources/views/admin/petani/create.blade.php ==
Variabel: $kelompokList, $persenLumbung
Form POST ke route admin.petani.store:
- Nama Petani (text, required)
- Kelompok Tani (select dari $kelompokList, required)
- Luas Lahan (number, step 0.01, satuan "Hektar" di kanan input, required)
- Divider "Buat Akun Login (Opsional)"
- Checkbox "Sekaligus buat akun login" id="buat_akun" name="buat_akun" value="1"
- Div #akun-fields hidden by default, tampil jika checkbox dicentang (toggle via JavaScript inline):
  - Username (text, regex hint: huruf, angka, titik, strip, underscore)
  - Password (password)
  - Konfirmasi Password (password name="password_confirmation")
- Submit "Simpan Petani" + Batal

== FILE 4: resources/views/admin/petani/edit.blade.php ==
Variabel: $petani (model), $kelompokList
Form PUT ke route admin.petani.update($petani->id_petani):
- Sama dengan create tapi tanpa bagian buat akun (akun dikelola di Manajemen Akun)
- Pre-fill semua field dengan old() + $petani->field

== FILE 5: resources/views/admin/petani/show.blade.php ==
Variabel: $petani (with kelompokTani, login), $riwayatPanen (paginate), $stokAktif (collection), $totalStok
Konten:
- Page header: nama petani + tombol Edit + tombol Kembali
- 2 card info: Profil Petani (nama, kelompok, luas lahan) | Info Akun (username/belum punya, role)
- Stat mini: Total Stok Aktif ($totalStok kg)
- Tabel Stok Aktif: Jenis Gabah, Slot, Lumbung, Jumlah, Tanggal Masuk, Umur (hari) — urutan FIFO
- Tabel Riwayat Panen: Tanggal, Jenis Gabah (comma separated dari detailPanen), Total Panen, + pagination

== FILE 6: resources/views/admin/kelompok/index.blade.php ==
Variabel: $kelompokList (paginate dengan withCount petani)
Tabel: No, Nama Kelompok, Jumlah Anggota, Aksi (show/edit/delete)
Tombol: "Tambah Kelompok"

== FILE 7: resources/views/admin/kelompok/create.blade.php ==
Form sederhana: Nama Kelompok (text)

== FILE 8: resources/views/admin/kelompok/edit.blade.php ==
Pre-fill $kelompok->nama_kelompok

== FILE 9: resources/views/admin/kelompok/show.blade.php ==
Variabel: $kelompok (withCount petani), $anggota (paginate), $totalStokKelompok
- Header: nama kelompok + jumlah anggota badge
- Stat: Total Stok Gabah Kelompok ($totalStokKelompok kg)
- Tabel anggota: Nama Petani, Luas Lahan, Status Akun

== FILE 10: resources/views/admin/jenis-gabah/index.blade.php ==
Variabel: $jenisGabahList (paginate), $stokPerJenis (collection keyed by id_jenis_gabah)
Tabel: No, Nama Jenis, Stok Tersimpan (dari $stokPerJenis[$item->id_jenis_gabah] ?? 0 kg), Aksi

== FILE 11: resources/views/admin/jenis-gabah/create.blade.php ==
Form: Nama Jenis (text, contoh: IR64, Ciherang, Mekongga)

== FILE 12: resources/views/admin/jenis-gabah/edit.blade.php ==
Pre-fill $jenisGabah->nama_jenis

== FILE 13: resources/views/admin/jenis-gabah/show.blade.php ==
Variabel: $jenisGabah, $stokPerLumbung (grouped), $totalStok, $petaniDenganStok
- Total stok card
- Tabel stok per lumbung (grouped by nama_lumbung)
- Tabel petani yang punya stok jenis ini

Buat semua 13 file tersebut.
```

---

## ═══════════════════════════════════════════
## SESI 3 — Admin: Lumbung + Slot + Pengelola + Akun
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK TERLEBIH DAHULU]

== FILE 1: resources/views/admin/lumbung/index.blade.php ==
Variabel: $lumbungList (paginate, tiap item sudah punya total_kapasitas, total_terpakai, persen_terpakai, pengelola)
Tabel: No, Nama Lumbung, Pengelola (badge tiap pengelola — indigo jika pemilik_akun, abu jika anggota, ambil dari $lumbung->pengelola), Kapasitas Total, Terpakai, Status Bar, Aksi

== FILE 2: resources/views/admin/lumbung/create.blade.php ==
Variabel: $pengelolaList
Form: Nama Lumbung (text)
Section "Pengelola Lumbung":
- Daftar checkbox dari $pengelolaList — setiap item punya nama + no HP
- Untuk setiap pengelola yang dicentang, ada radio "Peran": pemilik_akun | anggota
- Gunakan name="pengelola[{id}][checked]" dan name="pengelola[{id}][peran]"
- Hint: "Minimal satu pengelola dengan peran Pemilik Akun disarankan"

== FILE 3: resources/views/admin/lumbung/edit.blade.php ==
Variabel: $lumbung (with pengelola via pivot), $pengelolaList
Pre-fill nama lumbung.
Section "Pengelola Lumbung" (same structure as create):
- Checkbox + radio peran untuk setiap pengelola di $pengelolaList
- Pre-check pengelola yang sudah terdaftar di pivot, pre-select peran dari $lumbung->pengelola->pivot->peran
- Gunakan: @php $existing = $lumbung->pengelola->keyBy('id_pengelola'); @endphp lalu cek dengan $existing->has($p->id_pengelola)

== FILE 4: resources/views/admin/lumbung/show.blade.php ==
Variabel: $lumbung (with pengelola via pivot, slotLumbung), $totalKapasitas, $totalTersedia, $totalTerpakai, $persenTerpakai, $stokList, $gabahKadaluarsa, $slotHampirPenuh
- Capacity bar besar di bagian atas
- Section "Pengelola Lumbung": loop $lumbung->pengelola, tampilkan nama + no HP + badge peran
  (pemilik_akun → badge indigo "Pemilik Akun", anggota → badge gray "Anggota")
  gunakan $pengelola->pivot->peran untuk membaca peran dari pivot
- Tabel slot: Kode Slot, Kapasitas, Tersedia, Terpakai %, Capacity Bar mini, Aksi (detail slot)
- Alert jika $gabahKadaluarsa->isNotEmpty() atau $slotHampirPenuh->isNotEmpty()
- Tabel stok gabah dalam lumbung ini

== FILE 5: resources/views/admin/slot/index.blade.php ==
Variabel: $lumbung, $slotList (paginate, tiap item punya persen_terpakai, hampir_penuh)
Breadcrumb: Lumbung / [nama lumbung] / Slot
Tabel: Kode Slot, Kapasitas, Tersedia, Terpakai, Capacity Bar, Status (normal/hampir penuh), Aksi

== FILE 6: resources/views/admin/slot/create.blade.php ==
Variabel: $lumbung
Form: Kode Slot (text, uppercase hint), Kapasitas (number kg)
Keterangan: "Slot baru akan dimulai penuh kosong (kapasitas tersedia = kapasitas)"

== FILE 7: resources/views/admin/slot/edit.blade.php ==
Variabel: $lumbung, $slot, $terpakai
Form: Kode Slot, Kapasitas (min = $terpakai)
Warning: "Kapasitas tidak bisa dikurangi di bawah {{ $terpakai }} kg (sudah terpakai)"

== FILE 8: resources/views/admin/slot/show.blade.php ==
Variabel: $lumbung, $slot (dengan persen_terpakai), $gabahTersimpan (collection dengan is_kadaluarsa, umur_hari), $riwayatPenyimpanan (paginate), $batasHari
- Capacity bar besar
- Tabel gabah tersimpan (FIFO order): Petani, Jenis Gabah, Jumlah, Tanggal Masuk, Umur Simpan → badge merah jika is_kadaluarsa
- Tabel riwayat semua penyimpanan

== FILE 9: resources/views/admin/pengelola/index.blade.php ==
Variabel: $pengelolaList (paginate, with lumbung via pivot, login)
Filter: search text, dropdown status_akun (semua/punya_akun/belum_akun)
Tabel: No, Nama, No HP, Lumbung Dikelola (badge per lumbung — tampilkan nama_lumbung, tooltip/title menampilkan perannya), Status Akun, Aksi
Contoh kolom lumbung: loop $pengelola->lumbung → badge tiap lumbung, warna indigo jika pivot->peran == 'pemilik_akun', abu jika 'anggota'

== FILE 10: resources/views/admin/pengelola/create.blade.php ==
Form: Nama Pengelola, No HP (hint: 08xxx)
Section opsional buat akun: checkbox toggle → Username, Password, Konfirmasi Password

== FILE 11: resources/views/admin/pengelola/edit.blade.php ==
Pre-fill $pengelola (nama, no_hp saja — akun dikelola terpisah)

== FILE 12: resources/views/admin/pengelola/show.blade.php ==
Variabel: $pengelola (with lumbung via pivot, login), $ringkasanLumbung (collection)
- Info card: nama, no HP, username (jika ada) / badge "Belum punya akun"
- Tombol kondisional:
  - Jika belum punya akun: Tombol "Buat Akun Login" → form modal atau inline form (POST ke admin.pengelola.buat-akun)
  - Jika sudah punya akun: Tombol "Reset Password" (modal form) + Tombol "Hapus Akun" (DELETE)
- Tabel lumbung yang dikelola: Nama Lumbung, Peran (badge: "Pemilik Akun" indigo / "Anggota" gray dari $lumbung->pivot->peran), Jumlah Slot, Kapasitas, Terpakai %
  Gunakan: @foreach($pengelola->lumbung as $lumbung) ... $lumbung->pivot->peran ...

== FILE 13: resources/views/admin/akun/index.blade.php ==
Variabel: $akunList (paginate, tiap item punya nama_pemilik, label_role), $jumlahPerRole
Tabs filter: Semua | Petani ({{ $jumlahPerRole['petani'] ?? 0 }}) | Pengelola | Admin
Tabel: No, Username, Role (badge), Pemilik Akun, Aksi (show/edit/reset-pass/delete)

== FILE 14: resources/views/admin/akun/create.blade.php ==
Variabel: $role, $entitasTersedia
Tabs pilih role di atas form (query param ?role=petani/pengelola/admin)
Dropdown $entitasTersedia: tampilkan nama entitas
Form: Username, Password, Konfirmasi Password

== FILE 15: resources/views/admin/akun/edit.blade.php ==
Variabel: $akun (dengan nama_pemilik, label_role)
Form: hanya Username (readonly info: role dan pemilik)
Info card: "Untuk ganti password, gunakan fitur Reset Password"

== FILE 16: resources/views/admin/akun/show.blade.php ==
Variabel: $akun (dengan nama_pemilik, label_role)
Detail: Username, Role, Pemilik, Dibuat
Tombol: Edit Username | Reset Password (form modal) | Hapus Akun

== FILE 17: resources/views/admin/akun/ganti-password.blade.php ==
Form ganti password sendiri (admin):
- Password Lama, Password Baru, Konfirmasi Password Baru

Buat semua 17 file tersebut.
```

---

## ═══════════════════════════════════════════
## SESI 4 — Admin: Panen + Instruksi + Permintaan + Laporan
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK TERLEBIH DAHULU]

== FILE 1: resources/views/admin/panen/index.blade.php ==
Variabel: $panenList (paginate, with petani, detailPanen, instruksiPenyimpanan), $petaniList, $kelompokList, $totalPanenBulanIni
Filter: search (cari), id_petani, id_kelompok, dari, sampai, status_instruksi
Tabel: No, Tanggal Panen, Petani, Kelompok, Jenis Gabah (comma), Total Panen (kg), Status Instruksi (badge per detail), Aksi (show/delete)
Status instruksi: loop detailPanen, tiap detail cek instruksiPenyimpanan.first()->status

== FILE 2: resources/views/admin/panen/create.blade.php ==
Variabel: $petaniList, $jenisGabahList, $persenLumbung
Form POST ke admin.panen.store:
- Petani (select required)
- Tanggal Panen (date, max today)
- Section "Detail Panen" — DINAMIS:
  - Baris awal 1 baris: Jenis Gabah (select) + Jumlah Panen (number kg) + tombol hapus baris
  - Tombol "Tambah Jenis Gabah Lain" → JavaScript tambah baris baru
  - Setiap baris name: detail[0][id_jenis_gabah], detail[0][jumlah_panen]
  - Preview kalkulasi 3%: "= {{ $persenLumbung }}% → X kg untuk lumbung" (JavaScript hitung realtime)
- Submit "Simpan Data Panen"

== FILE 3: resources/views/admin/panen/show.blade.php ==
Variabel: $panen (with petani), $ringkasanDetail (collection), $totalPanen, $totalLumbung, $persenLumbung
- Header: "Panen #{{ $panen->id_panen }}" + tanggal + petani + kelompok
- Stat: Total Panen ($totalPanen kg) | Total untuk Lumbung ($totalLumbung kg, persenLumbung%)
- Tabel detail per jenis gabah:
  Kolom: Jenis Gabah, Jumlah Panen, Jumlah Lumbung, Status Instruksi, Slot Tujuan, Aksi
  - Jika status_instruksi == 'belum_dibuat': badge merah + tombol "Buat Instruksi Manual" → link admin.panen.instruksi-manual(idDetail)
  - Jika status_instruksi == 'pending': badge amber + info slot
  - Jika status_instruksi == 'selesai': badge emerald + info penyimpanan actual
  - Tombol batal instruksi (DELETE) jika pending

== FILE 4: resources/views/admin/panen/instruksi-manual.blade.php ==
Variabel: $detailPanen (with panen.petani, jenisGabah), $jumlahLumbung, $slotTersedia
Info: "{{ $detailPanen->jenisGabah->nama_jenis }} — {{ $jumlahLumbung }} kg perlu ditempatkan"
Form POST ke admin.panen.instruksi-manual.post(idDetail):
- Tabel/list $slotTersedia: Radio button | Nama Lumbung | Kode Slot | Kapasitas Tersedia | %
- Submit "Buat Instruksi"

== FILE 5: resources/views/admin/instruksi/index.blade.php ==
Variabel: $instruksiList (paginate), $jumlahPending, $jumlahSelesai, $lumbungList
Badge counter: "{{ $jumlahPending }} pending" di tab
Filter: status, id_lumbung, dari, sampai
Tabel: No, Tanggal, Petani, Jenis Gabah, Jumlah, Slot → Lumbung, Status (badge), Aksi (show/pindah-slot/delete)

== FILE 6: resources/views/admin/instruksi/show.blade.php ==
Variabel: $instruksi (with detailPanen.panen.petani, jenisGabah, slotLumbung.lumbung.pengelola), $penyimpanan (null jika pending)
- Detail instruksi lengkap
- Info pengelola yang bertanggung jawab: loop $instruksi->slotLumbung->lumbung->pengelola (many-to-many), tampilkan nama + badge peran (pivot->peran)
- Jika status pending: tampilkan tombol "Pindah Slot" → link admin.instruksi.pindah-slot

== FILE 7: resources/views/admin/instruksi/pindah-slot.blade.php ==
Variabel: $instruksi, $slotAlternatif
Info current slot, form pilih slot alternatif (radio + alasan textarea)

== FILE 8: resources/views/admin/permintaan/index.blade.php ==
Variabel: $permintaanList (paginate), $jumlahPerStatus, $petaniList, $kelompokList, $statusFilter
Tabs navigasi status: Pending ({{ $jumlahPerStatus['pending'] ?? 0 }}) | Disetujui | Selesai | Ditolak | Semua
Filter: petani, kelompok, tanggal
Tabel: No, Tanggal, Petani, Jenis Gabah, Jumlah Diminta, Lumbung/Slot, Status, Aksi (show)

== FILE 9: resources/views/admin/permintaan/show.blade.php ==
Variabel: $permintaan (with petani, penyimpananGabah.detailPanen.jenisGabah, slotLumbung, detailPengambilan), $rekomendasiFifo, $adaPelanggaranFifo, $totalStokJenisSama, $riwayatPermintaan
- Header: info permintaan + status badge
- Info petani + info gabah yang diminta (jenis, jumlah, slot, tanggal masuk gabah)
- Alasan pengambilan (dari detailPengambilan->alasan)
- FIFO WARNING: @if($adaPelanggaranFifo) tampilkan amber warning card + list $rekomendasiFifo
- Tombol aksi kondisional berdasarkan $permintaan->status:
  - pending: form Setujui (POST admin.permintaan.setujui + catatan textarea) + form Tolak (POST admin.permintaan.tolak + alasan required)
  - disetujui: form Batal Setujui (POST admin.permintaan.batal-setujui + alasan) + form Tolak Paksa
  - selesai/ditolak: readonly, tampilkan info saja
- Riwayat permintaan petani sebelumnya (5 item)

== FILE 10: resources/views/admin/laporan/stok.blade.php ==
Variabel: $stokList (paginate), $rekapPerLumbung, $rekapPerJenis, $totalStokKeseluruhan, $jumlahKadaluarsa, $lumbungList, $jenisGabahList, $petaniList, $batasHari
- Stat cards: Total Stok ({{ number_format($totalStokKeseluruhan) }} kg) | Lot Kadaluarsa ({{ $jumlahKadaluarsa }})
- Rekapitulasi per lumbung: tabel dengan capacity bar
- Rekapitulasi per jenis gabah: tabel
- Filter + Tabel detail stok dengan flag kadaluarsa (row bg-red-50 jika is_kadaluarsa)
- Tombol "Ekspor CSV" → admin.laporan.ekspor.stok

== FILE 11: resources/views/admin/laporan/panen.blade.php ==
Variabel: $panenList (paginate), $rekapPerPetani, $rekapPerJenis, $rekapPerKelompok, $totalPanenKg, $totalLumbungKg, $persenLumbung, $dari, $sampai, $petaniList, $kelompokList
- Date range filter (dari, sampai) + petani + kelompok filter
- Stat: Total Panen ($totalPanenKg kg) | Total ke Lumbung ($totalLumbungKg kg, $persenLumbung%)
- Tabel rekap per petani (sortable by total)
- Tabel rekap per jenis gabah
- Tabel detail panen + pagination
- Tombol "Ekspor CSV"

== FILE 12: resources/views/admin/laporan/pengambilan.blade.php ==
Variabel: $pengambilanList, $rekapPerPetani, $rekapPerJenis, $totalDiambilKg, $jumlahTransaksi, $jumlahDitolak, $dari, $sampai, filter lists
- Date range filter
- Stats: Total Diambil | Jumlah Transaksi | Ditolak dalam periode
- Tabel rekap per petani
- Tabel detail pengambilan

Buat semua 12 file tersebut.
```

---

## ═══════════════════════════════════════════
## SESI 5 — Pengelola Views + Petani Views
## ═══════════════════════════════════════════

```
[PASTE CONTEXT BLOCK TERLEBIH DAHULU]

== PENGELOLA VIEWS (semua @extends('layouts.pengelola')) ==

FILE 1: resources/views/pengelola/dashboard.blade.php
Variabel: $instruksiPending (collection, 5 item), $jumlahInstruksiPending, $permintaanDisetujui (collection), $jumlahPermintaanDisetujui, $slotHampirPenuh, $gabahKadaluarsa, $ringkasanLumbung
- Sapa "Selamat datang, {{ session('nama') }}"
- Stat cards: Instruksi Pending | Pengeluaran Pending | Slot Hampir Penuh | Lot Kadaluarsa
- Alert warnings jika ada slot hampir penuh atau gabah kadaluarsa
- Tabel instruksi pending (5 row): Petani, Jenis Gabah, Jumlah, Slot Tujuan, Tanggal → link konfirmasi
- Tabel permintaan disetujui (5 row): Petani, Jenis Gabah, Jumlah, Slot → link konfirmasi
- Ringkasan kapasitas lumbung dengan capacity bar

FILE 2: resources/views/pengelola/instruksi/index.blade.php
Variabel: $instruksiList (paginate), $jumlahPending
Filter status (pending/selesai)
Tabel: Tanggal, Petani, Jenis Gabah, Jumlah, Slot Tujuan, Status, Aksi (show)
Badge counter jumlah pending di header

FILE 3: resources/views/pengelola/instruksi/show.blade.php
Variabel: $instruksi (with semua relasi)
- Detail lengkap: petani, jenis gabah, jumlah, slot tujuan, lumbung
- Jika status PENDING: Form konfirmasi dengan input tanggal_masuk (date) + catatan opsional → POST pengelola.instruksi.konfirmasi
- Validasi kapasitas slot ditampilkan: "Kapasitas tersedia slot: X kg, dibutuhkan: Y kg"
- Jika status SELESAI: tampilkan info penyimpanan yang terbentuk

FILE 4: resources/views/pengelola/pengeluaran/index.blade.php
Variabel: $permintaanList (paginate), $jumlahMenunggu, $statusFilter
Tabs: Menunggu Pengeluaran (disetujui) | Selesai
Tabel: Tanggal, Petani, Jenis Gabah, Jumlah Akan Dikeluarkan, Slot Asal, Aksi

FILE 5: resources/views/pengelola/pengeluaran/show.blade.php
Variabel: $permintaan (with semua relasi)
- Info permintaan + detail gabah yang akan dikeluarkan
- Slot asal + jumlah sisa setelah pengeluaran (preview: $penyimpanan->jumlah - $totalKeluar)
- Jika status disetujui: Form konfirmasi dengan tanggal_pengeluaran + catatan → POST pengelola.pengeluaran.konfirmasi
- Warning jika stok tidak mencukupi

FILE 6: resources/views/pengelola/stok/index.blade.php
Variabel: $lumbungList, $stokList (paginate), $ringkasanKapasitas, $jumlahKadaluarsa
Filter: id_lumbung, id_jenis_gabah
- Ringkasan kapasitas per lumbung (capacity bars)
- Tabel stok gabah tersimpan: Lumbung, Slot, Petani, Jenis Gabah, Jumlah, Tanggal Masuk, Umur (hari), Status (badge kadaluarsa jika lewat batas)
- Row highlight bg-red-50 jika is_kadaluarsa

== PETANI VIEWS (semua @extends('layouts.petani')) ==

FILE 7: resources/views/petani/dashboard.blade.php
Variabel: $petani (with kelompokTani), $totalGabahTersimpan, $stokPerJenis, $permintaanTerbaru, $permintaanAktif, $panenTerbaru
- Sapa "Selamat datang, {{ session('nama') }}"
- Stat cards: Total Gabah Saya ($totalGabahTersimpan kg) | Permintaan Aktif ($permintaanAktif)
- Card profil: nama, kelompok tani, luas lahan
- Stok per jenis: simple grid/list dari $stokPerJenis
- Tabel permintaan terbaru: Tanggal, Jenis Gabah, Jumlah, Status badge
- Tabel panen terbaru: Tanggal, Jenis Gabah (comma), Total

FILE 8: resources/views/petani/stok/index.blade.php
Variabel: $stokList (paginate), $totalTersimpan, $rekapPerJenis, $jenisGabahList, $statusFilter
- Stat: Total Tersimpan ($totalTersimpan kg)
- Rekap per jenis gabah: simple cards/list (nama jenis + total kg + jumlah lot)
- Filter: status (tersimpan/semua/habis), jenis gabah
- Tabel stok: Jenis Gabah, Lumbung, Slot, Jumlah, Tanggal Masuk, Umur Simpan, Status badge
- Row highlight amber-50 jika tanggal_masuk sudah lama
- Keterangan FIFO: "Gabah yang paling lama akan diambil terlebih dahulu saat pengambilan"

FILE 9: resources/views/petani/permintaan/index.blade.php
Variabel: $permintaanList (paginate)
- Tombol "Ajukan Permintaan Pengambilan" → petani.permintaan.create
- Tabel: Tanggal, Jenis Gabah, Jumlah Diminta, Alasan (truncated), Status badge, Aksi (show + batal jika pending)

FILE 10: resources/views/petani/permintaan/create.blade.php
Variabel: $stokTersimpan (collection, FIFO order)
- Info: "Pilih gabah yang ingin Anda ambil. Sistem merekomendasikan mengambil dari lot yang paling lama tersimpan terlebih dahulu."
- List/tabel $stokTersimpan: Radio | Jenis Gabah | Lumbung | Slot | Jumlah Tersisa | Tanggal Masuk | Umur
- Highlight baris paling atas (terlama) dengan label "Rekomendasi FIFO" badge emerald
- Input: Jumlah yang ingin diambil (number, max = stok yang dipilih, diisi otomatis via JS saat radio dipilih)
- Textarea: Alasan Pengambilan (min 10 char, placeholder: "Gagal panen, kebutuhan konsumsi...")
- Submit "Ajukan Permintaan" + Batal

FILE 11: resources/views/petani/permintaan/show.blade.php
Variabel: $permintaan (with semua relasi)
- Status badge besar di header
- Detail: jenis gabah, jumlah diminta, lumbung/slot, tanggal masuk gabah
- Alasan pengambilan
- Status tracker sederhana: Diajukan → Menunggu Persetujuan → Disetujui → Selesai
- Jika pending: tombol "Batalkan Permintaan" (POST petani.permintaan.batal dengan konfirmasi)
- Jika ditolak: tampilkan alasan penolakan dari kolom alasan

Buat semua 11 file tersebut.
```

---

## CATATAN PENTING UNTUK AI AGENT

1. **Jangan skip field `old()`** — setiap input wajib punya
2. **Semua angka kg gunakan `number_format()`** bukan raw number
3. **Semua tanggal format**: `{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}`
4. **Icon SVG bisa diambil dari heroicons.com** — gunakan versi `outline` 24px
5. **Jika ragu soal variabel**, lihat controller yang sudah ada di `app/Http/Controllers/`
6. **Konsisten dengan nama route** yang sudah didefinisikan di SESI 1 routes/web.php
