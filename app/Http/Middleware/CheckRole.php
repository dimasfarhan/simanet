<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Mengecek apakah user sudah login atau belum
        if (!$request->user()) {
            return $this->baseResponse(401, 'error', ['msg' => 'Unauthorized']);
        }

        // Mengecek apakah role user termasuk dalam daftar role yang diizinkan
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Jika role user tidak terdaftar di dalam daftar role yang diizinkan, kembalikan response Unauthorized
        return $this->baseResponse(401, 'error', ['msg' => 'Unauthorized']);
    }
}
