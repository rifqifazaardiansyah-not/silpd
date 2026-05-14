<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPengambilan;
use App\Models\KelompokTani;
use App\Models\Petani;
use App\Models\PermintaanPengambilan;
use App\Models\PenyimpananGabah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    /**
     * Daftar semua permintaan pengambilan gabah dari seluruh petani.
     *
     * Default menampilkan yang masih 'pending' di bagian atas.
     * Bisa difilter berdasarkan status, petani, atau kelompok.
     */
    public function index(Request $request)
    {
        $query = PermintaanPengambilan::with([
            'petani.kelompokTani',
            'penyimpananGabah.detailPanen.jenisGabah',
            'penyimpananGabah.slotLumbung.lumbung',
            'detailPengambilan',
        ]);

        // Filter berdasarkan status
        $statusFilter = $request->get('status', 'pending');
        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        // Filter berdasarkan petani
        if ($request->filled('id_petani')) {
            $query->where('id_petani', $request->id_petani);
        }

        // Filter berdasarkan kelompok tani
        if ($request->filled('id_kelompok')) {
            $query->whereHas('petani', fn ($q) => $q->where('id_kelompok', $request->id_kelompok));
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('dari')) {
            $query->where('tanggal_permintaan', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal_permintaan', '<=', $request->sampai);
        }

        $permintaanList = $query
            ->orderByRaw("FIELD(status, 'pending', 'disetujui', 'selesai', 'ditolak')")
            ->orderBy('tanggal_permintaan')
            ->paginate(15)
            ->withQueryString();

        // Jumlah per status untuk badge navigasi
        $jumlahPerStatus = PermintaanPengambilan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $petaniList   = Petani::orderBy('nama_petani')->get();
        $kelompokList = KelompokTani::orderBy('nama_kelompok')->get();

        return view('admin.permintaan.index', compact(
            'permintaanList',
            'jumlahPerStatus',
            'petaniList',
            'kelompokList',
            'statusFilter',
        ));
    }

    /**
     * Detail permintaan pengambilan.
     *
     * Menampilkan:
     * - Identitas petani pengaju
     * - Gabah yang diminta (dari penyimpanan mana, jenis apa, berapa kg)
     * - Alasan pengambilan
     * - Rekomendasi FIFO: apakah gabah yang diminta memang yang tertua,
     *   atau ada gabah yang lebih lama yang seharusnya diambil dulu
     * - Riwayat permintaan petani sebelumnya
     */
    public function show(int $id)
    {
        $permintaan = PermintaanPengambilan::with([
            'petani.kelompokTani',
            'penyimpananGabah.detailPanen.jenisGabah',
            'penyimpananGabah.detailPanen.panen',
            'penyimpananGabah.slotLumbung.lumbung',
            'detailPengambilan',
        ])->findOrFail($id);

        // Rekomendassi FIFO: cari gabah milik petani ini yang lebih lama
        // dari gabah yang diminta, dengan jenis yang sama
        $idPetani     = $permintaan->id_petani;
        $idJenisGabah = $permintaan->penyimpananGabah->detailPanen->id_jenis_gabah;
        $tanggalMasuk = $permintaan->penyimpananGabah->tanggal_masuk;

        $rekomendasiFifo = PenyimpananGabah::whereHas('detailPanen.panen', fn ($q) => $q->where('id_petani', $idPetani))
            ->whereHas('detailPanen', fn ($q) => $q->where('id_jenis_gabah', $idJenisGabah))
            ->where('status', 'tersimpan')
            ->where('tanggal_masuk', '<', $tanggalMasuk)
            ->with(['detailPanen.jenisGabah', 'slotLumbung.lumbung'])
            ->orderBy('tanggal_masuk')
            ->get();

        // Ada gabah yang lebih tua yang seharusnya diambil duluan?
        $adaPelanggaranFifo = $rekomendasiFifo->isNotEmpty();

        // Total stok aktif milik petani untuk jenis gabah yang sama
        $totalStokJenisSama = PenyimpananGabah::whereHas('detailPanen.panen', fn ($q) => $q->where('id_petani', $idPetani))
            ->whereHas('detailPanen', fn ($q) => $q->where('id_jenis_gabah', $idJenisGabah))
            ->where('status', 'tersimpan')
            ->sum('jumlah');

        // Riwayat permintaan sebelumnya dari petani ini
        $riwayatPermintaan = PermintaanPengambilan::where('id_petani', $idPetani)
            ->where('id_permintaan', '!=', $id)
            ->with('penyimpananGabah.detailPanen.jenisGabah')
            ->orderByDesc('tanggal_permintaan')
            ->take(5)
            ->get();

        return view('admin.permintaan.show', compact(
            'permintaan',
            'rekomendasiFifo',
            'adaPelanggaranFifo',
            'totalStokJenisSama',
            'riwayatPermintaan',
        ));
    }

    /**
     * Setujui permintaan pengambilan.
     *
     * Alur:
     * 1. Validasi permintaan masih berstatus 'pending'
     * 2. Validasi stok gabah yang diminta masih tersedia dan mencukupi
     * 3. Jika ada pelanggaran FIFO, admin bisa tetap menyetujui
     *    (FIFO adalah rekomendasi, bukan paksaan — keputusan ada di admin)
     * 4. Update status permintaan → 'disetujui'
     * 5. Pengelola lumbung selanjutnya melakukan konfirmasi pengeluaran fisik
     */
    public function setujui(Request $request, int $id)
    {
        $permintaan = PermintaanPengambilan::where('status', 'pending')
            ->with(['penyimpananGabah', 'detailPengambilan'])
            ->findOrFail($id);

        $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        // Validasi stok masih ada
        $penyimpanan = $permintaan->penyimpananGabah;
        if ($penyimpanan->status !== 'tersimpan') {
            return back()->withErrors([
                'setujui' => 'Gabah yang diminta sudah tidak tersedia (mungkin sudah diambil atau habis).',
            ]);
        }

        // Validasi jumlah yang diminta tidak melebihi stok tersimpan
        $totalDiminta = $permintaan->detailPengambilan->sum('jumlah');
        if ($totalDiminta > $penyimpanan->jumlah) {
            return back()->withErrors([
                'setujui' => "Jumlah yang diminta ({$totalDiminta} kg) melebihi stok tersimpan ({$penyimpanan->jumlah} kg).",
            ]);
        }

        $permintaan->update(['status' => 'disetujui']);

        return redirect()
            ->route('admin.permintaan.show', $permintaan->id_permintaan)
            ->with('success', 'Permintaan pengambilan berhasil disetujui. Pengelola lumbung akan segera mengeluarkan gabah.');
    }

    /**
     * Tolak permintaan pengambilan.
     *
     * Admin wajib mengisi alasan penolakan agar petani mengetahui
     * mengapa permintaannya tidak bisa dipenuhi.
     */
    public function tolak(Request $request, int $id)
    {
        $permintaan = PermintaanPengambilan::where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'alasan_tolak' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'alasan_tolak.required' => 'Alasan penolakan wajib diisi.',
            'alasan_tolak.min'      => 'Alasan penolakan terlalu singkat, minimal 10 karakter.',
        ]);

        DB::transaction(function () use ($permintaan, $request) {
            $permintaan->update(['status' => 'ditolak']);

            // Simpan alasan penolakan ke detail pengambilan sebagai catatan
            // (menggunakan kolom 'alasan' yang sudah ada di detail_pengambilan)
            $permintaan->detailPengambilan()->update([
                'alasan' => $permintaan->detailPengambilan->first()?->alasan
                    . "\n[DITOLAK ADMIN] " . $request->alasan_tolak,
            ]);
        });

        return redirect()
            ->route('admin.permintaan.index')
            ->with('success', 'Permintaan pengambilan berhasil ditolak.');
    }

    /**
     * Batalkan persetujuan (rollback dari 'disetujui' ke 'pending').
     *
     * Berguna jika admin salah menyetujui sebelum pengelola mengeksekusi.
     * Hanya bisa jika pengelola belum mengkonfirmasi pengeluaran fisik
     * (status masih 'disetujui', belum 'selesai').
     */
    public function batalSetujui(Request $request, int $id)
    {
        $permintaan = PermintaanPengambilan::where('status', 'disetujui')
            ->findOrFail($id);

        $request->validate([
            'alasan_batal' => ['required', 'string', 'min:10', 'max:255'],
        ], [
            'alasan_batal.required' => 'Alasan pembatalan wajib diisi.',
            'alasan_batal.min'      => 'Alasan terlalu singkat, minimal 10 karakter.',
        ]);

        $permintaan->update(['status' => 'pending']);

        return redirect()
            ->route('admin.permintaan.show', $permintaan->id_permintaan)
            ->with('success', 'Persetujuan berhasil dibatalkan. Permintaan kembali ke status pending.');
    }

    /**
     * Tolak permintaan yang sudah disetujui secara langsung
     * (misalnya ada perubahan kondisi setelah persetujuan).
     *
     * Hanya bisa jika pengelola BELUM mengkonfirmasi pengeluaran fisik.
     */
    public function tolakSetelahDisetujui(Request $request, int $id)
    {
        $permintaan = PermintaanPengambilan::where('status', 'disetujui')
            ->findOrFail($id);

        $request->validate([
            'alasan_tolak' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'alasan_tolak.required' => 'Alasan penolakan wajib diisi.',
            'alasan_tolak.min'      => 'Alasan terlalu singkat, minimal 10 karakter.',
        ]);

        $permintaan->update(['status' => 'ditolak']);

        return redirect()
            ->route('admin.permintaan.index')
            ->with('success', 'Permintaan yang telah disetujui berhasil dibatalkan dan ditolak.');
    }
}