<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\InstruksiPenyimpanan;
use App\Models\Lumbung;
use App\Models\PermintaanPengambilan;
use App\Models\PenyimpananGabah;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard pengelola.
     *
     * Menampilkan:
     * - Jumlah instruksi penyimpanan pending
     * - Jumlah permintaan pengambilan yang sudah disetujui (siap dikeluarkan)
     * - Notifikasi slot hampir penuh (kapasitas tersedia < 20%)
     * - Notifikasi gabah terlalu lama disimpan (> 90 hari)
     * - Ringkasan kapasitas lumbung yang dikelola
     */
    public function index()
    {
        $idPengelola = session('ref_id');

        // Lumbung yang dikelola oleh pengelola ini
        $lumbungList = Lumbung::where('id_pengelola', $idPengelola)
            ->with('slotLumbung')
            ->get();

        $idLumbungList = $lumbungList->pluck('id_lumbung');

        // Slot dari lumbung yang dikelola
        $slotIds = SlotLumbung::whereIn('id_lumbung', $idLumbungList)
            ->pluck('id_slot');

        // Instruksi penyimpanan pending
        $instruksiPending = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)
            ->where('status', 'pending')
            ->with(['detailPanen.panen.petani', 'detailPanen.jenisGabah', 'slotLumbung.lumbung'])
            ->orderBy('tanggal_instruksi')
            ->take(5)
            ->get();

        $jumlahInstruksiPending = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)
            ->where('status', 'pending')
            ->count();

        // Permintaan pengambilan yang disetujui (belum selesai dikeluarkan)
        $permintaanDisetujui = PermintaanPengambilan::whereHas('penyimpananGabah.slotLumbung', function ($q) use ($slotIds) {
                $q->whereIn('id_slot', $slotIds);
            })
            ->where('status', 'disetujui')
            ->with(['petani', 'penyimpananGabah.slotLumbung'])
            ->orderBy('tanggal_permintaan')
            ->take(5)
            ->get();

        $jumlahPermintaanDisetujui = PermintaanPengambilan::whereHas('penyimpananGabah.slotLumbung', function ($q) use ($slotIds) {
                $q->whereIn('id_slot', $slotIds);
            })
            ->where('status', 'disetujui')
            ->count();

        // Notifikasi slot hampir penuh (kapasitas tersedia < 20% dari kapasitas total)
        $slotHampirPenuh = SlotLumbung::whereIn('id_slot', $slotIds)
            ->whereRaw('kapasitas_tersedia / kapasitas * 100 < 20')
            ->with('lumbung')
            ->get();

        // Notifikasi gabah terlalu lama disimpan (> 90 hari)
        $batasHari    = config('silpd.batas_hari_simpan', 90);
        $batasDate    = Carbon::now()->subDays($batasHari);

        $gabahKadaluarsa = PenyimpananGabah::whereIn('id_slot', $slotIds)
            ->where('status', 'tersimpan')
            ->where('tanggal_masuk', '<=', $batasDate)
            ->with(['detailPanen.jenisGabah', 'detailPanen.panen.petani', 'slotLumbung.lumbung'])
            ->orderBy('tanggal_masuk')
            ->get();

        // Ringkasan kapasitas per lumbung
        $ringkasanLumbung = $lumbungList->map(function ($lumbung) {
            $totalKapasitas   = $lumbung->slotLumbung->sum('kapasitas');
            $totalTersedia    = $lumbung->slotLumbung->sum('kapasitas_tersedia');
            $totalTerpakai    = $totalKapasitas - $totalTersedia;
            $persenTerpakai   = $totalKapasitas > 0
                ? round(($totalTerpakai / $totalKapasitas) * 100, 1)
                : 0;

            return [
                'lumbung'         => $lumbung,
                'total_kapasitas' => $totalKapasitas,
                'total_tersedia'  => $totalTersedia,
                'total_terpakai'  => $totalTerpakai,
                'persen_terpakai' => $persenTerpakai,
            ];
        });

        return view('pengelola.dashboard', compact(
            'instruksiPending',
            'jumlahInstruksiPending',
            'permintaanDisetujui',
            'jumlahPermintaanDisetujui',
            'slotHampirPenuh',
            'gabahKadaluarsa',
            'ringkasanLumbung',
        ));
    }
}