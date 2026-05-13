<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\JenisGabah;
use App\Models\PenyimpananGabah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StokController extends Controller
{
    /**
     * Daftar gabah milik petani yang tersimpan di lumbung.
     *
     * Petani hanya dapat melihat gabah miliknya sendiri.
     * Data ditampilkan dengan informasi:
     * - Jenis gabah
     * - Jumlah tersimpan
     * - Lokasi slot & lumbung
     * - Tanggal masuk (untuk transparansi FIFO)
     * - Status penyimpanan
     */
    public function index(Request $request)
    {
        $idPetani = session('ref_id');

        $query = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($idPetani) {
                $q->where('id_petani', $idPetani);
            })
            ->with([
                'detailPanen.jenisGabah',
                'detailPanen.panen',
                'slotLumbung.lumbung',
            ]);

        // Filter berdasarkan status
        $statusFilter = $request->get('status', 'tersimpan');
        if (in_array($statusFilter, ['tersimpan', 'diambil', 'habis', 'semua'])) {
            if ($statusFilter !== 'semua') {
                $query->where('status', $statusFilter);
            }
        }

        // Filter berdasarkan jenis gabah
        if ($request->filled('id_jenis_gabah')) {
            $query->whereHas('detailPanen', function ($q) use ($request) {
                $q->where('id_jenis_gabah', $request->id_jenis_gabah);
            });
        }

        // Urut dari yang paling lama (FIFO awareness untuk petani)
        $stokList = $query->orderBy('tanggal_masuk')->paginate(15);

        // Total gabah aktif (tersimpan)
        $totalTersimpan = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($idPetani) {
                $q->where('id_petani', $idPetani);
            })
            ->where('status', 'tersimpan')
            ->sum('jumlah');

        // Rekapitulasi per jenis gabah
        $rekapPerJenis = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($idPetani) {
                $q->where('id_petani', $idPetani);
            })
            ->where('status', 'tersimpan')
            ->with('detailPanen.jenisGabah')
            ->get()
            ->groupBy(fn ($item) => $item->detailPanen->id_jenis_gabah)
            ->map(function ($group) {
                return [
                    'nama_jenis' => $group->first()->detailPanen->jenisGabah->nama_jenis,
                    'total'      => $group->sum('jumlah'),
                    'jumlah_lot' => $group->count(),
                ];
            })
            ->values();

        // Daftar jenis gabah untuk filter dropdown
        $jenisGabahList = JenisGabah::orderBy('nama_jenis')->get();

        return view('petani.stok.index', compact(
            'stokList',
            'totalTersimpan',
            'rekapPerJenis',
            'jenisGabahList',
            'statusFilter',
        ));
    }
}