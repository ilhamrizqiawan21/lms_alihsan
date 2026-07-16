<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogLogin;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login', [
            'branding' => [
                'school_name' => school_setting('school_name', 'Nama Sekolah'),
                'school_short_name' => school_setting('school_short_name', 'LMS'),
                'school_motto' => school_setting('motto', 'Learning Management System'),
                'school_address' => school_setting('address', 'Alamat sekolah belum diatur'),
                'logo_url' => school_logo_url(),
            ],
            'loginUrl' => route('login.post'),
            'year' => date('Y'),
        ]);
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
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            // Ganti ID sesi setelah autentikasi untuk mencegah session fixation.
            $request->session()->regenerate();

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

            // Redirect berdasarkan role. Untuk request Inertia, pakai full visit agar aman
            // saat target intended masih halaman Blade.
            $intendedUrl = $request->session()->pull('url.intended', $this->redirectToByRole($user));

            if ($request->header('X-Inertia')) {
                return Inertia::location($intendedUrl);
            }

            return redirect($intendedUrl);
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
