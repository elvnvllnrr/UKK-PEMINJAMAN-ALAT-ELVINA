<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Memproses permintaan tautan pemulihan kata sandi. */
class PasswordResetLinkController extends Controller
{
    /** Menampilkan formulir permintaan tautan pemulihan kata sandi. */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** Mengirim tautan pemulihan dan menampilkan hasilnya kepada pengguna. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Kirim tautan pemulihan dan terjemahkan status broker menjadi respons.
        $status = Password::sendResetLink(
        $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}









