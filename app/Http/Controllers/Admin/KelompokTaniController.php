<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelompokTani;
use App\Models\PenyimpananGabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelompokTaniController extends Controller
{
    /**
     * Daftar semua kelompok tani.
     */
    public function index(Request $request)
    {
        $query = KelompokTani::withCount('petani');

        if ($request->filled('search')) {
            $query->where('nama_kelompok', 'like', '%' . $request->search . '%');
        }

        $kelompokList = $query->orderBy('nama_kelompok')->paginate(15);

        return view('admin.kelompok.index', compact('kelompokList'));
    }

    /**
     * Form tambah kelompok tani baru.
     */
    public function create()
    {
        return view('admin.kelompok.create');
    }

    /**
     * Lihat detail kelompok tani dan daftar anggota petani.
     */
    public function show(int $id)
    {
        $kelompok = KelompokTani::withCount('petani')->findOrFail($id);
        
        // Paginate anggota petani
        $anggota = $kelompok->petani()->paginate(10);
        
        // Calculate total stok gabah for the group
        // Join: penyimpanan_gabah -> detail_panen -> panen -> petani -> kelompok_tani
        $totalStokKelompok = PenyimpananGabah::join('detail_panen', 'penyimpanan_gabah.id_detail', '=', 'detail_panen.id_detail')
            ->join('panen', 'detail_panen.id_panen', '=', 'panen.id_panen')
            ->join('petani', 'panen.id_petani', '=', 'petani.id_petani')
            ->where('petani.id_kelompok', $id)
            ->where('penyimpanan_gabah.status', 'tersimpan')
            ->sum(DB::raw('penyimpanan_gabah.jumlah'));

        return view('admin.kelompok.show', compact('kelompok', 'anggota', 'totalStokKelompok'));
    }

    /**
     * Simpan kelompok tani baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelompok' => ['required', 'string', 'max:100', 'unique:kelompok_tani,nama_kelompok'],
        ], [
            'nama_kelompok.required' => 'Nama kelompok tani wajib diisi.',
            'nama_kelompok.unique'   => 'Nama kelompok tani sudah terdaftar.',
            'nama_kelompok.max'      => 'Nama kelompok maksimal 100 karakter.',
        ]);

        KelompokTani::create(['nama_kelompok' => $request->nama_kelompok]);

        return redirect()
            ->route('admin.kelompok.index')
            ->with('success', "Kelompok tani \"{$request->nama_kelompok}\" berhasil ditambahkan.");
    }

    /**
     * Form edit kelompok tani.
     */
    public function edit(int $id)
    {
        $kelompok = KelompokTani::withCount('petani')->findOrFail($id);

        return view('admin.kelompok.edit', compact('kelompok'));
    }

    /**
     * Update kelompok tani.
     */
    public function update(Request $request, int $id)
    {
        $kelompok = KelompokTani::findOrFail($id);

        $request->validate([
            'nama_kelompok' => [
                'required', 'string', 'max:100',
                'unique:kelompok_tani,nama_kelompok,' . $id . ',id_kelompok',
            ],
        ], [
            'nama_kelompok.required' => 'Nama kelompok tani wajib diisi.',
            'nama_kelompok.unique'   => 'Nama kelompok tani sudah digunakan.',
        ]);

        $kelompok->update(['nama_kelompok' => $request->nama_kelompok]);

        return redirect()
            ->route('admin.kelompok.index')
            ->with('success', 'Kelompok tani berhasil diperbarui.');
    }

    /**
     * Hapus kelompok tani.
     * Tidak bisa dihapus jika masih memiliki anggota petani.
     */
    public function destroy(int $id)
    {
        $kelompok = KelompokTani::withCount('petani')->findOrFail($id);

        if ($kelompok->petani_count > 0) {
            return back()->withErrors([
                'hapus' => "Kelompok \"{$kelompok->nama_kelompok}\" tidak dapat dihapus karena masih memiliki {$kelompok->petani_count} petani.",
            ]);
        }

        $nama = $kelompok->nama_kelompok;
        $kelompok->delete();

        return redirect()
            ->route('admin.kelompok.index')
            ->with('success', "Kelompok tani \"{$nama}\" berhasil dihapus.");
    }
}