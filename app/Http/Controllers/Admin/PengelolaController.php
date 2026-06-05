<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\Lumbung;
use App\Models\Pengelola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengelolaController extends Controller
{
    /**
     * Daftar semua pengelola beserta lumbung yang mereka kelola
     * dan status ketersediaan akun login.
     */
    public function index(Request $request)
    {
        $query = Pengelola::with(['lumbung', 'login']);

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pengelola', 'like', '%' . $request->cari . '%')
                  ->orWhere('no_hp', 'like', '%' . $request->cari . '%');
            });
        }

        // Filter: hanya yang punya / belum punya akun
        if ($request->filled('status_akun')) {
            if ($request->status_akun === 'punya_akun') {
                $query->whereHas('login');
            } elseif ($request->status_akun === 'belum_akun') {
                $query->whereDoesntHave('login');
            }
        }

        $pengelolaList = $query->orderBy('nama_pengelola')->paginate(15)->withQueryString();

        return view('admin.pengelola.index', compact('pengelolaList'));
    }

    /**
     * Form tambah pengelola baru.
     * Bisa sekaligus membuat akun login di form yang sama (opsional).
     */
    public function create()
    {
        return view('admin.pengelola.create');
    }

    /**
     * Simpan pengelola baru.
     *
     * Jika admin memilih "Buat Akun", akun login dengan role 'pengelola'
     * langsung dibuat dalam satu transaksi bersamaan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengelola' => ['required', 'string', 'max:100'],
            'no_hp'          => [
                'required',
                'string',
                'max:15',
                'regex:/^(\+62|0)[0-9]{8,13}$/',
                'unique:pengelola,no_hp',
            ],
            // Akun login (opsional)
            'buat_akun'      => ['nullable', 'boolean'],
            'username'       => [
                'nullable',
                'required_if:buat_akun,1',
                'string',
                'max:50',
                'unique:login,username',
                'regex:/^[a-zA-Z0-9._-]+$/',
            ],
            'password'       => [
                'nullable',
                'required_if:buat_akun,1',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'nama_pengelola.required' => 'Nama pengelola wajib diisi.',
            'no_hp.required'          => 'Nomor HP wajib diisi.',
            'no_hp.regex'             => 'Format nomor HP tidak valid. Contoh: 08123456789 atau +6281234567890.',
            'no_hp.unique'            => 'Nomor HP sudah terdaftar untuk pengelola lain.',
            'username.required_if'    => 'Username wajib diisi jika membuat akun.',
            'username.unique'         => 'Username sudah digunakan.',
            'username.regex'          => 'Username hanya boleh huruf, angka, titik, garis bawah, dan strip.',
            'password.required_if'    => 'Password wajib diisi jika membuat akun.',
            'password.min'            => 'Password minimal 8 karakter.',
            'password.confirmed'      => 'Konfirmasi password tidak cocok.',
        ]);

        DB::transaction(function () use ($request) {
            $pengelola = Pengelola::create([
                'nama_pengelola' => $request->nama_pengelola,
                'no_hp'          => $request->no_hp,
            ]);

            if ($request->boolean('buat_akun') && $request->filled('username')) {
                Login::create([
                    'id_petani'    => null,
                    'id_pengelola' => $pengelola->id_pengelola,
                    'id_admin'     => null,
                    'username'     => $request->username,
                    'password'     => bcrypt($request->password),
                    'role'         => 'pengelola',
                ]);
            }
        });

        return redirect()
            ->route('admin.pengelola.index')
            ->with('success', "Pengelola {$request->nama_pengelola} berhasil ditambahkan.");
    }

    /**
     * Detail pengelola: profil, lumbung yang dikelola, dan akun login.
     */
    public function show(int $id)
    {
        $pengelola = Pengelola::with(['login', 'lumbung.slotLumbung'])->findOrFail($id);

        // Attach capacity attributes to each lumbung
        $pengelola->lumbung->each(function ($lumbung) {
            $totalKapasitas = (float) $lumbung->slotLumbung->sum('kapasitas');
            $totalTersedia  = (float) $lumbung->slotLumbung->sum('kapasitas_tersedia');
            $totalTerpakai  = $totalKapasitas - $totalTersedia;
            $persenTerpakai = $totalKapasitas > 0
                ? round(($totalTerpakai / $totalKapasitas) * 100, 2)
                : 0;

            $lumbung->total_kapasitas = $totalKapasitas;
            $lumbung->total_terpakai = $totalTerpakai;
            $lumbung->persen_terpakai = $persenTerpakai;
        });

        return view('admin.pengelola.show', compact('pengelola'));
    }

    /**
     * Form edit data pengelola.
     */
    public function edit(int $id)
    {
        $pengelola = Pengelola::with('login')->findOrFail($id);

        return view('admin.pengelola.edit', compact('pengelola'));
    }

    /**
     * Update data pengelola.
     * Perubahan data login (username/password) ditangani di AkunController,
     * bukan di sini — agar tanggung jawab tetap terpisah.
     */
    public function update(Request $request, int $id)
    {
        $pengelola = Pengelola::findOrFail($id);

        $request->validate([
            'nama_pengelola' => ['required', 'string', 'max:100'],
            'no_hp'          => [
                'required',
                'string',
                'max:15',
                'regex:/^(\+62|0)[0-9]{8,13}$/',
                Rule::unique('pengelola', 'no_hp')->ignore($pengelola->id_pengelola, 'id_pengelola'),
            ],
        ], [
            'nama_pengelola.required' => 'Nama pengelola wajib diisi.',
            'no_hp.required'          => 'Nomor HP wajib diisi.',
            'no_hp.regex'             => 'Format nomor HP tidak valid. Contoh: 08123456789.',
            'no_hp.unique'            => 'Nomor HP sudah digunakan pengelola lain.',
        ]);

        $pengelola->update([
            'nama_pengelola' => $request->nama_pengelola,
            'no_hp'          => $request->no_hp,
        ]);

        return redirect()
            ->route('admin.pengelola.show', $pengelola->id_pengelola)
            ->with('success', 'Data pengelola berhasil diperbarui.');
    }

    /**
     * Hapus pengelola.
     *
     * Tidak bisa dihapus jika:
     * - Masih mengelola satu atau lebih lumbung aktif.
     */
    public function destroy(int $id)
    {
        $pengelola = Pengelola::withCount('lumbung')->findOrFail($id);

        if ($pengelola->lumbung_count > 0) {
            return back()->withErrors([
                'hapus' => "Pengelola {$pengelola->nama_pengelola} tidak dapat dihapus karena masih mengelola {$pengelola->lumbung_count} lumbung. Pindahkan pengelola lumbung terlebih dahulu.",
            ]);
        }

        DB::transaction(function () use ($pengelola) {
            Login::where('id_pengelola', $pengelola->id_pengelola)->delete();
            $pengelola->delete();
        });

        return redirect()
            ->route('admin.pengelola.index')
            ->with('success', "Pengelola {$pengelola->nama_pengelola} berhasil dihapus.");
    }

    // =========================================================================
    // MANAJEMEN AKUN LOGIN PENGELOLA
    // Ditangani langsung di sini karena scope-nya kecil (buat & reset saja).
    // Untuk pengelolaan akun yang lebih luas, gunakan AkunController.
    // =========================================================================

    /**
     * Buat akun login baru untuk pengelola yang belum punya akun.
     * Dipanggil dari halaman show pengelola via tombol "Buat Akun Login".
     */
    public function buatAkun(Request $request, int $id)
    {
        $pengelola = Pengelola::findOrFail($id);

        // Cegah buat akun ganda
        if (Login::where('id_pengelola', $id)->exists()) {
            return back()->withErrors([
                'akun' => 'Pengelola ini sudah memiliki akun login.',
            ]);
        }

        $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:login,username',
                'regex:/^[a-zA-Z0-9._-]+$/',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'username.regex'     => 'Username hanya boleh huruf, angka, titik, garis bawah, dan strip.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        Login::create([
            'id_petani'    => null,
            'id_pengelola' => $pengelola->id_pengelola,
            'id_admin'     => null,
            'username'     => $request->username,
            'password'     => bcrypt($request->password),
            'role'         => 'pengelola',
        ]);

        return redirect()
            ->route('admin.pengelola.show', $pengelola->id_pengelola)
            ->with('success', "Akun login untuk {$pengelola->nama_pengelola} berhasil dibuat.");
    }

    /**
     * Reset password akun login pengelola.
     * Dipanggil dari halaman show pengelola via tombol "Reset Password".
     */
    public function resetPassword(Request $request, int $id)
    {
        $pengelola = Pengelola::findOrFail($id);

        $login = Login::where('id_pengelola', $id)->first();

        if (! $login) {
            return back()->withErrors([
                'akun' => 'Pengelola ini belum memiliki akun login.',
            ]);
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $login->update(['password' => bcrypt($request->password)]);

        return redirect()
            ->route('admin.pengelola.show', $pengelola->id_pengelola)
            ->with('success', "Password akun {$pengelola->nama_pengelola} berhasil direset.");
    }

    /**
     * Nonaktifkan (hapus) akun login pengelola tanpa menghapus data pengelolanya.
     * Berguna jika pengelola berganti orang tapi data lumbungnya tetap dipertahankan.
     */
    public function hapusAkun(int $id)
    {
        $pengelola = Pengelola::findOrFail($id);

        $deleted = Login::where('id_pengelola', $id)->delete();

        if (! $deleted) {
            return back()->withErrors([
                'akun' => 'Pengelola ini tidak memiliki akun login.',
            ]);
        }

        return redirect()
            ->route('admin.pengelola.show', $pengelola->id_pengelola)
            ->with('success', "Akun login {$pengelola->nama_pengelola} berhasil dihapus. Data pengelola tetap tersimpan.");
    }
}