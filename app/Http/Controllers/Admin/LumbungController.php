<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lumbung;
use App\Models\Pengelola;
use App\Models\SlotLumbung;
use Illuminate\Http\Request;

class LumbungController extends Controller
{
    /**
     * Daftar semua lumbung beserta ringkasan kapasitas.
     */
    public function index(Request $request)
    {
        $query = Lumbung::with(['pengelola', 'slotLumbung'])
            ->withCount('slotLumbung');

        if ($request->filled('search')) {
            $query->where('nama_lumbung', 'like', '%' . $request->search . '%');
        }

        $lumbungList = $query->orderBy('nama_lumbung')->paginate(15);

        // Tambahkan ringkasan kapasitas ke setiap lumbung
        $lumbungList->getCollection()->transform(function ($lumbung) {
            $lumbung->total_kapasitas  = $lumbung->slotLumbung->sum('kapasitas');
            $lumbung->total_tersedia   = $lumbung->slotLumbung->sum('kapasitas_tersedia');
            $lumbung->total_terpakai   = $lumbung->total_kapasitas - $lumbung->total_tersedia;
            $lumbung->persen_terpakai  = $lumbung->total_kapasitas > 0
                ? round(($lumbung->total_terpakai / $lumbung->total_kapasitas) * 100, 1)
                : 0;
            return $lumbung;
        });

        return view('admin.lumbung.index', compact('lumbungList'));
    }

    /**
     * Form tambah lumbung baru.
     */
    public function create()
    {
        // Pengelola yang belum mengelola lumbung manapun (atau izinkan 1 pengelola ke banyak lumbung)
        $pengelolaList = Pengelola::orderBy('nama_pengelola')->get();

        return view('admin.lumbung.create', compact('pengelolaList'));
    }

    /**
     * Simpan lumbung baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lumbung' => ['required', 'string', 'max:100', 'unique:lumbung,nama_lumbung'],
            'id_pengelola' => ['required', 'integer', 'exists:pengelola,id_pengelola'],
        ], [
            'nama_lumbung.required' => 'Nama lumbung wajib diisi.',
            'nama_lumbung.unique'   => 'Nama lumbung sudah terdaftar.',
            'id_pengelola.required' => 'Pengelola wajib dipilih.',
            'id_pengelola.exists'   => 'Pengelola tidak ditemukan.',
        ]);

        Lumbung::create($request->only('nama_lumbung', 'id_pengelola'));

        return redirect()
            ->route('admin.lumbung.index')
            ->with('success', "Lumbung \"{$request->nama_lumbung}\" berhasil ditambahkan.");
    }

    /**
     * Detail lumbung: profil + daftar slot + ringkasan stok.
     */
    public function show(int $id)
    {
        $lumbung = Lumbung::with([
            'pengelola',
            'slotLumbung.penyimpananGabah.detailPanen.jenisGabah',
            'slotLumbung.penyimpananGabah.detailPanen.panen.petani',
        ])
        ->findOrFail($id);

        // Ringkasan kapasitas
        $totalKapasitas = $lumbung->slotLumbung->sum('kapasitas');
        $totalTersedia  = $lumbung->slotLumbung->sum('kapasitas_tersedia');
        $totalTerpakai  = $totalKapasitas - $totalTersedia;
        $persenTerpakai = $totalKapasitas > 0
            ? round(($totalTerpakai / $totalKapasitas) * 100, 1)
            : 0;

        return view('admin.lumbung.show', compact(
            'lumbung',
            'totalKapasitas',
            'totalTersedia',
            'totalTerpakai',
            'persenTerpakai',
        ));
    }

    /**
     * Form edit lumbung.
     */
    public function edit(int $id)
    {
        $lumbung       = Lumbung::findOrFail($id);
        $pengelolaList = Pengelola::orderBy('nama_pengelola')->get();

        return view('admin.lumbung.edit', compact('lumbung', 'pengelolaList'));
    }

    /**
     * Update data lumbung.
     */
    public function update(Request $request, int $id)
    {
        $lumbung = Lumbung::findOrFail($id);

        $request->validate([
            'nama_lumbung' => [
                'required', 'string', 'max:100',
                'unique:lumbung,nama_lumbung,' . $id . ',id_lumbung',
            ],
            'id_pengelola' => ['required', 'integer', 'exists:pengelola,id_pengelola'],
        ], [
            'nama_lumbung.required' => 'Nama lumbung wajib diisi.',
            'nama_lumbung.unique'   => 'Nama lumbung sudah digunakan.',
            'id_pengelola.required' => 'Pengelola wajib dipilih.',
        ]);

        $lumbung->update($request->only('nama_lumbung', 'id_pengelola'));

        return redirect()
            ->route('admin.lumbung.show', $id)
            ->with('success', 'Data lumbung berhasil diperbarui.');
    }

    /**
     * Hapus lumbung.
     * Tidak bisa dihapus jika masih memiliki slot aktif.
     */
    public function destroy(int $id)
    {
        $lumbung = Lumbung::withCount('slotLumbung')->findOrFail($id);

        if ($lumbung->slot_lumbung_count > 0) {
            return back()->withErrors([
                'hapus' => "Lumbung \"{$lumbung->nama_lumbung}\" tidak dapat dihapus karena masih memiliki {$lumbung->slot_lumbung_count} slot.",
            ]);
        }

        $nama = $lumbung->nama_lumbung;
        $lumbung->delete();

        return redirect()
            ->route('admin.lumbung.index')
            ->with('success', "Lumbung \"{$nama}\" berhasil dihapus.");
    }
}