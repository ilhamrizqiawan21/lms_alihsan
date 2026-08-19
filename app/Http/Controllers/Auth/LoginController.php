<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login', [
            'branding' => [
                'school_name' => school_setting('school_name', 'Nama Sekolah'),
                'school_short_name' => school_setting('school_short_name', 'LMS'),
                'school_motto' => school_setting('motto', 'Learning Management System'),
                'school_address' => school_setting('address', 'Alamat sekolah belum diatur'),
                'support_contact' => school_setting('whatsapp') ?: school_setting('phone'),
                'logo_url' => school_logo_url(),
            ],
            'loginUrl' => route('login.post'),
            'year' => date('Y'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $ip = $request->ip();
        $throttleKey = Str::lower($username . '|' . $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
        }

        $loginField = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginField => $username, 'password' => $request->password], $request->filled('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            $request->session()->regenerate();

            LogLogin::create([
                'user_id' => $user->id,
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'role' => $user->role?->nama_role ?? 'unknown',
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'login_time' => now(),
            ]);

            RateLimiter::clear($throttleKey);

            $defaultUrl = $this->redirectToByRole($user);
            $intendedUrl = $request->session()->pull('url.intended', $defaultUrl);
            $intendedUrl = $this->intendedUrlIsAllowedForRole($intendedUrl, $user->role?->nama_role, $request)
                ? $intendedUrl
                : $defaultUrl;

            if ($request->header('X-Inertia')) {
                return Inertia::location($intendedUrl);
            }

            return redirect($intendedUrl);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->with('error', 'Username atau password salah.')->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

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

    private function intendedUrlIsAllowedForRole(?string $url, ?string $role, Request $request): bool
    {
        if (! $url || ! $role) {
            return false;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return false;
        }

        // Absolute/protocol-relative intended URLs must remain on this application host.
        if (isset($parts['host']) && ! hash_equals((string) $request->getHost(), (string) $parts['host'])) {
            return false;
        }

        if (isset($parts['scheme']) && ! hash_equals((string) $request->getScheme(), (string) $parts['scheme'])) {
            return false;
        }

        $path = '/' . ltrim((string) ($parts['path'] ?? ''), '/');

        return match ($role) {
            'admin' => str_starts_with($path, '/admin'),
            'guru' => str_starts_with($path, '/guru'),
            'siswa' => str_starts_with($path, '/siswa'),
            'kepala_sekolah' => str_starts_with($path, '/kepsek'),
            default => false,
        };
    }
}
