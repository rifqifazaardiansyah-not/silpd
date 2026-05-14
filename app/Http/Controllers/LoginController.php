<?php

namespace App\Http\Controllers;

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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        // Cek Login tabel Admin
        $login = Login::where('email', $email)->first();

        if (!$login) {
            return back()->with('error', 'Email atau password salah');
        }

        if (!Hash::check($password, $login->password)) {
            return back()->with('error', 'Email atau password salah');
        }

        // Tentukan role berdasarkan ref_model
        $role = strtolower($login->ref_model);
        $refId = $login->ref_id;

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
            'role' => $role,
            'ref_id' => $refId,
            'name' => $user->nama ?? $user->name ?? $email,
            'email' => $email,
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
