<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! config('security.force_password_change', false) || ! $user->is_password_default) {
            return $next($request);
        }

        if ($request->routeIs('*.pengaturan-akun') || $request->routeIs('*.pengaturan.update') || $request->routeIs('login') || $request->routeIs('login.post') || $request->routeIs('logout')) {
            return $next($request);
        }

        $role = $user->role?->nama_role;
        $route = match ($role) {
            'admin' => 'admin.pengaturan-akun',
            'guru' => 'guru.pengaturan',
            'siswa' => 'siswa.pengaturan',
            'kepala_sekolah' => null,
            default => null,
        };

        if ($route && app('router')->has($route)) {
            return redirect()->route($route)->with('warning', 'Demi keamanan, silakan ubah password default sebelum melanjutkan.');
        }

        return $next($request);
    }
}
