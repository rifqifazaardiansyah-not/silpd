<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\JenisGabah;
use App\Models\Lumbung;
use App\Models\PenyimpananGabah;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StokController extends Controller
{
    /**
     * Tampilkan monitoring stok seluruh lumbung yang dikelola.
     *
     * Menampilkan:
     * - Ringkasan kapasitas per lumbung
     * - Daftar stok gabah per slot dengan FIFO info (tanggal masuk tertua)
     * - Flag gabah yang sudah melewati batas hari simpan
     */
    public function index(Request $request)
    {
        $idPengelola = session('ref_id');

        // Lumbung yang dikelola pengelola ini (many-to-many)
        $lumbungList = Lumbung::whereHas('pengelola', function($q) use ($idPengelola) {
                $q->where('pengelola.id_pengelola', $idPengelola);
            })
            ->with([
                'slotLumbung' => function ($q) {
                    $q->orderBy('kode_slot');
                },
            ])
            ->get();

        $slotIds = $lumbungList->flatMap->slotLumbung->pluck('id_slot');

        // Query stok gabah aktif per slot
        $queryStok = PenyimpananGabah::whereIn('id_slot', $slotIds)
            ->where('status', 'tersimpan')
            ->with([
                'slotLumbung.lumbung',
                'detailPanen.jenisGabah',
                'detailPanen.panen.petani',
            ]);

        // Filter per lumbung jika dipilih
        if ($request->filled('id_lumbung')) {
            $slotIdsFiltered = SlotLumbung::where('id_lumbung', $request->id_lumbung)
                ->pluck('id_slot');
            $queryStok->whereIn('id_slot', $slotIdsFiltered);
        }

        // Filter per jenis gabah
        if ($request->filled('id_jenis_gabah')) {
            $queryStok->whereHas('detailPanen', function ($q) use ($request) {
                $q->where('id_jenis_gabah', $request->id_jenis_gabah);
            });
        }

        // Sort berdasarkan FIFO (tanggal masuk paling lama dulu)
        $stokList = $queryStok->orderBy('tanggal_masuk')->paginate(20);

        // Hitung ringkasan kapasitas per lumbung
        $ringkasanKapasitas = $lumbungList->map(function ($lumbung) {
            $slots            = $lumbung->slotLumbung;
            $totalKapasitas   = $slots->sum('kapasitas');
            $totalTersedia    = $slots->sum('kapasitas_tersedia');
            $totalTerpakai    = $totalKapasitas - $totalTersedia;
            $persenTerpakai   = $totalKapasitas > 0
                ? round(($totalTerpakai / $totalKapasitas) * 100, 1)
                : 0;

            return [
                'id_lumbung'      => $lumbung->id_lumbung,
                'nama_lumbung'    => $lumbung->nama_lumbung,
                'total_kapasitas' => $totalKapasitas,
                'total_tersedia'  => $totalTersedia,
                'total_terpakai'  => $totalTerpakai,
                'persen_terpakai' => $persenTerpakai,
                'jumlah_slot'     => $slots->count(),
            ];
        });

        // Gabah yang melewati batas hari simpan (untuk badge peringatan)
        $batasHari     = config('silpd.batas_hari_simpan', 90);
        $batasDate     = Carbon::now()->subDays($batasHari);
        $jumlahKadaluarsa = PenyimpananGabah::whereIn('id_slot', $slotIds)
            ->where('status', 'tersimpan')
            ->where('tanggal_masuk', '<=', $batasDate)
            ->count();

        // Ambil semua jenis gabah untuk filter dropdown
        $jenisGabahList = JenisGabah::orderBy('nama_jenis')->get();

        return view('pengelola.stok.index', compact(
            'lumbungList',
            'stokList',
            'ringkasanKapasitas',
            'jumlahKadaluarsa',
            'jenisGabahList',
        ));
    }

    /**
     * Detail stok per slot tertentu.
     * Menampilkan semua batch gabah di slot dengan urutan FIFO.
     */
    public function showSlot(int $idSlot)
    {
        $idPengelola   = session('ref_id');
        $idLumbungList = Lumbung::whereHas('pengelola', function($q) use ($idPengelola) {
            $q->where('pengelola.id_pengelola', $idPengelola);
        })->pluck('id_lumbung');

        $slot = SlotLumbung::whereIn('id_lumbung', $idLumbungList)
            ->with('lumbung')
            ->findOrFail($idSlot);

        $batasHari  = config('silpd.batas_hari_simpan', 90);
        $batasDate  = Carbon::now()->subDays($batasHari);

        // Semua gabah tersimpan di slot ini, urutan FIFO
        $stokSlot = PenyimpananGabah::where('id_slot', $idSlot)
            ->where('status', 'tersimpan')
            ->with([
                'detailPanen.jenisGabah',
                'detailPanen.panen.petani',
            ])
            ->orderBy('tanggal_masuk')
            ->get()
            ->map(function ($item) use ($batasDate) {
                $item->is_kadaluarsa = Carbon::parse($item->tanggal_masuk)->lte($batasDate);
                $item->umur_simpan   = Carbon::parse($item->tanggal_masuk)->diffInDays(now());
                return $item;
            });

        return view('pengelola.stok.slot', compact('slot', 'stokSlot', 'batasHari'));
    }
}