<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Login;
use App\Models\Pengelola;
use App\Models\Petani;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    /**
     * Daftar semua akun login di sistem.
     *
     * Menampilkan akun dari semua role sekaligus, bisa difilter
     * berdasarkan role atau dicari berdasarkan username / nama pemilik.
     */
    public function index(Request $request)
    {
        $query = Login::with(['petani', 'pengelola', 'admin']);

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Pencarian berdasarkan username
        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('username', 'like', '%' . $keyword . '%')
                  ->orWhereHas('petani', fn ($q2) => $q2->where('nama_petani', 'like', '%' . $keyword . '%'))
                  ->orWhereHas('pengelola', fn ($q2) => $q2->where('nama_pengelola', 'like', '%' . $keyword . '%'))
                  ->orWhereHas('admin', fn ($q2) => $q2->where('nama_admin', 'like', '%' . $keyword . '%'));
            });
        }

        $akunList = $query->orderBy('role')->orderBy('username')->paginate(20)->withQueryString();

        // Tambahkan atribut nama_pemilik ke tiap record untuk kemudahan tampilan
        $akunList->getCollection()->transform(fn ($akun) => $this->appendNamaPemilik($akun));

        // Jumlah akun per role untuk tab / badge
        $jumlahPerRole = Login::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.akun.index', compact('akunList', 'jumlahPerRole'));
    }

    /**
     * Form buat akun baru.
     *
     * Admin memilih role terlebih dahulu. Berdasarkan role yang dipilih,
     * form menampilkan dropdown entitas yang belum punya akun.
     * (Petani / Pengelola / Admin yang sudah punya akun tidak ditampilkan.)
     */
    public function create(Request $request)
    {
        $role = $request->get('role', 'petani');

        $entitasTersedia = match ($role) {
            'petani'    => Petani::whereDoesntHave('login')->orderBy('nama_petani')->get(),
            'pengelola' => Pengelola::whereDoesntHave('login')->orderBy('nama_pengelola')->get(),
            'admin'     => Admin::whereDoesntHave('login')->orderBy('nama_admin')->get(),
            default     => collect(),
        };

        return view('admin.akun.create', compact('role', 'entitasTersedia'));
    }

    /**
     * Simpan akun baru.
     *
     * Validasi memastikan:
     * - Entitas yang dipilih sesuai role
     * - Entitas belum memiliki akun (tidak boleh double akun)
     * - Username unik di seluruh tabel login
     */
    public function store(Request $request)
    {
        $request->validate([
            'role'     => ['required', Rule::in(['petani', 'pengelola', 'admin'])],
            'ref_id'   => ['required', 'integer'],
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:login,username',
                'regex:/^[a-zA-Z0-9._-]+$/',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'role.required'      => 'Role wajib dipilih.',
            'role.in'            => 'Role tidak valid.',
            'ref_id.required'    => 'Pemilik akun wajib dipilih.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'username.regex'     => 'Username hanya boleh huruf, angka, titik, garis bawah, dan strip.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Validasi: entitas ref_id harus ada dan belum punya akun
        $this->validasiEntitasBelumPunyaAkun($request->role, $request->ref_id);

        $data = [
            'id_petani'    => null,
            'id_pengelola' => null,
            'id_admin'     => null,
            'username'     => $request->username,
            'password'     => bcrypt($request->password),
            'role'         => $request->role,
        ];

        // Isi kolom FK sesuai role
        $data[$this->getFkKolom($request->role)] = $request->ref_id;

        Login::create($data);

        return redirect()
            ->route('admin.akun.index', ['role' => $request->role])
            ->with('success', "Akun \"{$request->username}\" berhasil dibuat.");
    }

    /**
     * Detail akun: informasi login, role, dan entitas yang dimiliki.
     */
    public function show(int $id)
    {
        $akun = Login::with(['petani.kelompokTani', 'pengelola.lumbung', 'admin'])->findOrFail($id);
        $akun = $this->appendNamaPemilik($akun);

        return view('admin.akun.show', compact('akun'));
    }

    /**
     * Form edit akun.
     * Hanya bisa mengubah username, bukan mengganti role atau pemilik akun.
     * Untuk ganti password, gunakan method resetPassword.
     */
    public function edit(int $id)
    {
        $akun = Login::with(['petani', 'pengelola', 'admin'])->findOrFail($id);
        $akun = $this->appendNamaPemilik($akun);

        return view('admin.akun.edit', compact('akun'));
    }

    /**
     * Update username akun.
     * Role dan ref_id (pemilik akun) tidak bisa diubah dari sini.
     */
    public function update(Request $request, int $id)
    {
        $akun = Login::findOrFail($id);

        $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('login', 'username')->ignore($akun->id_login, 'id_login'),
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan akun lain.',
            'username.regex'    => 'Username hanya boleh huruf, angka, titik, garis bawah, dan strip.',
        ]);

        $akun->update(['username' => $request->username]);

        return redirect()
            ->route('admin.akun.show', $akun->id_login)
            ->with('success', 'Username berhasil diperbarui.');
    }

    /**
     * Hapus akun login.
     *
     * Menghapus akun saja, data entitas (petani/pengelola/admin) tetap ada.
     * Admin tidak bisa menghapus akunnya sendiri yang sedang aktif.
     */
    public function destroy(int $id)
    {
        $akun = Login::findOrFail($id);

        // Cegah admin menghapus akun dirinya sendiri
        if ($akun->id_login === session('login_id')) {
            return back()->withErrors([
                'hapus' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.',
            ]);
        }

        $username = $akun->username;
        $akun->delete();

        return redirect()
            ->route('admin.akun.index')
            ->with('success', "Akun \"{$username}\" berhasil dihapus.");
    }

    /**
     * Reset password akun tanpa perlu mengetahui password lama.
     * Hanya admin yang bisa melakukan ini.
     */
    public function resetPassword(Request $request, int $id)
    {
        $akun = Login::findOrFail($id);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $akun->update(['password' => bcrypt($request->password)]);

        return redirect()
            ->route('admin.akun.show', $akun->id_login)
            ->with('success', "Password akun \"{$akun->username}\" berhasil direset.");
    }

    /**
     * Admin mengubah password akunnya sendiri.
     * Memerlukan verifikasi password lama sebelum bisa diganti.
     */
    public function gantiPasswordSendiri(Request $request)
    {
        $akun = Login::findOrFail(session('login_id'));

        $request->validate([
            'password_lama' => ['required', 'string'],
            'password'      => ['required', 'string', 'min:8', 'confirmed', 'different:password_lama'],
        ], [
            'password_lama.required'  => 'Password lama wajib diisi.',
            'password.required'       => 'Password baru wajib diisi.',
            'password.min'            => 'Password baru minimal 8 karakter.',
            'password.confirmed'      => 'Konfirmasi password baru tidak cocok.',
            'password.different'      => 'Password baru tidak boleh sama dengan password lama.',
        ]);

        // Verifikasi password lama
        if (! \Illuminate\Support\Facades\Hash::check($request->password_lama, $akun->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $akun->update(['password' => bcrypt($request->password)]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Password berhasil diubah. Silakan login ulang.');
    }

    /**
     * Tampilkan form ganti password sendiri.
     */
    public function formGantiPasswordSendiri()
    {
        return view('admin.akun.ganti-password');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Validasi bahwa entitas (petani/pengelola/admin) ada di database
     * dan belum memiliki akun login.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validasiEntitasBelumPunyaAkun(string $role, int $refId): void
    {
        $model     = $this->getModel($role);
        $fkKolom   = $this->getFkKolom($role);
        $entitas   = $model::find($refId);

        if (! $entitas) {
            \Illuminate\Validation\ValidationException::withMessages([
                'ref_id' => 'Data ' . $role . ' tidak ditemukan.',
            ]);
        }

        $sudahPunyaAkun = Login::where($fkKolom, $refId)->exists();

        if ($sudahPunyaAkun) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'ref_id' => ucfirst($role) . ' ini sudah memiliki akun login.',
            ]);
        }
    }

    /**
     * Dapatkan nama class model berdasarkan role.
     */
    private function getModel(string $role): string
    {
        return match ($role) {
            'petani'    => Petani::class,
            'pengelola' => Pengelola::class,
            'admin'     => Admin::class,
            default     => throw new \InvalidArgumentException("Role tidak dikenal: {$role}"),
        };
    }

    /**
     * Dapatkan nama kolom FK di tabel login berdasarkan role.
     */
    private function getFkKolom(string $role): string
    {
        return match ($role) {
            'petani'    => 'id_petani',
            'pengelola' => 'id_pengelola',
            'admin'     => 'id_admin',
            default     => throw new \InvalidArgumentException("Role tidak dikenal: {$role}"),
        };
    }

    /**
     * Tambahkan atribut nama_pemilik dan label role ke objek Login
     * agar view tidak perlu logika kondisional berulang.
     */
    private function appendNamaPemilik(Login $akun): Login
    {
        $akun->nama_pemilik = match ($akun->role) {
            'petani'    => $akun->petani?->nama_petani,
            'pengelola' => $akun->pengelola?->nama_pengelola,
            'admin'     => $akun->admin?->nama_admin,
            default     => '-',
        } ?? '-';

        $akun->label_role = match ($akun->role) {
            'petani'    => 'Petani',
            'pengelola' => 'Pengelola Lumbung',
            'admin'     => 'Admin Desa',
            default     => ucfirst($akun->role),
        };

        return $akun;
    }
}