<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek role user
        $userRole = auth()->user()->role;

        // Mapping role ke permission
        $roleHierarchy = [
            'super_admin' => ['super_admin', 'admin', 'user', 'analyst', 'viewer'],
            'admin' => ['admin', 'user', 'analyst', 'viewer'],
            'user' => ['user', 'viewer'],
            'analyst' => ['analyst', 'viewer'],
            'viewer' => ['viewer'],
        ];

        // Cek apakah role user memiliki akses ke role yang diminta
        if (!in_array($role, $roleHierarchy[$userRole] ?? [])) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
