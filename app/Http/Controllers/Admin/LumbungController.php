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
            $lumbung->total_kapasitas  = (float) $lumbung->slotLumbung->sum('kapasitas');
            $lumbung->total_tersedia   = (float) $lumbung->slotLumbung->sum('kapasitas_tersedia');
            $lumbung->total_terpakai   = $lumbung->total_kapasitas - $lumbung->total_tersedia;
            $lumbung->persen_terpakai  = $lumbung->total_kapasitas > 0
                ? round(($lumbung->total_terpakai / $lumbung->total_kapasitas) * 100, 2)
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
            'pengelola'    => ['nullable', 'array'],
            'pengelola.*.checked' => ['nullable'],
            'pengelola.*.peran'   => ['nullable', 'in:pemilik_akun,anggota'],
        ], [
            'nama_lumbung.required' => 'Nama lumbung wajib diisi.',
            'nama_lumbung.unique'   => 'Nama lumbung sudah terdaftar.',
        ]);

        // Buat lumbung
        $lumbung = Lumbung::create(['nama_lumbung' => $request->nama_lumbung]);

        // Attach pengelola ke lumbung via pivot table
        if ($request->filled('pengelola')) {
            $pengelolaData = [];
            foreach ($request->pengelola as $idPengelola => $data) {
                if (isset($data['checked']) && $data['checked']) {
                    $pengelolaData[$idPengelola] = [
                        'peran' => $data['peran'] ?? 'anggota',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            if (!empty($pengelolaData)) {
                $lumbung->pengelola()->attach($pengelolaData);
            }
        }

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
        $totalKapasitas = (float) $lumbung->slotLumbung->sum('kapasitas');
        $totalTersedia  = (float) $lumbung->slotLumbung->sum('kapasitas_tersedia');
        $totalTerpakai  = $totalKapasitas - $totalTersedia;
        $persenTerpakai = $totalKapasitas > 0
            ? round(($totalTerpakai / $totalKapasitas) * 100, 2)
            : 0;

        // Notifikasi slot hampir penuh (kapasitas tersedia < 20%)
        $thresholdKapasitas = 20;
        $slotHampirPenuh = $lumbung->slotLumbung->filter(function($slot) use ($thresholdKapasitas) {
            $persenTersedia = $slot->kapasitas > 0 
                ? ($slot->kapasitas_tersedia / $slot->kapasitas) * 100 
                : 0;
            return $persenTersedia < $thresholdKapasitas;
        });

        // Notifikasi gabah kadaluarsa (> 90 hari)
        $batasHari = config('silpd.batas_hari_simpan', 90);
        $batasDate = \Carbon\Carbon::now()->subDays($batasHari);
        
        $gabahKadaluarsa = collect();
        foreach ($lumbung->slotLumbung as $slot) {
            foreach ($slot->penyimpananGabah as $penyimpanan) {
                if ($penyimpanan->status === 'tersimpan' && 
                    \Carbon\Carbon::parse($penyimpanan->tanggal_masuk)->lte($batasDate)) {
                    $gabahKadaluarsa->push($penyimpanan);
                }
            }
        }

        // Daftar stok gabah (untuk tabel di bawah) - sorted by FIFO
        $stokList = collect();
        foreach ($lumbung->slotLumbung as $slot) {
            foreach ($slot->penyimpananGabah as $penyimpanan) {
                if ($penyimpanan->status === 'tersimpan') {
                    $stokList->push($penyimpanan);
                }
            }
        }
        $stokList = $stokList->sortBy('tanggal_masuk')->values();

        return view('admin.lumbung.show', compact(
            'lumbung',
            'totalKapasitas',
            'totalTersedia',
            'totalTerpakai',
            'persenTerpakai',
            'slotHampirPenuh',
            'gabahKadaluarsa',
            'stokList',
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
            'pengelola'    => ['nullable', 'array'],
            'pengelola.*.checked' => ['nullable'],
            'pengelola.*.peran'   => ['nullable', 'in:pemilik_akun,anggota'],
        ], [
            'nama_lumbung.required' => 'Nama lumbung wajib diisi.',
            'nama_lumbung.unique'   => 'Nama lumbung sudah digunakan.',
        ]);

        // Update nama lumbung
        $lumbung->update(['nama_lumbung' => $request->nama_lumbung]);

        // Sync pengelola (update relasi many-to-many)
        if ($request->filled('pengelola')) {
            $pengelolaData = [];
            foreach ($request->pengelola as $idPengelola => $data) {
                if (isset($data['checked']) && $data['checked']) {
                    $pengelolaData[$idPengelola] = [
                        'peran' => $data['peran'] ?? 'anggota',
                        'updated_at' => now(),
                    ];
                }
            }
            
            $lumbung->pengelola()->sync($pengelolaData);
        } else {
            // Jika tidak ada pengelola yang dipilih, hapus semua relasi
            $lumbung->pengelola()->detach();
        }

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