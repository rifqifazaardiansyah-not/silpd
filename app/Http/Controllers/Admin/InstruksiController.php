<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstruksiPenyimpanan;
use App\Models\Lumbung;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InstruksiController extends Controller
{
    /**
     * Daftar semua instruksi penyimpanan di seluruh lumbung.
     *
     * Berbeda dengan Pengelola/InstruksiPenyimpananController yang hanya
     * menampilkan instruksi untuk lumbungnya sendiri, admin melihat semuanya.
     * Bisa difilter berdasarkan status, lumbung, atau rentang tanggal.
     */
    public function index(Request $request)
    {
        $query = InstruksiPenyimpanan::with([
            'detailPanen.panen.petani',
            'detailPanen.jenisGabah',
            'slotLumbung.lumbung.pengelola',
        ]);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan lumbung
        if ($request->filled('id_lumbung')) {
            $query->whereHas('slotLumbung', fn ($q) => $q->where('id_lumbung', $request->id_lumbung));
        }

        // Filter berdasarkan rentang tanggal instruksi
        if ($request->filled('dari')) {
            $query->where('tanggal_instruksi', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal_instruksi', '<=', $request->sampai);
        }

        // Filter berdasarkan petani
        if ($request->filled('id_petani')) {
            $query->whereHas('detailPanen.panen', fn ($q) => $q->where('id_petani', $request->id_petani));
        }

        $instruksiList = $query
            ->orderByRaw("FIELD(status, 'pending', 'selesai')")
            ->orderBy('tanggal_instruksi')
            ->paginate(15)
            ->withQueryString();

        // Ringkasan untuk kartu info
        $jumlahPending  = InstruksiPenyimpanan::where('status', 'pending')->count();
        $jumlahSelesai  = InstruksiPenyimpanan::where('status', 'selesai')->count();
        $lumbungList    = Lumbung::orderBy('nama_lumbung')->get();

        return view('admin.instruksi.index', compact(
            'instruksiList',
            'jumlahPending',
            'jumlahSelesai',
            'lumbungList',
        ));
    }

    /**
     * Detail satu instruksi penyimpanan.
     *
     * Menampilkan informasi lengkap: petani, jenis gabah, slot tujuan,
     * pengelola penanggung jawab, dan status konfirmasi.
     */
    public function show(int $id)
    {
        $instruksi = InstruksiPenyimpanan::with([
            'detailPanen.panen.petani.kelompokTani',
            'detailPanen.jenisGabah',
            'slotLumbung.lumbung.pengelola',
        ])->findOrFail($id);

        // Jika sudah selesai, tampilkan juga data penyimpanan gabah yang terbentuk
        $penyimpanan = null;
        if ($instruksi->status === 'selesai') {
            $penyimpanan = $instruksi->detailPanen->penyimpananGabah()
                ->where('id_slot', $instruksi->id_slot)
                ->first();
        }

        return view('admin.instruksi.show', compact('instruksi', 'penyimpanan'));
    }

    /**
     * Pindahkan instruksi ke slot lain.
     *
     * Admin bisa mengganti slot tujuan selama instruksi masih berstatus 'pending'.
     * Berguna jika slot awal ternyata penuh atau tidak sesuai.
     */
    public function pindahSlot(Request $request, int $id)
    {
        $instruksi = InstruksiPenyimpanan::where('status', 'pending')
            ->with('slotLumbung')
            ->findOrFail($id);

        $request->validate([
            'id_slot_baru' => [
                'required',
                'exists:slot_lumbung,id_slot',
                // Tidak boleh ke slot yang sama
                function ($attribute, $value, $fail) use ($instruksi) {
                    if ((int) $value === $instruksi->id_slot) {
                        $fail('Slot tujuan tidak boleh sama dengan slot saat ini.');
                    }
                },
            ],
            'alasan' => ['required', 'string', 'max:255'],
        ], [
            'id_slot_baru.required' => 'Slot tujuan wajib dipilih.',
            'id_slot_baru.exists'   => 'Slot tidak ditemukan.',
            'alasan.required'       => 'Alasan perpindahan slot wajib diisi.',
        ]);

        // Validasi kapasitas slot baru mencukupi
        $slotBaru = SlotLumbung::with('lumbung')->findOrFail($request->id_slot_baru);

        if ($slotBaru->kapasitas_tersedia < $instruksi->jumlah) {
            return back()->withErrors([
                'id_slot_baru' => "Kapasitas slot {$slotBaru->kode_slot} tidak mencukupi. " .
                    "Tersedia: {$slotBaru->kapasitas_tersedia} kg, dibutuhkan: {$instruksi->jumlah} kg.",
            ]);
        }

        $instruksi->update(['id_slot' => $slotBaru->id_slot]);

        return redirect()
            ->route('admin.instruksi.show', $instruksi->id_instruksi)
            ->with('success', "Instruksi berhasil dipindahkan ke slot {$slotBaru->kode_slot} ({$slotBaru->lumbung->nama_lumbung}).");
    }

    /**
     * Form pilih slot untuk fitur pindah slot.
     * Menampilkan daftar slot yang kapasitasnya mencukupi.
     */
    public function formPindahSlot(int $id)
    {
        $instruksi = InstruksiPenyimpanan::where('status', 'pending')
            ->with([
                'detailPanen.jenisGabah',
                'slotLumbung.lumbung',
            ])
            ->findOrFail($id);

        $slotAlternatif = SlotLumbung::where('kapasitas_tersedia', '>=', $instruksi->jumlah)
            ->where('id_slot', '!=', $instruksi->id_slot)
            ->with('lumbung')
            ->orderByDesc('kapasitas_tersedia')
            ->get();

        return view('admin.instruksi.pindah-slot', compact('instruksi', 'slotAlternatif'));
    }

    /**
     * Hapus / batalkan instruksi penyimpanan yang masih pending.
     *
     * Berguna jika admin salah input data panen dan ingin membuat ulang instruksi.
     * Instruksi yang sudah dikonfirmasi pengelola (status: selesai) tidak bisa dihapus.
     */
    public function destroy(int $id)
    {
        $instruksi = InstruksiPenyimpanan::where('status', 'pending')
            ->with('detailPanen.panen')
            ->findOrFail($id);

        $idPanen = $instruksi->detailPanen->id_panen;

        $instruksi->delete();

        return redirect()
            ->route('admin.panen.show', $idPanen)
            ->with('success', 'Instruksi penyimpanan berhasil dibatalkan.');
    }
}