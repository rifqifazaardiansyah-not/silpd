<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Middleware untuk validasi role dan redirect sesuai role.
     *
     * Cara kerja:
     * 1. Cek apakah user sudah login (session login_id)
     * 2. Cek apakah role user cocok dengan role yang diizinkan
     * 3. Jika tidak, redirect ke dashboard sesuai role user atau ke login
     *
     * Penggunaan di routes:
     *   Route::middleware('role:admin')->group(function () { ... });
     *   Route::middleware('role:pengelola,admin')->group(function () { ... });
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah sudah login
        if (!session()->has('login_id') || !session()->has('role')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userRole = session('role');

        // Cek apakah role user ada di list role yang diizinkan
        if (!in_array($userRole, $roles)) {
            // Role tidak sesuai, redirect ke dashboard sesuai rolenya
            $dashboardRoutes = [
                'admin' => 'admin.dashboard',
                'pengelola' => 'pengelola.dashboard',
                'petani' => 'petani.dashboard',
            ];

            $redirectRoute = $dashboardRoutes[$userRole] ?? 'login';

            return redirect()->route($redirectRoute)
                ->with('warning', 'Anda tidak memiliki akses ke halaman tersebut');
        }

        return $next($request);
    }
}
