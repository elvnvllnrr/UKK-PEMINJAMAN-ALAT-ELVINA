<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Memeriksa role pengguna sebelum permintaan diteruskan. */
class RoleMiddleware
{
    /** Meneruskan permintaan hanya jika pengguna terautentikasi dan rolenya diizinkan. */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        if (empty($roles) || in_array($userRole, $roles, true)) {
            return $next($request);
        }

        $dashboard = match ($userRole) {
            'admin' => '/admin/dashboard',
            'petugas' => '/petugas/dashboard',
            'peminjam' => '/peminjam/dashboard',
            default => null,
        };

        if ($dashboard !== null) {
            return redirect($dashboard)->with('error', 'Anda tidak memiliki akses ke halaman tersebut. Anda dialihkan ke dashboard sesuai role akun.');
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}









