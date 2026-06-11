<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * NoCacheHeaders Middleware
 *
 * Menambahkan HTTP response headers agar browser tidak menyimpan
 * halaman dashboard/login di cache.
 *
 * Dengan ini, setiap kali user menekan tombol Back, browser akan
 * meminta ulang halaman ke server (bukan menampilkan dari cache).
 * Middleware EnsureLogin/role akan menangkap request tersebut
 * dan meredirect ke dashboard yang sesuai jika sudah login.
 */
class NoCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
