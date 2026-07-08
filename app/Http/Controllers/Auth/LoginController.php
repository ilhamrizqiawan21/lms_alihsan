<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogLogin;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $ip = $request->ip();

        // Cek rate limiting
        $throttleKey = Str::lower($username . '|' . $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
        }

        $loginField = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt login
        if (Auth::attempt([$loginField => $username, 'password' => $request->password], $request->filled('remember'))) {
            $user = Auth::user();

            // Cek user aktif
            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            // Catat log login
            LogLogin::create([
                'user_id' => $user->id,
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'role' => $user->role?->nama_role ?? 'unknown',
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'login_time' => now(),
            ]);

            // Hapus percobaan login yang berhasil
            RateLimiter::clear($throttleKey);

            // Redirect berdasarkan role
            return redirect()->intended($this->redirectToByRole($user));
        }

        // Catat percobaan gagal
        RateLimiter::hit($throttleKey, 60);

        return back()->with('error', 'Username atau password salah.')->withInput($request->only('username'));
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    /**
     * Redirect berdasarkan role user.
     */
    protected function redirectToByRole($user): string
    {
        return match ($user->role?->nama_role) {
            'admin' => route('admin.dashboard'),
            'guru' => route('guru.dashboard'),
            'siswa' => route('siswa.dashboard'),
            'kepala_sekolah' => route('kepsek.dashboard'),
            default => '/',
        };
    }
}
