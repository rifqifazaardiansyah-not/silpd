<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisGabah;
use App\Models\PenyimpananGabah;
use Illuminate\Http\Request;

class JenisGabahController extends Controller
{
    /**
     * Daftar semua jenis gabah.
     */
    public function index(Request $request)
    {
        $query = JenisGabah::withCount('detailPanen');

        if ($request->filled('search')) {
            $query->where('nama_jenis', 'like', '%' . $request->search . '%');
        }

        $jenisGabahList = $query->orderBy('nama_jenis')->paginate(15);

        // Hitung stok tersimpan dan pending untuk setiap jenis gabah
        $stokPerJenis = [];
        $pendingPerJenis = [];
        
        foreach ($jenisGabahList as $jenis) {
            // Stok tersimpan
            $stokPerJenis[$jenis->id_jenis_gabah] = (float) PenyimpananGabah::whereHas('detailPanen', function ($q) use ($jenis) {
                    $q->where('id_jenis_gabah', $jenis->id_jenis_gabah);
                })
                ->where('status', 'tersimpan')
                ->sum('jumlah');
            
            // Instruksi pending
            $pendingPerJenis[$jenis->id_jenis_gabah] = (float) \App\Models\InstruksiPenyimpanan::whereHas('detailPanen', function ($q) use ($jenis) {
                    $q->where('id_jenis_gabah', $jenis->id_jenis_gabah);
                })
                ->where('status', 'pending')
                ->sum('jumlah');
        }

        return view('admin.jenis-gabah.index', compact('jenisGabahList', 'stokPerJenis', 'pendingPerJenis'));
    }

    /**
     * Form tambah jenis gabah.
     */
    public function create()
    {
        return view('admin.jenis-gabah.create');
    }

    /**
     * Tampilkan detail jenis gabah.
     */
    public function show(int $id)
    {
        $jenisGabah = JenisGabah::withCount('detailPanen')->findOrFail($id);

        // Hitung total stok gabah jenis ini yang tersimpan
        $totalStok = (float) PenyimpananGabah::whereHas('detailPanen', function ($q) use ($id) {
                $q->where('id_jenis_gabah', $id);
            })
            ->where('status', 'tersimpan')
            ->sum('jumlah');

        // Hitung total instruksi pending
        $totalPending = (float) \App\Models\InstruksiPenyimpanan::whereHas('detailPanen', function ($q) use ($id) {
                $q->where('id_jenis_gabah', $id);
            })
            ->where('status', 'pending')
            ->sum('jumlah');

        // Stok per lumbung untuk jenis gabah ini (FIFO)
        $stokPerLumbung = PenyimpananGabah::whereHas('detailPanen', function ($q) use ($id) {
                $q->where('id_jenis_gabah', $id);
            })
            ->where('status', 'tersimpan')
            ->with(['slotLumbung.lumbung', 'detailPanen.panen.petani'])
            ->orderBy('tanggal_masuk', 'asc') // FIFO: oldest first
            ->get()
            ->map(function ($item) {
                return (object)[
                    'slotLumbung' => $item->slotLumbung,
                    'jumlah' => (float) $item->jumlah,
                    'tanggal_masuk' => $item->tanggal_masuk,
                    'petani' => $item->detailPanen->panen->petani,
                ];
            });

        // Petani yang memiliki stok jenis gabah ini
        $petaniDenganStok = PenyimpananGabah::whereHas('detailPanen', function ($q) use ($id) {
                $q->where('id_jenis_gabah', $id);
            })
            ->where('status', 'tersimpan')
            ->with('detailPanen.panen.petani.kelompokTani')
            ->get()
            ->groupBy(function ($item) {
                return $item->detailPanen->panen->id_petani;
            })
            ->map(function ($group) {
                $petani = $group->first()->detailPanen->panen->petani;
                return (object)[
                    'id_petani' => $petani->id_petani,
                    'nama_petani' => $petani->nama_petani,
                    'kelompokTani' => $petani->kelompokTani,
                    'total_stok' => (float) $group->sum('jumlah'),
                ];
            })
            ->sortByDesc('total_stok')
            ->values();

        return view('admin.jenis-gabah.show', compact('jenisGabah', 'totalStok', 'totalPending', 'stokPerLumbung', 'petaniDenganStok'));
    }

    /**
     * Simpan jenis gabah baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => ['required', 'string', 'max:100', 'unique:jenis_gabah,nama_jenis'],
        ], [
            'nama_jenis.required' => 'Nama jenis gabah wajib diisi.',
            'nama_jenis.unique'   => 'Jenis gabah sudah terdaftar.',
            'nama_jenis.max'      => 'Nama jenis gabah maksimal 100 karakter.',
        ]);

        JenisGabah::create(['nama_jenis' => $request->nama_jenis]);

        return redirect()
            ->route('admin.jenis-gabah.index')
            ->with('success', "Jenis gabah \"{$request->nama_jenis}\" berhasil ditambahkan.");
    }

    /**
     * Form edit jenis gabah.
     */
    public function edit(int $id)
    {
        $jenisGabah = JenisGabah::withCount('detailPanen')->findOrFail($id);

        return view('admin.jenis-gabah.edit', compact('jenisGabah'));
    }

    /**
     * Update jenis gabah.
     */
    public function update(Request $request, int $id)
    {
        $jenisGabah = JenisGabah::findOrFail($id);

        $request->validate([
            'nama_jenis' => [
                'required', 'string', 'max:100',
                'unique:jenis_gabah,nama_jenis,' . $id . ',id_jenis_gabah',
            ],
        ], [
            'nama_jenis.required' => 'Nama jenis gabah wajib diisi.',
            'nama_jenis.unique'   => 'Nama jenis gabah sudah digunakan.',
        ]);

        $jenisGabah->update(['nama_jenis' => $request->nama_jenis]);

        return redirect()
            ->route('admin.jenis-gabah.index')
            ->with('success', 'Jenis gabah berhasil diperbarui.');
    }

    /**
     * Hapus jenis gabah.
     * Tidak bisa dihapus jika sudah dipakai di detail panen.
     */
    public function destroy(int $id)
    {
        $jenisGabah = JenisGabah::withCount('detailPanen')->findOrFail($id);

        if ($jenisGabah->detail_panen_count > 0) {
            return back()->withErrors([
                'hapus' => "Jenis gabah \"{$jenisGabah->nama_jenis}\" tidak dapat dihapus karena sudah digunakan pada {$jenisGabah->detail_panen_count} data panen.",
            ]);
        }

        $nama = $jenisGabah->nama_jenis;
        $jenisGabah->delete();

        return redirect()
            ->route('admin.jenis-gabah.index')
            ->with('success', "Jenis gabah \"{$nama}\" berhasil dihapus.");
    }
}