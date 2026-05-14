<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lumbung;
use App\Models\PenyimpananGabah;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;

class SlotLumbungController extends Controller
{
    /**
     * Daftar slot dalam satu lumbung tertentu.
     * Slot selalu diakses dalam konteks lumbungnya.
     */
    public function index(Request $request, int $idLumbung)
    {
        $lumbung = Lumbung::with('pengelola')->findOrFail($idLumbung);

        $query = SlotLumbung::where('id_lumbung', $idLumbung)
            ->withCount([
                'penyimpananGabah as jumlah_lot_aktif' => function ($q) {
                    $q->where('status', 'tersimpan');
                },
            ]);

        if ($request->filled('search')) {
            $query->where('kode_slot', 'like', '%' . $request->search . '%');
        }

        $slotList = $query->orderBy('kode_slot')->paginate(20);

        // Hitung persentase penggunaan tiap slot
        $slotList->getCollection()->transform(function ($slot) {
            $terpakai = $slot->kapasitas - $slot->kapasitas_tersedia;
            $slot->persen_terpakai = $slot->kapasitas > 0
                ? round(($terpakai / $slot->kapasitas) * 100, 1)
                : 0;
            return $slot;
        });

        return view('admin.slot.index', compact('lumbung', 'slotList'));
    }

    /**
     * Form tambah slot baru ke sebuah lumbung.
     */
    public function create(int $idLumbung)
    {
        $lumbung = Lumbung::findOrFail($idLumbung);

        return view('admin.slot.create', compact('lumbung'));
    }

    /**
     * Simpan slot baru.
     * kapasitas_tersedia di-set sama dengan kapasitas saat pertama dibuat.
     */
    public function store(Request $request, int $idLumbung)
    {
        $lumbung = Lumbung::findOrFail($idLumbung);

        $request->validate([
            'kode_slot' => [
                'required', 'string', 'max:20',
                // Kode slot harus unik dalam satu lumbung
                function ($attribute, $value, $fail) use ($idLumbung) {
                    $exists = SlotLumbung::where('id_lumbung', $idLumbung)
                        ->where('kode_slot', $value)
                        ->exists();
                    if ($exists) {
                        $fail("Kode slot \"{$value}\" sudah digunakan di lumbung ini.");
                    }
                },
            ],
            'kapasitas' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ], [
            'kode_slot.required' => 'Kode slot wajib diisi.',
            'kode_slot.max'      => 'Kode slot maksimal 20 karakter.',
            'kapasitas.required' => 'Kapasitas slot wajib diisi.',
            'kapasitas.min'      => 'Kapasitas minimal 1 kg.',
        ]);

        SlotLumbung::create([
            'id_lumbung'        => $idLumbung,
            'kode_slot'         => strtoupper($request->kode_slot),
            'kapasitas'         => $request->kapasitas,
            'kapasitas_tersedia' => $request->kapasitas, // Awal: penuh tersedia
        ]);

        return redirect()
            ->route('admin.lumbung.slot.index', $idLumbung)
            ->with('success', "Slot \"{$request->kode_slot}\" berhasil ditambahkan.");
    }

    /**
     * Detail slot: kapasitas + semua gabah tersimpan (FIFO order).
     */
    public function show(int $idLumbung, int $idSlot)
    {
        $lumbung = Lumbung::findOrFail($idLumbung);

        $slot = SlotLumbung::where('id_lumbung', $idLumbung)->findOrFail($idSlot);

        $stokList = PenyimpananGabah::where('id_slot', $idSlot)
            ->where('status', 'tersimpan')
            ->with([
                'detailPanen.jenisGabah',
                'detailPanen.panen.petani',
            ])
            ->orderBy('tanggal_masuk') // FIFO
            ->get();

        $terpakai       = $slot->kapasitas - $slot->kapasitas_tersedia;
        $persenTerpakai = $slot->kapasitas > 0
            ? round(($terpakai / $slot->kapasitas) * 100, 1)
            : 0;

        return view('admin.slot.show', compact('lumbung', 'slot', 'stokList', 'terpakai', 'persenTerpakai'));
    }

    /**
     * Form edit slot.
     * Kapasitas hanya bisa diubah jika tidak mengurangi di bawah jumlah yang sudah terpakai.
     */
    public function edit(int $idLumbung, int $idSlot)
    {
        $lumbung = Lumbung::findOrFail($idLumbung);
        $slot    = SlotLumbung::where('id_lumbung', $idLumbung)->findOrFail($idSlot);

        return view('admin.slot.edit', compact('lumbung', 'slot'));
    }

    /**
     * Update data slot.
     */
    public function update(Request $request, int $idLumbung, int $idSlot)
    {
        $slot = SlotLumbung::where('id_lumbung', $idLumbung)->findOrFail($idSlot);

        $terpakai = $slot->kapasitas - $slot->kapasitas_tersedia;

        $request->validate([
            'kode_slot' => [
                'required', 'string', 'max:20',
                function ($attribute, $value, $fail) use ($idLumbung, $idSlot) {
                    $exists = SlotLumbung::where('id_lumbung', $idLumbung)
                        ->where('kode_slot', $value)
                        ->where('id_slot', '!=', $idSlot)
                        ->exists();
                    if ($exists) {
                        $fail("Kode slot \"{$value}\" sudah digunakan di lumbung ini.");
                    }
                },
            ],
            'kapasitas' => [
                'required', 'numeric', 'min:' . max(1, $terpakai),
            ],
        ], [
            'kode_slot.required' => 'Kode slot wajib diisi.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'kapasitas.min'      => "Kapasitas tidak boleh kurang dari {$terpakai} kg (jumlah gabah yang sudah terpakai).",
        ]);

        // Hitung selisih kapasitas untuk update kapasitas_tersedia
        $selisihKapasitas = $request->kapasitas - $slot->kapasitas;

        $slot->update([
            'kode_slot'          => strtoupper($request->kode_slot),
            'kapasitas'          => $request->kapasitas,
            'kapasitas_tersedia' => $slot->kapasitas_tersedia + $selisihKapasitas,
        ]);

        return redirect()
            ->route('admin.lumbung.slot.index', $idLumbung)
            ->with('success', 'Data slot berhasil diperbarui.');
    }

    /**
     * Hapus slot.
     * Tidak bisa dihapus jika masih menyimpan gabah.
     */
    public function destroy(int $idLumbung, int $idSlot)
    {
        $slot = SlotLumbung::where('id_lumbung', $idLumbung)
            ->withCount([
                'penyimpananGabah as gabah_aktif' => fn ($q) => $q->where('status', 'tersimpan'),
            ])
            ->findOrFail($idSlot);

        if ($slot->gabah_aktif > 0) {
            return back()->withErrors([
                'hapus' => "Slot \"{$slot->kode_slot}\" tidak dapat dihapus karena masih menyimpan gabah.",
            ]);
        }

        $kode = $slot->kode_slot;
        $slot->delete();

        return redirect()
            ->route('admin.lumbung.slot.index', $idLumbung)
            ->with('success', "Slot \"{$kode}\" berhasil dihapus.");
    }
}