<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\DetailPengambilan;
use App\Models\Lumbung;
use App\Models\PermintaanPengambilan;
use App\Models\PenyimpananGabah;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranGabahController extends Controller
{
    /**
     * Daftar permintaan pengambilan yang sudah disetujui admin
     * dan menunggu konfirmasi pengeluaran fisik oleh pengelola.
     */
    public function index(Request $request)
    {
        $idPengelola = session('ref_id');
        $slotIds     = $this->getSlotIdsPengelola($idPengelola);

        $query = PermintaanPengambilan::whereHas('penyimpananGabah.slotLumbung', function ($q) use ($slotIds) {
                $q->whereIn('id_slot', $slotIds);
            })
            ->with([
                'petani',
                'penyimpananGabah.slotLumbung.lumbung',
                'penyimpananGabah.detailPanen.jenisGabah',
                'detailPengambilan',
            ]);

        // Filter status
        $statusFilter = $request->get('status', 'disetujui');
        if (in_array($statusFilter, ['disetujui', 'selesai', 'semua'])) {
            if ($statusFilter !== 'semua') {
                $query->where('status', $statusFilter);
            }
        }

        $permintaanList = $query->orderBy('tanggal_permintaan')->paginate(15);

        $jumlahMenunggu = PermintaanPengambilan::whereHas('penyimpananGabah.slotLumbung', function ($q) use ($slotIds) {
                $q->whereIn('id_slot', $slotIds);
            })
            ->where('status', 'disetujui')
            ->count();
            
        $jumlahSelesai = PermintaanPengambilan::whereHas('penyimpananGabah.slotLumbung', function ($q) use ($slotIds) {
                $q->whereIn('id_slot', $slotIds);
            })
            ->where('status', 'selesai')
            ->count();

        return view('pengelola.pengeluaran.index', compact('permintaanList', 'jumlahMenunggu', 'jumlahSelesai', 'statusFilter'));
    }

    /**
     * Detail satu permintaan pengambilan beserta instruksi pengeluaran.
     */
    public function show(int $id)
    {
        $idPengelola = session('ref_id');
        $slotIds     = $this->getSlotIdsPengelola($idPengelola);

        $permintaan = PermintaanPengambilan::whereHas('penyimpananGabah.slotLumbung', function ($q) use ($slotIds) {
                $q->whereIn('id_slot', $slotIds);
            })
            ->with([
                'petani.kelompokTani',
                'penyimpananGabah.slotLumbung.lumbung',
                'penyimpananGabah.detailPanen.jenisGabah',
                'penyimpananGabah.detailPanen.panen',
                'detailPengambilan',
            ])
            ->findOrFail($id);

        return view('pengelola.pengeluaran.show', compact('permintaan'));
    }

    /**
     * Konfirmasi pengeluaran fisik gabah.
     *
     * Alur:
     * 1. Validasi permintaan berstatus 'disetujui'
     * 2. Kurangi jumlah gabah di penyimpanan_gabah
     * 3. Jika gabah habis, update status penyimpanan menjadi 'habis'
     * 4. Kembalikan kapasitas tersedia ke slot
     * 5. Update status permintaan menjadi 'selesai'
     */
    public function konfirmasi(Request $request, int $id)
    {
        $idPengelola = session('ref_id');
        $slotIds     = $this->getSlotIdsPengelola($idPengelola);

        $permintaan = PermintaanPengambilan::whereHas('penyimpananGabah.slotLumbung', function ($q) use ($slotIds) {
                $q->whereIn('id_slot', $slotIds);
            })
            ->where('status', 'disetujui')
            ->with(['detailPengambilan'])
            ->findOrFail($id);

        $request->validate([
            'tanggal_pengeluaran' => ['required', 'date', 'before_or_equal:today'],
            'catatan'             => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal_pengeluaran.required'        => 'Tanggal pengeluaran wajib diisi.',
            'tanggal_pengeluaran.before_or_equal' => 'Tanggal tidak boleh melewati hari ini.',
        ]);

        // Hitung total jumlah yang akan dikeluarkan dari detail_pengambilan
        $totalKeluar = $permintaan->detailPengambilan->sum('jumlah');

        if ($totalKeluar <= 0) {
            return back()->withErrors(['konfirmasi' => 'Tidak ada detail pengambilan. Hubungi admin.']);
        }

        try {
            DB::transaction(function () use ($permintaan, $totalKeluar) {
                // Lock penyimpanan dan slot untuk mencegah race condition
                $penyimpanan = PenyimpananGabah::where('id_penyimpanan', $permintaan->id_penyimpanan)
                    ->lockForUpdate()
                    ->first();

                if (!$penyimpanan) {
                    throw new \Exception('Data penyimpanan gabah tidak ditemukan.');
                }

                $slot = SlotLumbung::where('id_slot', $penyimpanan->id_slot)
                    ->lockForUpdate()
                    ->first();

                if (!$slot) {
                    throw new \Exception('Data slot lumbung tidak ditemukan.');
                }

                // Validasi stok DALAM transaction setelah lock
                if ($penyimpanan->jumlah < $totalKeluar) {
                    throw new \Exception("Stok gabah tidak mencukupi. Tersedia: {$penyimpanan->jumlah} kg, diminta: {$totalKeluar} kg.");
                }

                $sisaGabah = $penyimpanan->jumlah - $totalKeluar;

                // 1. Update jumlah penyimpanan gabah
                $penyimpanan->update([
                    'jumlah' => $sisaGabah,
                    'status' => $sisaGabah <= 0 ? 'habis' : 'tersimpan',
                ]);

                // 2. Kembalikan kapasitas slot
                $slot->increment('kapasitas_tersedia', $totalKeluar);

                // 3. Update status permintaan menjadi selesai
                $permintaan->update(['status' => 'selesai']);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['konfirmasi' => $e->getMessage()]);
        }

        return redirect()
            ->route('pengelola.pengeluaran.index')
            ->with('success', 'Konfirmasi pengeluaran berhasil. Stok gabah telah diperbarui.');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function getSlotIdsPengelola(int $idPengelola): \Illuminate\Support\Collection
    {
        $idLumbungList = Lumbung::whereHas('pengelola', function($q) use ($idPengelola) {
            $q->where('pengelola.id_pengelola', $idPengelola);
        })->pluck('id_lumbung');

        return SlotLumbung::whereIn('id_lumbung', $idLumbungList)->pluck('id_slot');
    }
}