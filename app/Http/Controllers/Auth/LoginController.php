<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Pengelola;
use App\Models\Petani;
use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login berdasarkan role.
     *
     * Mengecek 3 tabel:
     * 1. Admin (role: admin)
     * 2. Pengelola (role: pengelola)
     * 3. Petani (role: petani)
     *
     * Menyimpan session:
     * - role: admin, pengelola, atau petani
     * - ref_id: ID pengguna sesuai tabel role
     * - name: nama pengguna
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $input = $request->username;  // Bisa email atau username
        $password = $request->password;

        // Cek di tabel Login - cari berdasarkan username (bisa email atau username biasa)
        $login = Login::where('username', $input)->first();

        if (!$login) {
            return back()->with('error', 'Username/Email atau password salah');
        }

        if (!Hash::check($password, $login->password)) {
            return back()->with('error', 'Username/Email atau password salah');
        }

        // Tentukan role dari enum role
        $role = strtolower($login->role);
        $refId = match($role) {
            'petani' => $login->id_petani,
            'pengelola' => $login->id_pengelola,
            'admin' => $login->id_admin,
            default => null,
        };

        // Verifikasi user masih aktif di tabel masing-masing
        $user = match ($role) {
            'admin' => Admin::find($refId),
            'pengelola' => Pengelola::find($refId),
            'petani' => Petani::find($refId),
            default => null,
        };

        if (!$user) {
            return back()->with('error', 'Pengguna tidak ditemukan atau sudah dihapus');
        }

        // Simpan ke session
        session([
            'login_id' => $login->id_login,
            'role' => $role,
            'ref_id' => $refId,
            'nama' => $user->nama_petani ?? $user->nama_pengelola ?? $user->nama_admin ?? $input,
        ]);

        // Redirect ke dashboard sesuai role
        $redirects = [
            'admin' => 'admin.dashboard',
            'pengelola' => 'pengelola.dashboard',
            'petani' => 'petani.dashboard',
        ];

        return redirect()->route($redirects[$role] ?? 'login');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logout berhasil');
    }
}
