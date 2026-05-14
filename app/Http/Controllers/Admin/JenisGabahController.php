<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisGabah;
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

        return view('admin.jenis-gabah.index', compact('jenisGabahList'));
    }

    /**
     * Form tambah jenis gabah.
     */
    public function create()
    {
        return view('admin.jenis-gabah.create');
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