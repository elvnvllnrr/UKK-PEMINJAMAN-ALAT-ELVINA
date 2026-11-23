<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Menentukan tampilan pengingat verifikasi surel. */
class EmailVerificationPromptController extends Controller
{
    /** Mengalihkan pengguna terverifikasi dan menampilkan pengingat bagi yang belum. */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
        ? redirect()->intended(route('dashboard', absolute: false))
        : view('auth.verify-email');
    }
}









