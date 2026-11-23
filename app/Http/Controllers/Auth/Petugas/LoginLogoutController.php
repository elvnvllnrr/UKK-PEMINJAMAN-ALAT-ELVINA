<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;

/** Memproses autentikasi dan sesi pengguna petugas. */
class LoginLogoutController extends Controller
{
    /** Menampilkan halaman masuk. */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /** Memproses autentikasi pengguna. */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cocokkan kredensial petugas dengan username yang disimpan.
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            // Rekam keberhasilan masuk sebelum membuka dashboard petugas.
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Login',
                'deskripsi' => 'User ' . Auth::user()->username . ' telah login',
            ]);

            // Arahkan petugas ke dashboard operasionalnya.
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard')->with('success', 'Selamat datang, Admin!');
            } elseif ($user->role === 'petugas') {
                return redirect()->intended('/petugas/dashboard')->with('success', 'Selamat datang, Petugas!');
            } else {
                return redirect()->intended('/peminjam/dashboard')->with('success', 'Selamat datang, Peminjam!');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /** Mengakhiri sesi pengguna. */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Logout',
                'deskripsi' => 'User ' . Auth::user()->username . ' telah logout',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}