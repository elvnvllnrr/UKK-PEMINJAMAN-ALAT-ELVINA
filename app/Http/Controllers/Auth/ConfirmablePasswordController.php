<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Memvalidasi konfirmasi kata sandi pengguna. */
class ConfirmablePasswordController extends Controller
{
    /** Menampilkan formulir konfirmasi kata sandi. */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /** Memvalidasi kata sandi lalu menandai konfirmasi berhasil untuk sesi ini. */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}









