<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     * Jika sudah login, redirect ke dashboard sesuai role.
     */
    public function showLoginForm()
    {
        if (session()->has('login_id')) {
            return $this->redirectByRole(session('role'));
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     * Menggunakan tabel `login` custom (bukan tabel users bawaan Laravel).
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Rate limiter: maksimal 5 percobaan per menit per IP+username
        $throttleKey = strtolower($request->username) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Cari akun berdasarkan username
        $loginData = Login::where('username', $request->username)->first();

        // Verifikasi password (hash Argon2)
        if (! $loginData || ! Hash::check($request->password, $loginData->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        // Ambil nama tampilan sesuai role
        $nama = $this->getNamaPengguna($loginData);

        // Simpan data sesi
        $request->session()->regenerate();
        $request->session()->put([
            'login_id'  => $loginData->id_login,
            'role'      => $loginData->role,
            'nama'      => $nama,
            'ref_id'    => $this->getRefId($loginData),
        ]);

        return $this->redirectByRole($loginData->role);
    }

    /**
     * Logout: hapus session dan redirect ke halaman login.
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Redirect ke dashboard sesuai role pengguna.
     */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'pengelola' => redirect()->route('pengelola.dashboard'),
            'petani'    => redirect()->route('petani.dashboard'),
            default     => redirect()->route('login')->withErrors(['username' => 'Role tidak dikenali.']),
        };
    }

    /**
     * Ambil nama tampilan pengguna berdasarkan role.
     */
    private function getNamaPengguna(Login $loginData): string
    {
        return match ($loginData->role) {
            'petani'    => $loginData->petani?->nama_petani ?? $loginData->username,
            'pengelola' => $loginData->pengelola?->nama_pengelola ?? $loginData->username,
            'admin'     => $loginData->admin?->nama_admin ?? $loginData->username,
            default     => $loginData->username,
        };
    }

    /**
     * Ambil ID referensi entitas (id_petani / id_pengelola / id_admin).
     */
    private function getRefId(Login $loginData): ?int
    {
        return match ($loginData->role) {
            'petani'    => $loginData->id_petani,
            'pengelola' => $loginData->id_pengelola,
            'admin'     => $loginData->id_admin,
            default     => null,
        };
    }
}