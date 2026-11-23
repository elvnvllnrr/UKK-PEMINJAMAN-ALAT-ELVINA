<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;

/** Memproses autentikasi dan sesi pengguna admin. */
class LoginLogoutController extends Controller
{
    /** Menampilkan formulir masuk admin. */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /** Memvalidasi kredensial admin dan membuka dashboard. */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cocokkan kredensial admin dengan username yang disimpan.
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            // Rekam keberhasilan masuk sebelum berpindah halaman.
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Login',
                'deskripsi' => 'User ' . Auth::user()->username . ' telah login',
            ]);

            // Arahkan admin ke dashboard administrasi.
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard')->with('success', 'Selamat datang, Admin!');
            } elseif ($user->role === 'petugas') {
                return redirect('/petugas/dashboard')->with('success', 'Selamat datang, Petugas!');
            } else {
                return redirect('/peminjam/dashboard')->with('success', 'Selamat datang, Peminjam!');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /** Mengakhiri sesi admin dan kembali ke halaman masuk. */
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