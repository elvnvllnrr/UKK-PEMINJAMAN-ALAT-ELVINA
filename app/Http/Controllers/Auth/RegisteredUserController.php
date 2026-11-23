<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/** Memproses pendaftaran akun baru. */
class RegisteredUserController extends Controller
{
    /** Menampilkan formulir pendaftaran akun baru. */
    public function create(): View
    {
        return view('auth.register');
    }

    /** Membuat akun, mengirim event pendaftaran, lalu langsung masuk ke sesi. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Abaikan seluruh data role dari formulir; registrasi hanya menghasilkan peminjam.
            'role' => 'peminjam',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('peminjam.dashboard')
            ->with('success', 'Registrasi berhasil. Akun peminjam Anda siap digunakan.');
    }
}









