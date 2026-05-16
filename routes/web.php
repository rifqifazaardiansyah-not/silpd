<?php



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

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PanenController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\PetaniController;
use App\Http\Controllers\Admin\KelompokTaniController;
use App\Http\Controllers\Admin\JenisGabahController;
use App\Http\Controllers\Admin\LumbungController;
use App\Http\Controllers\Admin\SlotLumbungController;
use App\Http\Controllers\Admin\PengelolaController;
use App\Http\Controllers\Admin\InstruksiController;
use App\Http\Controllers\Admin\PermintaanController as AdminPermintaanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Pengelola\DashboardController as PengelolaDashboardController;
use App\Http\Controllers\Pengelola\InstruksiPenyimpananController;
use App\Http\Controllers\Pengelola\StokController as PengelolaStokController;
use App\Http\Controllers\Pengelola\PengeluaranGabahController;
use App\Http\Controllers\Petani\DashboardController as PetaniDashboardController;
use App\Http\Controllers\Petani\StokController as PetaniStokController;
use App\Http\Controllers\Petani\PermintaanController as PetaniPermintaanController;
use Illuminate\Support\Facades\Route;
// Halaman Awal
Route::get('/', function () {
    return view('welcome');
});

// ===== LOGIN & LOGOUT =====
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// ===== ADMIN ROUTES =====
Route::middleware(['ensure_login', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Panen
    Route::resource('panen', PanenController::class)->names([
        'index' => 'panen.index',
        'create' => 'panen.create',
        'store' => 'panen.store',
        'show' => 'panen.show',
        'destroy' => 'panen.destroy',
    ]);
    Route::get('panen/{id}/form-instruksi-manual/{idDetail}', [PanenController::class, 'formInstruksiManual'])->name('panen.form-instruksi');
    Route::post('panen/instruksi-manual', [PanenController::class, 'buatInstruksiManual'])->name('panen.buat-instruksi');
    Route::post('panen/batal-instruksi/{idInstruksi}', [PanenController::class, 'batalInstruksi'])->name('panen.batal-instruksi');

    // Manajemen Akun
    Route::resource('akun', AkunController::class)->names([
        'index' => 'akun.index',
        'create' => 'akun.create',
        'store' => 'akun.store',
        'show' => 'akun.show',
        'edit' => 'akun.edit',
        'update' => 'akun.update',
        'destroy' => 'akun.destroy',
    ]);

    // Manajemen Petani
    Route::resource('petani', PetaniController::class)->names([
        'index' => 'petani.index',
        'create' => 'petani.create',
        'store' => 'petani.store',
        'show' => 'petani.show',
        'edit' => 'petani.edit',
        'update' => 'petani.update',
        'destroy' => 'petani.destroy',
    ]);

    // Manajemen Kelompok Tani
    Route::resource('kelompok-tani', KelompokTaniController::class)->names([
        'index' => 'kelompok-tani.index',
        'create' => 'kelompok-tani.create',
        'store' => 'kelompok-tani.store',
        'show' => 'kelompok-tani.show',
        'edit' => 'kelompok-tani.edit',
        'update' => 'kelompok-tani.update',
        'destroy' => 'kelompok-tani.destroy',
    ]);

    // Manajemen Jenis Gabah
    Route::resource('jenis-gabah', JenisGabahController::class)->names([
        'index' => 'jenis-gabah.index',
        'create' => 'jenis-gabah.create',
        'store' => 'jenis-gabah.store',
        'show' => 'jenis-gabah.show',
        'edit' => 'jenis-gabah.edit',
        'update' => 'jenis-gabah.update',
        'destroy' => 'jenis-gabah.destroy',
    ]);

    // Manajemen Lumbung
    Route::resource('lumbung', LumbungController::class)->names([
        'index' => 'lumbung.index',
        'create' => 'lumbung.create',
        'store' => 'lumbung.store',
        'show' => 'lumbung.show',
        'edit' => 'lumbung.edit',
        'update' => 'lumbung.update',
        'destroy' => 'lumbung.destroy',
    ]);

    // Manajemen Slot Lumbung
    Route::resource('slot-lumbung', SlotLumbungController::class)->names([
        'index' => 'slot-lumbung.index',
        'create' => 'slot-lumbung.create',
        'store' => 'slot-lumbung.store',
        'show' => 'slot-lumbung.show',
        'edit' => 'slot-lumbung.edit',
        'update' => 'slot-lumbung.update',
        'destroy' => 'slot-lumbung.destroy',
    ]);

    // Manajemen Pengelola
    Route::resource('pengelola', PengelolaController::class)->names([
        'index' => 'pengelola.index',
        'create' => 'pengelola.create',
        'store' => 'pengelola.store',
        'show' => 'pengelola.show',
        'edit' => 'pengelola.edit',
        'update' => 'pengelola.update',
        'destroy' => 'pengelola.destroy',
    ]);

    // Manajemen Instruksi Penyimpanan
    Route::resource('instruksi', InstruksiController::class)->names([
        'index' => 'instruksi.index',
        'show' => 'instruksi.show',
    ]);

    // Manajemen Permintaan Pengambilan
    Route::resource('permintaan', AdminPermintaanController::class)->names([
        'index' => 'permintaan.index',
        'show' => 'permintaan.show',
    ]);
    Route::post('permintaan/validasi/{id}', [AdminPermintaanController::class, 'validasi'])->name('permintaan.validasi');
    Route::post('permintaan/tolak/{id}', [AdminPermintaanController::class, 'tolak'])->name('permintaan.tolak');

    // Laporan
    Route::resource('laporan', LaporanController::class)->names([
        'index' => 'laporan.index',
    ]);
});

// ===== PENGELOLA ROUTES =====
Route::middleware(['ensure_login', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PengelolaDashboardController::class, 'index'])->name('dashboard');

    // Instruksi Penyimpanan
    Route::resource('instruksi', InstruksiPenyimpananController::class)->names([
        'index' => 'instruksi.index',
        'show' => 'instruksi.show',
    ]);
    Route::post('instruksi/konfirmasi/{id}', [InstruksiPenyimpananController::class, 'konfirmasi'])->name('instruksi.konfirmasi');

    // Stok Lumbung
    Route::resource('stok', PengelolaStokController::class)->names([
        'index' => 'stok.index',
        'show' => 'stok.show',
    ]);

    // Pengeluaran Gabah
    Route::resource('pengeluaran', PengeluaranGabahController::class)->names([
        'index' => 'pengeluaran.index',
        'show' => 'pengeluaran.show',
    ]);
    Route::post('pengeluaran/selesai/{id}', [PengeluaranGabahController::class, 'selesai'])->name('pengeluaran.selesai');
});

// ===== PETANI ROUTES =====
Route::middleware(['ensure_login', 'role:petani'])->prefix('petani')->name('petani.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PetaniDashboardController::class, 'index'])->name('dashboard');

    // Stok Gabah Milik Petani
    Route::resource('stok', PetaniStokController::class)->names([
        'index' => 'stok.index',
        'show' => 'stok.show',
    ]);

    // Permintaan Pengambilan
    Route::resource('permintaan', PetaniPermintaanController::class)->names([
        'index' => 'permintaan.index',
        'create' => 'permintaan.create',
        'store' => 'permintaan.store',
        'show' => 'permintaan.show',
    ]);
    Route::post('permintaan/batal/{id}', [PetaniPermintaanController::class, 'batal'])->name('permintaan.batal');
});
