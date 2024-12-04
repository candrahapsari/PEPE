<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login'); // Sesuaikan dengan nama file view login Anda
    }

    // Menangani proses login
    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Mencoba login dengan data yang diberikan
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            // Jika berhasil login, arahkan ke dashboard sesuai peran
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'karyawan') {
                return redirect()->route('karyawan.dashboard');
            }

            // Jika tidak ada role khusus, arahkan ke home
            return redirect()->intended('/');
        }

        // Jika login gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    // Logout pengguna
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus sesi dan arahkan ke halaman login
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
