<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\InstruksiPenyimpanan;
use App\Models\Lumbung;
use App\Models\PenyimpananGabah;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InstruksiPenyimpananController extends Controller
{
    /**
     * Daftar semua instruksi penyimpanan untuk lumbung yang dikelola.
     * Bisa difilter berdasarkan status.
     */
    public function index(Request $request)
    {
        $idPengelola = session('ref_id');
        $slotIds     = $this->getSlotIdsPengelola($idPengelola);

        $query = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)
            ->with([
                'detailPanen.panen.petani',
                'detailPanen.jenisGabah',
                'slotLumbung.lumbung',
            ]);

        // Filter status
        $statusFilter = $request->get('status', 'pending'); // Default: pending
        
        if ($statusFilter === 'semua') {
            // Tampilkan semua, tidak perlu where
        } else {
            $query->where('status', $statusFilter);
        }

        $instruksiList = $query->orderBy('tanggal_instruksi')->paginate(15);

        $jumlahPending = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)
            ->where('status', 'pending')
            ->count();
            
        $jumlahSelesai = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)
            ->where('status', 'selesai')
            ->count();
            
        $jumlahTotal = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)->count();

        return view('pengelola.instruksi.index', compact('instruksiList', 'jumlahPending', 'jumlahSelesai', 'jumlahTotal', 'statusFilter'));
    }

    /**
     * Detail satu instruksi penyimpanan.
     */
    public function show(int $id)
    {
        $idPengelola = session('ref_id');
        $slotIds     = $this->getSlotIdsPengelola($idPengelola);

        $instruksi = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)
            ->with([
                'detailPanen.panen.petani.kelompokTani',
                'detailPanen.jenisGabah',
                'slotLumbung.lumbung',
            ])
            ->findOrFail($id);

        return view('pengelola.instruksi.show', compact('instruksi'));
    }

    /**
     * Konfirmasi penyimpanan fisik gabah.
     *
     * Alur:
     * 1. Validasi instruksi masih pending dan milik lumbung pengelola ini
     * 2. Buat record penyimpanan_gabah
     * 3. Kurangi kapasitas_tersedia di slot_lumbung
     * 4. Update status instruksi menjadi 'selesai'
     */
    public function konfirmasi(Request $request, int $id)
    {
        $idPengelola = session('ref_id');
        $slotIds     = $this->getSlotIdsPengelola($idPengelola);

        $instruksi = InstruksiPenyimpanan::whereIn('id_slot', $slotIds)
            ->where('status', 'pending')
            ->with('slotLumbung')
            ->findOrFail($id);

        $request->validate([
            'tanggal_masuk' => ['required', 'date', 'before_or_equal:today'],
            'catatan'       => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal_masuk.required'        => 'Tanggal penyimpanan wajib diisi.',
            'tanggal_masuk.before_or_equal' => 'Tanggal tidak boleh melewati hari ini.',
        ]);

        // Cek kapasitas slot mencukupi
        $slot = $instruksi->slotLumbung;
        if ($slot->kapasitas_tersedia < $instruksi->jumlah) {
            return back()->withErrors([
                'konfirmasi' => "Kapasitas slot {$slot->kode_slot} tidak mencukupi. " .
                    "Tersedia: {$slot->kapasitas_tersedia} kg, dibutuhkan: {$instruksi->jumlah} kg.",
            ]);
        }

        DB::transaction(function () use ($instruksi, $request, $slot) {
            // 1. Buat record penyimpanan gabah dengan tracking historical
            PenyimpananGabah::create([
                'id_detail'     => $instruksi->id_detail,
                'id_instruksi'  => $instruksi->id_instruksi, // Link ke instruksi
                'id_slot'       => $instruksi->id_slot,
                'jumlah_masuk'  => $instruksi->jumlah, // Jumlah original (historical)
                'jumlah'        => $instruksi->jumlah, // Jumlah current (akan berkurang saat pengambilan)
                'tanggal_masuk' => $request->tanggal_masuk,
                'status'        => 'tersimpan',
            ]);

            // 2. Kurangi kapasitas tersedia slot
            $slot->decrement('kapasitas_tersedia', $instruksi->jumlah);

            // 3. Tandai instruksi selesai
            $instruksi->update(['status' => 'selesai']);
        });

        return redirect()
            ->route('pengelola.instruksi.index')
            ->with('success', "Konfirmasi penyimpanan berhasil. Gabah telah disimpan di slot {$slot->kode_slot}.");
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Ambil semua ID slot dari lumbung yang dikelola pengelola ini.
     */
    private function getSlotIdsPengelola(int $idPengelola): \Illuminate\Support\Collection
    {
        $idLumbungList = Lumbung::whereHas('pengelola', function($q) use ($idPengelola) {
            $q->where('pengelola.id_pengelola', $idPengelola);
        })->pluck('id_lumbung');

        return SlotLumbung::whereIn('id_lumbung', $idLumbungList)->pluck('id_slot');
    }
}