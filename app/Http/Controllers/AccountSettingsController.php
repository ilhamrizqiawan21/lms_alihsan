<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AccountSettingsController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->loadMissing(['role', 'siswa.kelas']);
        $role = $user->role?->nama_role;

        return Inertia::render('Account/Pengaturan', [
            'profile' => [
                'username' => $user->username,
                'email' => $user->email ?: '-',
                'nama_lengkap' => $user->nama_lengkap,
                'nip_nis' => $user->nip_nis ?: '-',
                'jenis_kelamin' => $this->genderLabel($user->jenis_kelamin),
                'role' => $role,
                'role_label' => $this->roleLabel($role),
                'is_active' => (bool) $user->is_active,
                'is_password_default' => (bool) $user->is_password_default,
                'created_at' => $user->created_at ? (string) $user->created_at : '-',
                'siswa' => $user->siswa ? [
                    'nis' => $user->siswa->nis ?: '-',
                    'kelas' => trim(($user->siswa->kelas?->tingkat ?? '') . ' ' . ($user->siswa->kelas?->nama_kelas ?? '')) ?: '-',
                    'angkatan' => $user->siswa->angkatan ?: '-',
                    'status' => $user->siswa->status ?: '-',
                    'tinggal_kelas' => (bool) $user->siswa->tinggal_kelas,
                ] : null,
            ],
            'updateUrl' => $this->updateRoute($role),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'is_password_default' => false,
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    private function updateRoute(?string $role): string
    {
        return match ($role) {
            'admin' => route('admin.pengaturan-akun.update'),
            'guru' => route('guru.pengaturan.update'),
            'siswa' => route('siswa.pengaturan.update'),
            default => url()->current(),
        };
    }

    private function roleLabel(?string $role): string
    {
        return match ($role) {
            'admin' => 'Admin',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
            'kepala_sekolah' => 'Kepala Sekolah',
            default => $role ?: '-',
        };
    }

    private function genderLabel(?string $gender): string
    {
        return match ($gender) {
            'L', 'l', 'laki-laki', 'Laki-laki', 'Laki-Laki' => 'Laki-laki',
            'P', 'p', 'perempuan', 'Perempuan' => 'Perempuan',
            default => $gender ?: '-',
        };
    }
}
