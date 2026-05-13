<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Panen;
use App\Models\Petani;
use App\Models\PenyimpananGabah;
use App\Models\PermintaanPengambilan;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard petani.
     *
     * Menampilkan:
     * - Data profil petani (nama, kelompok, luas lahan)
     * - Total gabah milik petani yang tersimpan di lumbung
     * - Rincian stok per jenis gabah
     * - Permintaan pengambilan terbaru dan statusnya
     * - Riwayat panen singkat
     */
    public function index()
    {
        $idPetani = session('ref_id');

        // Data petani
        $petani = Petani::with('kelompokTani')->findOrFail($idPetani);

        // Total gabah tersimpan milik petani ini (semua jenis)
        $totalGabahTersimpan = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($idPetani) {
                $q->where('id_petani', $idPetani);
            })
            ->where('status', 'tersimpan')
            ->sum('jumlah');

        // Stok per jenis gabah
        $stokPerJenis = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($idPetani) {
                $q->where('id_petani', $idPetani);
            })
            ->where('status', 'tersimpan')
            ->with('detailPanen.jenisGabah')
            ->get()
            ->groupBy(fn ($item) => $item->detailPanen->jenisGabah->nama_jenis)
            ->map(fn ($group) => $group->sum('jumlah'));

        // Permintaan pengambilan terbaru (5 terakhir)
        $permintaanTerbaru = PermintaanPengambilan::where('id_petani', $idPetani)
            ->with([
                'penyimpananGabah.detailPanen.jenisGabah',
                'penyimpananGabah.slotLumbung.lumbung',
                'detailPengambilan',
            ])
            ->orderByDesc('tanggal_permintaan')
            ->take(5)
            ->get();

        // Permintaan yang masih pending atau disetujui (belum selesai)
        $permintaanAktif = PermintaanPengambilan::where('id_petani', $idPetani)
            ->whereIn('status', ['pending', 'disetujui'])
            ->count();

        // Panen terbaru (3 terakhir)
        $panenTerbaru = Panen::where('id_petani', $idPetani)
            ->with('detailPanen.jenisGabah')
            ->orderByDesc('tanggal_panen')
            ->take(3)
            ->get();

        return view('petani.dashboard', compact(
            'petani',
            'totalGabahTersimpan',
            'stokPerJenis',
            'permintaanTerbaru',
            'permintaanAktif',
            'panenTerbaru',
        ));
    }
}