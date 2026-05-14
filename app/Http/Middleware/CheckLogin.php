<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Middleware untuk mengecek login dan role pengguna.
     *
     * Mengecek session login:
     * - role: tipe pengguna (admin, pengelola, petani)
     * - ref_id: ID pengguna (id_admin, id_pengelola, id_petani)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('role') || !session()->has('ref_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return $next($request);
    }
}
