<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Middleware untuk redirect user yang sudah login agar tidak bisa akses halaman login.
     *
     * Cara kerja:
     * 1. Cek apakah user sudah login (session login_id)
     * 2. Jika sudah login → redirect ke dashboard sesuai role
     * 3. Jika belum login → lanjutkan ke halaman login (tampilkan form login)
     *
     * Penggunaan di routes:
     *   Route::get('/login', [LoginController::class, 'showLoginForm'])
     *        ->middleware('guest')  // atau middleware custom ini
     *        ->name('login');
     *
     * Tujuan: Mencegah user yang sudah login membuka halaman login lagi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah sudah login (cek session dari LoginController)
        if (session()->has('login_id') && session()->has('role')) {
            $userRole = session('role');

            // Redirect ke dashboard sesuai role
            $dashboardRoutes = [
                'admin' => 'admin.dashboard',
                'pengelola' => 'pengelola.dashboard',
                'petani' => 'petani.dashboard',
            ];

            $redirectRoute = $dashboardRoutes[$userRole] ?? 'login';

            return redirect()->route($redirectRoute);
        }

        // Jika belum login, lanjutkan ke halaman login
        return $next($request);
    }
}
