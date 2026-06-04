<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPanen;
use App\Models\JenisGabah;
use App\Models\KelompokTani;
use App\Models\Lumbung;
use App\Models\Panen;
use App\Models\Petani;
use App\Models\PermintaanPengambilan;
use App\Models\PenyimpananGabah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    // =========================================================================
    // LAPORAN STOK
    // =========================================================================

    /**
     * Laporan stok gabah saat ini di seluruh lumbung.
     *
     * Menampilkan kondisi terkini penyimpanan gabah:
     * - Stok per lumbung dan per slot
     * - Stok per jenis gabah
     * - Stok per petani
     * - Flag gabah yang mendekati atau sudah melewati batas simpan
     */
    public function stok(Request $request)
    {
        $batasHari = config('silpd.batas_hari_simpan', 90);
        $batasDate = Carbon::now()->subDays($batasHari);

        // Query dasar: gabah yang sedang tersimpan
        $queryStok = PenyimpananGabah::where('status', 'tersimpan')
            ->with([
                'slotLumbung.lumbung.pengelola',
                'detailPanen.jenisGabah',
                'detailPanen.panen.petani.kelompokTani',
            ]);

        // Filter berdasarkan lumbung
        if ($request->filled('id_lumbung')) {
            $queryStok->whereHas('slotLumbung', fn ($q) => $q->where('id_lumbung', $request->id_lumbung));
        }

        // Filter berdasarkan jenis gabah
        if ($request->filled('id_jenis_gabah')) {
            $queryStok->whereHas('detailPanen', fn ($q) => $q->where('id_jenis_gabah', $request->id_jenis_gabah));
        }

        // Filter berdasarkan petani (pencarian nama)
        if ($request->filled('search_petani')) {
            $queryStok->whereHas('detailPanen.panen.petani', function ($q) use ($request) {
                $q->where('nama_petani', 'like', '%' . $request->search_petani . '%');
            });
        }

        // Filter: tampilkan hanya yang kadaluarsa
        if ($request->boolean('hanya_kadaluarsa')) {
            $queryStok->where('tanggal_masuk', '<=', $batasDate);
        }

        $stokList = $queryStok->orderBy('tanggal_masuk')->paginate(20)->withQueryString();

        // Tambahkan info umur simpan dan flag kadaluarsa
        $stokList->getCollection()->transform(function ($item) use ($batasDate) {
            $item->umur_hari     = Carbon::parse($item->tanggal_masuk)->diffInDays(now());
            $item->is_kadaluarsa = Carbon::parse($item->tanggal_masuk)->lte($batasDate);
            return $item;
        });

        // Rekapitulasi total stok per lumbung
        $rekapPerLumbung = Lumbung::with('slotLumbung')->get()->map(function ($lumbung) {
            $slotIds        = $lumbung->slotLumbung->pluck('id_slot');
            $totalKapasitas = $lumbung->slotLumbung->sum('kapasitas');
            $totalTersedia  = $lumbung->slotLumbung->sum('kapasitas_tersedia');
            $totalTerpakai  = $totalKapasitas - $totalTersedia;

            return (object) [
                'nama_lumbung'      => $lumbung->nama_lumbung,
                'total_stok'        => $totalTerpakai,
                'kapasitas_total'   => $totalKapasitas,
                'kapasitas_tersedia'=> $totalTersedia,
                'persen_terpakai'   => $totalKapasitas > 0
                    ? round(($totalTerpakai / $totalKapasitas) * 100, 1) : 0,
                'jumlah_lot'        => PenyimpananGabah::whereIn('id_slot', $slotIds)
                    ->where('status', 'tersimpan')->count(),
            ];
        });

        // Rekapitulasi total stok per jenis gabah
        $rekapPerJenis = PenyimpananGabah::where('status', 'tersimpan')
            ->with('detailPanen.jenisGabah')
            ->get()
            ->groupBy(fn ($item) => $item->detailPanen->jenisGabah->nama_jenis)
            ->map(fn ($group, $namaJenis) => (object) [
                'nama_jenis' => $namaJenis,
                'total_stok' => $group->sum('jumlah'),
                'jumlah_lot' => $group->count(),
            ])
            ->sortByDesc('total_stok');

        $totalStokKeseluruhan = PenyimpananGabah::where('status', 'tersimpan')->sum('jumlah');
        $jumlahKadaluarsa     = PenyimpananGabah::where('status', 'tersimpan')
            ->where('tanggal_masuk', '<=', $batasDate)->count();

        $lumbungList    = Lumbung::orderBy('nama_lumbung')->get();
        $jenisGabahList = JenisGabah::orderBy('nama_jenis')->get();
        $petaniList     = Petani::orderBy('nama_petani')->get();

        return view('admin.laporan.stok', compact(
            'stokList',
            'rekapPerLumbung',
            'rekapPerJenis',
            'totalStokKeseluruhan',
            'jumlahKadaluarsa',
            'lumbungList',
            'jenisGabahList',
            'petaniList',
            'batasHari',
        ));
    }

    // =========================================================================
    // LAPORAN PANEN
    // =========================================================================

    /**
     * Laporan data panen per periode.
     *
     * Menampilkan rekap panen dengan analisis:
     * - Total panen per petani dalam periode
     * - Total kontribusi ke lumbung per petani
     * - Rekapitulasi per jenis gabah
     * - Rekapitulasi per kelompok tani
     * - Tren panen bulanan
     */
    public function panen(Request $request)
    {
        // Default rentang: Maret 2025 (sesuai data seeder)
        $dari    = $request->filled('dari') ? $request->dari : '2025-03-01';
        $sampai  = $request->filled('sampai') ? $request->sampai : '2025-03-31';

        $queryPanen = Panen::with([
            'petani.kelompokTani',
            'detailPanen.jenisGabah',
        ])
        ->whereBetween('tanggal_panen', [$dari, $sampai]);

        // Filter berdasarkan petani
        if ($request->filled('id_petani')) {
            $queryPanen->where('id_petani', $request->id_petani);
        }

        // Filter berdasarkan kelompok
        if ($request->filled('id_kelompok')) {
            $queryPanen->whereHas('petani', fn ($q) => $q->where('id_kelompok', $request->id_kelompok));
        }

        $panenList = $queryPanen->orderByDesc('tanggal_panen')->paginate(20)->withQueryString();

        // Rekap per petani dalam periode ini
        $persenLumbung = config('silpd.persen_lumbung', 3);
        $rekapPerPetani = Panen::with(['petani', 'detailPanen'])
            ->whereBetween('tanggal_panen', [$dari, $sampai])
            ->when($request->filled('id_petani'), fn ($q) => $q->where('id_petani', $request->id_petani))
            ->when($request->filled('id_kelompok'), fn ($q) => $q->whereHas('petani', fn ($q2) => $q2->where('id_kelompok', $request->id_kelompok)))
            ->get()
            ->groupBy('id_petani')
            ->map(function ($panenPetani) use ($persenLumbung) {
                $totalPanen   = $panenPetani->flatMap->detailPanen->sum('jumlah_panen');
                $totalLumbung = round($totalPanen * ($persenLumbung / 100), 2);

                return [
                    'petani'           => $panenPetani->first()->petani,
                    'jumlah_panen'     => $panenPetani->count(),
                    'total_panen_kg'   => $totalPanen,
                    'total_lumbung_kg' => $totalLumbung,
                ];
            })
            ->sortByDesc('total_panen_kg');

        // Rekap per jenis gabah dalam periode
        $rekapPerJenis = DetailPanen::whereHas('panen', fn ($q) => $q->whereBetween('tanggal_panen', [$dari, $sampai]))
            ->with('jenisGabah')
            ->get()
            ->groupBy(fn ($item) => $item->jenisGabah->nama_jenis)
            ->map(fn ($group) => [
                'total_panen'   => $group->sum('jumlah_panen'),
                'total_lumbung' => round($group->sum('jumlah_panen') * ($persenLumbung / 100), 2),
            ])
            ->sortByDesc('total_panen');

        // Rekap per kelompok tani dalam periode
        $rekapPerKelompok = Panen::with(['petani.kelompokTani', 'detailPanen'])
            ->whereBetween('tanggal_panen', [$dari, $sampai])
            ->get()
            ->groupBy(fn ($p) => $p->petani->kelompokTani->nama_kelompok)
            ->map(function ($group) use ($persenLumbung) {
                $totalPanen = $group->flatMap->detailPanen->sum('jumlah_panen');
                return [
                    'jumlah_petani' => $group->pluck('id_petani')->unique()->count(),
                    'jumlah_panen'  => $group->count(),
                    'total_panen'   => $totalPanen,
                    'total_lumbung' => round($totalPanen * ($persenLumbung / 100), 2),
                ];
            })
            ->sortByDesc('total_panen');

        // Tren panen harian dalam rentang periode (untuk grafik)
        $trenHarian = Panen::selectRaw('tanggal_panen, COUNT(*) as jumlah_transaksi, SUM(dp.jumlah_panen) as total_kg')
            ->join('detail_panen as dp', 'panen.id_panen', '=', 'dp.id_panen')
            ->whereBetween('tanggal_panen', [$dari, $sampai])
            ->groupBy('tanggal_panen')
            ->orderBy('tanggal_panen')
            ->get();

        $totalPanenKg   = $rekapPerPetani->sum('total_panen_kg');
        $totalLumbungKg = round($totalPanenKg * ($persenLumbung / 100), 2);

        $petaniList   = Petani::orderBy('nama_petani')->get();
        $kelompokList = KelompokTani::orderBy('nama_kelompok')->get();

        return view('admin.laporan.panen', compact(
            'panenList',
            'rekapPerPetani',
            'rekapPerJenis',
            'rekapPerKelompok',
            'trenHarian',
            'totalPanenKg',
            'totalLumbungKg',
            'persenLumbung',
            'dari',
            'sampai',
            'petaniList',
            'kelompokList',
        ));
    }

    // =========================================================================
    // LAPORAN PENGAMBILAN
    // =========================================================================

    /**
     * Laporan riwayat pengambilan gabah dari lumbung.
     *
     * Menampilkan:
     * - Semua pengambilan yang sudah selesai dalam periode
     * - Rekap per petani: berapa kali dan berapa kg yang sudah diambil
     * - Rekap per jenis gabah yang diambil
     * - Perbandingan antara stok yang dimasukkan vs diambil
     */
    public function pengambilan(Request $request)
    {
        $dari   = $request->filled('dari') ? $request->dari : Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->filled('sampai') ? $request->sampai : Carbon::now()->endOfMonth()->format('Y-m-d');

        $queryPengambilan = PermintaanPengambilan::with([
            'petani.kelompokTani',
            'penyimpananGabah.detailPanen.jenisGabah',
            'penyimpananGabah.slotLumbung.lumbung',
            'detailPengambilan',
        ])
        ->whereIn('status', ['selesai'])
        ->whereBetween('tanggal_permintaan', [$dari, $sampai]);

        // Filter berdasarkan petani
        if ($request->filled('id_petani')) {
            $queryPengambilan->where('id_petani', $request->id_petani);
        }

        // Filter berdasarkan kelompok
        if ($request->filled('id_kelompok')) {
            $queryPengambilan->whereHas('petani', fn ($q) => $q->where('id_kelompok', $request->id_kelompok));
        }

        // Filter berdasarkan jenis gabah
        if ($request->filled('id_jenis_gabah')) {
            $queryPengambilan->whereHas('penyimpananGabah.detailPanen', fn ($q) => $q->where('id_jenis_gabah', $request->id_jenis_gabah));
        }

        $pengambilanList = $queryPengambilan->orderByDesc('tanggal_permintaan')
            ->paginate(20)
            ->withQueryString();

        // Rekap per petani
        $rekapPerPetani = PermintaanPengambilan::with(['petani', 'detailPengambilan'])
            ->where('status', 'selesai')
            ->whereBetween('tanggal_permintaan', [$dari, $sampai])
            ->when($request->filled('id_petani'), fn ($q) => $q->where('id_petani', $request->id_petani))
            ->get()
            ->groupBy('id_petani')
            ->map(fn ($group) => [
                'petani'              => $group->first()->petani,
                'jumlah_pengambilan'  => $group->count(),
                'total_diambil_kg'    => $group->flatMap->detailPengambilan->sum('jumlah'),
            ])
            ->sortByDesc('total_diambil_kg');

        // Rekap per jenis gabah yang diambil
        $rekapPerJenis = PermintaanPengambilan::with([
                'penyimpananGabah.detailPanen.jenisGabah',
                'detailPengambilan',
            ])
            ->where('status', 'selesai')
            ->whereBetween('tanggal_permintaan', [$dari, $sampai])
            ->get()
            ->groupBy(fn ($item) => $item->penyimpananGabah->detailPanen->jenisGabah->nama_jenis)
            ->map(fn ($group) => [
                'jumlah_transaksi' => $group->count(),
                'total_diambil_kg' => $group->flatMap->detailPengambilan->sum('jumlah'),
            ])
            ->sortByDesc('total_diambil_kg');

        $totalDiambilKg  = $rekapPerPetani->sum('total_diambil_kg');
        $jumlahTransaksi = $rekapPerPetani->sum('jumlah_pengambilan');

        // Juga tampilkan permintaan yang ditolak dalam periode yang sama
        $jumlahDitolak = PermintaanPengambilan::where('status', 'ditolak')
            ->whereBetween('tanggal_permintaan', [$dari, $sampai])
            ->count();

        $petaniList     = Petani::orderBy('nama_petani')->get();
        $kelompokList   = KelompokTani::orderBy('nama_kelompok')->get();
        $jenisGabahList = JenisGabah::orderBy('nama_jenis')->get();

        return view('admin.laporan.pengambilan', compact(
            'pengambilanList',
            'rekapPerPetani',
            'rekapPerJenis',
            'totalDiambilKg',
            'jumlahTransaksi',
            'jumlahDitolak',
            'dari',
            'sampai',
            'petaniList',
            'kelompokList',
            'jenisGabahList',
        ));
    }

    // =========================================================================
    // LAPORAN REKAP PETANI
    // =========================================================================

    /**
     * Laporan rekap per petani: kontribusi ke lumbung vs pengambilan.
     *
     * Berguna untuk melihat saldo bersih setiap petani — berapa yang
     * sudah dimasukkan ke lumbung dan berapa yang sudah diambil kembali.
     */
    public function rekapPetani(Request $request)
    {
        $persenLumbung = config('silpd.persen_lumbung', 3);

        $petaniList = Petani::with(['kelompokTani'])
            ->when($request->filled('id_kelompok'), fn ($q) => $q->where('id_kelompok', $request->id_kelompok))
            ->orderBy('nama_petani')
            ->get()
            ->map(function ($petani) use ($persenLumbung) {
                // Total panen sepanjang waktu
                $totalPanenKg = DetailPanen::whereHas('panen', fn ($q) => $q->where('id_petani', $petani->id_petani))
                    ->sum('jumlah_panen');

                // Total yang sudah dimasukkan ke lumbung (sesuai 3%)
                $totalMasukLumbungKg = round($totalPanenKg * ($persenLumbung / 100), 2);

                // Stok aktif sekarang
                $stokAktifKg = PenyimpananGabah::whereHas('detailPanen.panen', fn ($q) => $q->where('id_petani', $petani->id_petani))
                    ->where('status', 'tersimpan')
                    ->sum('jumlah');

                // Total yang sudah diambil kembali
                $totalDiambilKg = PermintaanPengambilan::where('id_petani', $petani->id_petani)
                    ->where('status', 'selesai')
                    ->with('detailPengambilan')
                    ->get()
                    ->flatMap->detailPengambilan
                    ->sum('jumlah');

                return [
                    'petani'                    => $petani,
                    'total_panen_kg'            => $totalPanenKg,
                    'total_masuk_lumbung_kg'    => $totalMasukLumbungKg,
                    'stok_aktif_kg'             => $stokAktifKg,
                    'total_diambil_kg'          => $totalDiambilKg,
                ];
            });

        $kelompokList = KelompokTani::orderBy('nama_kelompok')->get();

        return view('admin.laporan.rekap-petani', compact('petaniList', 'kelompokList'));
    }

    // =========================================================================
    // EKSPOR
    // =========================================================================

    /**
     * Ekspor laporan stok ke format CSV sederhana.
     *
     * Untuk ekspor ke PDF atau Excel yang lebih lengkap,
     * install package barryvdh/laravel-dompdf (PDF) atau
     * maatwebsite/excel (Excel) dan implementasikan di sini.
     */
    public function eksporStokCsv(Request $request)
    {
        $batasDate = Carbon::now()->subDays(config('silpd.batas_hari_simpan', 90));

        $data = PenyimpananGabah::where('status', 'tersimpan')
            ->with([
                'slotLumbung.lumbung',
                'detailPanen.jenisGabah',
                'detailPanen.panen.petani',
            ])
            ->orderBy('tanggal_masuk')
            ->get();

        $namaFile = 'laporan_stok_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
        ];

        $callback = function () use ($data, $batasDate) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar UTF-8 terbaca dengan benar
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, [
                'No', 'Lumbung', 'Slot', 'Petani', 'Jenis Gabah',
                'Jumlah (kg)', 'Tanggal Masuk', 'Umur Simpan (hari)', 'Status Kadaluarsa',
            ]);

            foreach ($data as $index => $item) {
                $umurHari   = Carbon::parse($item->tanggal_masuk)->diffInDays(now());
                $kadaluarsa = Carbon::parse($item->tanggal_masuk)->lte($batasDate) ? 'KADALUARSA' : 'Baik';

                fputcsv($handle, [
                    $index + 1,
                    $item->slotLumbung->lumbung->nama_lumbung,
                    $item->slotLumbung->kode_slot,
                    $item->detailPanen->panen->petani->nama_petani,
                    $item->detailPanen->jenisGabah->nama_jenis,
                    $item->jumlah,
                    $item->tanggal_masuk,
                    $umurHari,
                    $kadaluarsa,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ekspor laporan panen ke CSV.
     */
    public function eksporPanenCsv(Request $request)
    {
        $dari   = $request->filled('dari') ? $request->dari : Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->filled('sampai') ? $request->sampai : Carbon::now()->endOfMonth()->format('Y-m-d');

        $data = Panen::with(['petani.kelompokTani', 'detailPanen.jenisGabah'])
            ->whereBetween('tanggal_panen', [$dari, $sampai])
            ->orderByDesc('tanggal_panen')
            ->get();

        $persenLumbung = config('silpd.persen_lumbung', 3);
        $namaFile      = 'laporan_panen_' . str_replace('-', '', $dari) . '_' . str_replace('-', '', $sampai) . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
        ];

        $callback = function () use ($data, $persenLumbung) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'Tanggal Panen', 'Petani', 'Kelompok Tani',
                'Jenis Gabah', 'Jumlah Panen (kg)', 'Jumlah Lumbung (kg)',
            ]);

            $no = 1;
            foreach ($data as $panen) {
                foreach ($panen->detailPanen as $detail) {
                    $jumlahLumbung = round($detail->jumlah_panen * ($persenLumbung / 100), 2);
                    fputcsv($handle, [
                        $no++,
                        $panen->tanggal_panen,
                        $panen->petani->nama_petani,
                        $panen->petani->kelompokTani->nama_kelompok,
                        $detail->jenisGabah->nama_jenis,
                        $detail->jumlah_panen,
                        $jumlahLumbung,
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}