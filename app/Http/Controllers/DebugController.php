<?php

namespace App\Http\Controllers;

use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DebugController extends Controller
{
    /**
     * Debug login - cek apakah username ada di database
     */
    public function checkLogin(Request $request)
    {
        $username = $request->input('username', 'admin');
        
        $login = Login::where('username', $username)->first();
        
        if (!$login) {
            return response()->json([
                'status' => 'error',
                'message' => "Username '{$username}' tidak ditemukan di database",
                'data' => null,
            ]);
        }

        return response()->json([
            'status' => 'found',
            'message' => "Username '{$username}' ditemukan",
            'data' => [
                'id_login' => $login->id_login,
                'username' => $login->username,
                'role' => $login->role,
                'id_admin' => $login->id_admin,
                'id_pengelola' => $login->id_pengelola,
                'id_petani' => $login->id_petani,
                'password_hash' => substr($login->password, 0, 20) . '...',
            ],
        ]);
    }

    /**
     * Debug password - cek apakah password cocok
     */
    public function checkPassword(Request $request)
    {
        $username = $request->input('username', 'admin');
        $password = $request->input('password', '');

        $login = Login::where('username', $username)->first();

        if (!$login) {
            return response()->json([
                'status' => 'error',
                'message' => "Username '{$username}' tidak ditemukan",
            ]);
        }

        $isMatch = Hash::check($password, $login->password);

        return response()->json([
            'status' => 'checked',
            'message' => $isMatch ? 'Password cocok' : 'Password tidak cocok',
            'password_match' => $isMatch,
            'username' => $username,
            'role' => $login->role,
        ]);
    }

    /**
     * Debug session - cek apa yang tersimpan di session
     */
    public function checkSession(Request $request)
    {
        return response()->json([
            'session_data' => [
                'login_id' => session('login_id'),
                'role' => session('role'),
                'ref_id' => session('ref_id'),
                'nama' => session('nama'),
            ],
            'all_session' => session()->all(),
        ]);
    }

    /**
     * Debug all logins - lihat semua akun di database
     */
    public function allLogins()
    {
        $logins = Login::select('id_login', 'username', 'role', 'id_admin', 'id_pengelola', 'id_petani')->get();

        return response()->json([
            'total' => $logins->count(),
            'logins' => $logins,
        ]);
    }
}
