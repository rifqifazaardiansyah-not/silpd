<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelompokTani;
use App\Models\Panen;
use App\Models\Petani;
use App\Models\PenyimpananGabah;
use Illuminate\Http\Request;

class PetaniController extends Controller
{
    /**
     * Daftar semua petani dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        $query = Petani::with('kelompokTani')->withCount('panen');

        if ($request->filled('search')) {
            $query->where('nama_petani', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('id_kelompok')) {
            $query->where('id_kelompok', $request->id_kelompok);
        }

        $petaniList   = $query->orderBy('nama_petani')->paginate(15);
        $kelompokList = KelompokTani::orderBy('nama_kelompok')->get();

        return view('admin.petani.index', compact('petaniList', 'kelompokList'));
    }

    /**
     * Form tambah petani baru.
     */
    public function create()
    {
        $kelompokList = KelompokTani::orderBy('nama_kelompok')->get();

        return view('admin.petani.create', compact('kelompokList'));
    }

    /**
     * Simpan data petani baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok_tani,id_kelompok'],
            'nama_petani' => ['required', 'string', 'max:100'],
            'luas_lahan'  => ['required', 'numeric', 'min:0.01', 'max:999.99'],
        ], [
            'id_kelompok.required' => 'Kelompok tani wajib dipilih.',
            'id_kelompok.exists'   => 'Kelompok tani tidak ditemukan.',
            'nama_petani.required' => 'Nama petani wajib diisi.',
            'luas_lahan.required'  => 'Luas lahan wajib diisi.',
            'luas_lahan.min'       => 'Luas lahan minimal 0.01 hektar.',
        ]);

        Petani::create($request->only('id_kelompok', 'nama_petani', 'luas_lahan'));

        return redirect()
            ->route('admin.petani.index')
            ->with('success', "Petani \"{$request->nama_petani}\" berhasil ditambahkan.");
    }

    /**
     * Detail petani: profil + riwayat panen + stok gabah tersimpan.
     */
    public function show(int $id)
    {
        $petani = Petani::with('kelompokTani', 'login')
            ->withCount('panen')
            ->findOrFail($id);

        // Stok gabah aktif milik petani ini (sudah fisik masuk ke lumbung)
        $stokAktif = PenyimpananGabah::whereHas('detailPanen.panen', function ($q) use ($id) {
                $q->where('id_petani', $id);
            })
            ->where('status', 'tersimpan')
            ->with([
                'detailPanen.jenisGabah',
                'slotLumbung.lumbung',
            ])
            ->orderBy('tanggal_masuk')
            ->get();

        $totalStok = $stokAktif->sum('jumlah');

        // Instruksi yang masih pending (belum masuk lumbung)
        $instruksiPending = \App\Models\InstruksiPenyimpanan::whereHas('detailPanen.panen', function ($q) use ($id) {
                $q->where('id_petani', $id);
            })
            ->where('status', 'pending')
            ->with([
                'detailPanen.jenisGabah',
                'slotLumbung.lumbung',
            ])
            ->orderBy('tanggal_instruksi')
            ->get();

        $totalPending = $instruksiPending->sum('jumlah');

        // Riwayat panen dengan pagination
        $riwayatPanen = Panen::where('id_petani', $id)
            ->with('detailPanen.jenisGabah')
            ->orderBy('tanggal_panen', 'desc')
            ->paginate(10);

        return view('admin.petani.show', compact('petani', 'stokAktif', 'totalStok', 'instruksiPending', 'totalPending', 'riwayatPanen'));
    }

    /**
     * Form edit data petani.
     */
    public function edit(int $id)
    {
        $petani       = Petani::findOrFail($id);
        $kelompokList = KelompokTani::orderBy('nama_kelompok')->get();

        return view('admin.petani.edit', compact('petani', 'kelompokList'));
    }

    /**
     * Update data petani.
     */
    public function update(Request $request, int $id)
    {
        $petani = Petani::findOrFail($id);

        $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok_tani,id_kelompok'],
            'nama_petani' => ['required', 'string', 'max:100'],
            'luas_lahan'  => ['required', 'numeric', 'min:0.01', 'max:999.99'],
        ], [
            'id_kelompok.required' => 'Kelompok tani wajib dipilih.',
            'nama_petani.required' => 'Nama petani wajib diisi.',
            'luas_lahan.min'       => 'Luas lahan minimal 0.01 hektar.',
        ]);

        $petani->update($request->only('id_kelompok', 'nama_petani', 'luas_lahan'));

        return redirect()
            ->route('admin.petani.show', $id)
            ->with('success', 'Data petani berhasil diperbarui.');
    }

    /**
     * Hapus data petani.
     * Tidak bisa dihapus jika sudah memiliki riwayat panen.
     */
    public function destroy(int $id)
    {
        $petani = Petani::withCount('panen')->findOrFail($id);

        if ($petani->panen_count > 0) {
            return back()->withErrors([
                'hapus' => "Petani \"{$petani->nama_petani}\" tidak dapat dihapus karena sudah memiliki riwayat panen.",
            ]);
        }

        $nama = $petani->nama_petani;
        $petani->delete();

        return redirect()
            ->route('admin.petani.index')
            ->with('success', "Data petani \"{$nama}\" berhasil dihapus.");
    }

    /**
     * API endpoint untuk Tom Select search.
     * Digunakan untuk autocomplete pemilihan petani.
     */
    public function apiSearch(Request $request)
    {
        $query = Petani::with('kelompokTani');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_petani', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $petani = $query->orderBy('nama_petani')->paginate(15);

        return response()->json([
            'data'         => $petani->items(),
            'current_page' => $petani->currentPage(),
            'last_page'    => $petani->lastPage(),
            'total'        => $petani->total(),
        ]);
    }

    /**
     * API endpoint untuk mendapatkan single petani.
     * Digunakan untuk menampilkan old value saat validation error.
     */
    public function apiShow($id)
    {
        $petani = Petani::with('kelompokTani')->findOrFail($id);

        return response()->json($petani);
    }
}