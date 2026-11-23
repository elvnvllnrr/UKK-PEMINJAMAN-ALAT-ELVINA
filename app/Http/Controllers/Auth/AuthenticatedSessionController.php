<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Menangani masuk dan keluar pengguna yang terautentikasi. */
class AuthenticatedSessionController extends Controller
{
    /** Menampilkan formulir masuk pengguna. */
    public function create(): View
    {
        return view('auth.login');
    }

    /** Memvalidasi kredensial, mengatur ulang percobaan login, dan memulai sesi. */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /** Mengakhiri sesi pengguna dan menghasilkan token sesi baru. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}









