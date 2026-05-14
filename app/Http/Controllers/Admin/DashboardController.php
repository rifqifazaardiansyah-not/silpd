<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstruksiPenyimpanan;
use App\Models\JenisGabah;
use App\Models\Lumbung;
use App\Models\Panen;
use App\Models\Petani;
use App\Models\PermintaanPengambilan;
use App\Models\PenyimpananGabah;
use App\Models\SlotLumbung;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard utama admin desa.
     *
     * Menampilkan:
     * - Statistik global (total petani, total panen, total stok, jumlah lumbung)
     * - Permintaan pengambilan yang perlu divalidasi (status pending)
     * - Instruksi penyimpanan yang belum dikonfirmasi pengelola
     * - Notifikasi sistem (slot hampir penuh, gabah kadaluarsa)
     * - Ringkasan stok per jenis gabah
     * - Grafik panen 6 bulan terakhir
     */
    public function index()
    {
        // ── Statistik global ──────────────────────────────────────────────────
        $totalPetani   = Petani::count();
        $totalLumbung  = Lumbung::count();
        $totalSlot     = SlotLumbung::count();

        $totalPanenBulanIni = Panen::whereMonth('tanggal_panen', Carbon::now()->month)
            ->whereYear('tanggal_panen', Carbon::now()->year)
            ->count();

        $totalGabahTersimpan = PenyimpananGabah::where('status', 'tersimpan')->sum('jumlah');

        // ── Permintaan pengambilan pending (harus divalidasi admin) ───────────
        $permintaanPending = PermintaanPengambilan::where('status', 'pending')
            ->with([
                'petani',
                'penyimpananGabah.detailPanen.jenisGabah',
                'penyimpananGabah.slotLumbung.lumbung',
                'detailPengambilan',
            ])
            ->orderBy('tanggal_permintaan')
            ->take(10)
            ->get();

        $jumlahPending = PermintaanPengambilan::where('status', 'pending')->count();

        // ── Instruksi penyimpanan yang belum dikonfirmasi ─────────────────────
        $instruksiPending = InstruksiPenyimpanan::where('status', 'pending')
            ->with([
                'detailPanen.panen.petani',
                'detailPanen.jenisGabah',
                'slotLumbung.lumbung',
            ])
            ->orderBy('tanggal_instruksi')
            ->take(5)
            ->get();

        $jumlahInstruksiPending = InstruksiPenyimpanan::where('status', 'pending')->count();

        // ── Notifikasi: slot hampir penuh ─────────────────────────────────────
        $thresholdKapasitas = config('silpd.threshold_kapasitas_persen', 20);

        $slotHampirPenuh = SlotLumbung::whereRaw('kapasitas_tersedia / kapasitas * 100 < ?', [$thresholdKapasitas])
            ->with('lumbung')
            ->get();

        // ── Notifikasi: gabah terlalu lama disimpan ───────────────────────────
        $batasHari   = config('silpd.batas_hari_simpan', 90);
        $batasDate   = Carbon::now()->subDays($batasHari);

        $gabahKadaluarsa = PenyimpananGabah::where('status', 'tersimpan')
            ->where('tanggal_masuk', '<=', $batasDate)
            ->with([
                'detailPanen.jenisGabah',
                'detailPanen.panen.petani',
                'slotLumbung.lumbung',
            ])
            ->orderBy('tanggal_masuk')
            ->take(5)
            ->get();

        $jumlahGabahKadaluarsa = PenyimpananGabah::where('status', 'tersimpan')
            ->where('tanggal_masuk', '<=', $batasDate)
            ->count();

        // ── Ringkasan stok per jenis gabah ────────────────────────────────────
        $stokPerJenis = PenyimpananGabah::where('status', 'tersimpan')
            ->join('detail_panen', 'penyimpanan_gabah.id_detail', '=', 'detail_panen.id_detail')
            ->join('jenis_gabah', 'detail_panen.id_jenis_gabah', '=', 'jenis_gabah.id_jenis_gabah')
            ->selectRaw('jenis_gabah.nama_jenis, SUM(penyimpanan_gabah.jumlah) as total_stok')
            ->groupBy('jenis_gabah.id_jenis_gabah', 'jenis_gabah.nama_jenis')
            ->orderByDesc('total_stok')
            ->get();

        // ── Data grafik panen 6 bulan terakhir ───────────────────────────────
        $grafikPanen = collect();
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $totalPanen = Panen::whereMonth('tanggal_panen', $bulan->month)
                ->whereYear('tanggal_panen', $bulan->year)
                ->count();

            $grafikPanen->push([
                'bulan'       => $bulan->translatedFormat('M Y'),
                'total_panen' => $totalPanen,
            ]);
        }

        // ── Kapasitas lumbung keseluruhan ─────────────────────────────────────
        $totalKapasitas  = SlotLumbung::sum('kapasitas');
        $totalTersedia   = SlotLumbung::sum('kapasitas_tersedia');
        $totalTerpakai   = $totalKapasitas - $totalTersedia;
        $persenTerpakai  = $totalKapasitas > 0
            ? round(($totalTerpakai / $totalKapasitas) * 100, 1)
            : 0;

        return view('admin.dashboard', compact(
            'totalPetani',
            'totalLumbung',
            'totalSlot',
            'totalPanenBulanIni',
            'totalGabahTersimpan',
            'permintaanPending',
            'jumlahPending',
            'instruksiPending',
            'jumlahInstruksiPending',
            'slotHampirPenuh',
            'gabahKadaluarsa',
            'jumlahGabahKadaluarsa',
            'stokPerJenis',
            'grafikPanen',
            'totalKapasitas',
            'totalTersedia',
            'totalTerpakai',
            'persenTerpakai',
        ));
    }
}