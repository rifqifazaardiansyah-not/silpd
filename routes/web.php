<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Pengelola;
use App\Http\Controllers\Petani;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Debug routes (hapus setelah selesai debugging)
Route::prefix('debug')->group(function () {
    Route::get('/check-login', [\App\Http\Controllers\DebugController::class, 'checkLogin']);
    Route::post('/check-password', [\App\Http\Controllers\DebugController::class, 'checkPassword']);
    Route::get('/check-session', [\App\Http\Controllers\DebugController::class, 'checkSession']);
    Route::get('/all-logins', [\App\Http\Controllers\DebugController::class, 'allLogins']);
});

// Admin routes - middleware role:admin
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Petani
    Route::resource('petani', Admin\PetaniController::class)->parameters(['petani' => 'id']);
    Route::get('petani/{id}', [Admin\PetaniController::class, 'show'])->name('petani.show');
    
    // API untuk Tom Select autocomplete
    Route::get('api/petani/search', [Admin\PetaniController::class, 'apiSearch'])->name('api.petani.search');
    Route::get('api/petani/{id}', [Admin\PetaniController::class, 'apiShow'])->name('api.petani.show');

    // Kelompok Tani
    Route::resource('kelompok', Admin\KelompokTaniController::class)->parameters(['kelompok' => 'id']);
    Route::get('kelompok/{id}', [Admin\KelompokTaniController::class, 'show'])->name('kelompok.show');

    // Jenis Gabah
    Route::resource('jenis-gabah', Admin\JenisGabahController::class)->parameters(['jenis-gabah' => 'id']);
    Route::get('jenis-gabah/{id}', [Admin\JenisGabahController::class, 'show'])->name('jenis-gabah.show');

    // Lumbung + Slot (nested resource)
    Route::resource('lumbung', Admin\LumbungController::class)->parameters(['lumbung' => 'id']);
    Route::resource('lumbung.slot', Admin\SlotLumbungController::class)->parameters(['lumbung' => 'idLumbung', 'slot' => 'idSlot']);
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
    Route::resource('panen', Admin\PanenController::class)->parameters(['panen' => 'id'])->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('panen/detail/{idDetail}/instruksi-manual', [Admin\PanenController::class, 'formInstruksiManual'])->name('panen.instruksi-manual');
    Route::post('panen/detail/{idDetail}/instruksi-manual', [Admin\PanenController::class, 'buatInstruksiManual'])->name('panen.instruksi-manual.post');
    Route::delete('panen/instruksi/{idInstruksi}/batal', [Admin\PanenController::class, 'batalInstruksi'])->name('panen.instruksi.batal');

    // Instruksi Penyimpanan (monitoring admin)
    Route::resource('instruksi', Admin\InstruksiController::class)->parameters(['instruksi' => 'id'])->only(['index', 'show', 'destroy']);
    Route::get('instruksi/{id}/pindah-slot', [Admin\InstruksiController::class, 'formPindahSlot'])->name('instruksi.pindah-slot');
    Route::post('instruksi/{id}/pindah-slot', [Admin\InstruksiController::class, 'pindahSlot'])->name('instruksi.pindah-slot.post');

    // Permintaan Pengambilan
    Route::resource('permintaan', Admin\PermintaanController::class)->parameters(['permintaan' => 'id'])->only(['index', 'show']);
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

    Route::resource('instruksi', Pengelola\InstruksiPenyimpananController::class)->parameters(['instruksi' => 'id'])->only(['index', 'show']);
    Route::post('instruksi/{id}/konfirmasi', [Pengelola\InstruksiPenyimpananController::class, 'konfirmasi'])->name('instruksi.konfirmasi');

    Route::resource('pengeluaran', Pengelola\PengeluaranGabahController::class)->parameters(['pengeluaran' => 'id'])->only(['index', 'show']);
    Route::post('pengeluaran/{id}/konfirmasi', [Pengelola\PengeluaranGabahController::class, 'konfirmasi'])->name('pengeluaran.konfirmasi');

    Route::get('stok', [Pengelola\StokController::class, 'index'])->name('stok.index');
    Route::get('stok/slot/{idSlot}', [Pengelola\StokController::class, 'showSlot'])->name('stok.slot');
});

// Petani routes - middleware role:petani
Route::middleware('role:petani')->prefix('petani')->name('petani.')->group(function () {
    Route::get('/dashboard', [Petani\DashboardController::class, 'index'])->name('dashboard');

    Route::get('stok', [Petani\StokController::class, 'index'])->name('stok.index');

    Route::resource('permintaan', Petani\PermintaanController::class)->parameters(['permintaan' => 'id'])->only(['index', 'create', 'store', 'show']);
    Route::post('permintaan/{id}/batal', [Petani\PermintaanController::class, 'batal'])->name('permintaan.batal');
});

// Root redirect - jika sudah login redirect ke dashboard, jika belum ke landing page
Route::get('/', function () {
    if (!session('login_id')) {
        // Jika tidak ada session, arahkan ke landing page (TanStack)
        // Karena / di-proxy ke TanStack oleh Nginx, ini tidak akan tercapai saat domain silpd.test
        // Tapi jika diakses via PHP server, redirect ke login
        return redirect()->route('login');
    }

    return match (session('role')) {
        'admin' => redirect()->route('admin.dashboard'),
        'pengelola' => redirect()->route('pengelola.dashboard'),
        'petani' => redirect()->route('petani.dashboard'),
        default => redirect()->route('login'),
    };
});
