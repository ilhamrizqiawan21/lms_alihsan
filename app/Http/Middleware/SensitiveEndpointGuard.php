<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevents legacy endpoints from exposing shared/default passwords.
 *
 * This middleware intentionally sits before the controller action so the
 * legacy implementation cannot execute while the route remains available
 * for backwards compatibility.
 */
class SensitiveEndpointGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('admin.users.export.excel')) {
            return $this->safeUserPasswordStatusExport($request);
        }

        if ($request->routeIs('admin.users.reset-password')) {
            return $this->secureResetPassword($request);
        }

        return $next($request);
    }

    private function secureResetPassword(Request $request): Response
    {
        /** @var User|null $user */
        $user = $request->route('user');

        abort_unless($user instanceof User && ! $user->isSiswa(), 404);

        $temporaryPassword = Str::password(20);

        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'is_password_default' => true,
        ])->save();

        return back()
            ->with('success', "Password {$user->nama_lengkap} berhasil direset. Password sementara hanya ditampilkan sekali.")
            ->with('temporary_password', $temporaryPassword);
    }

    private function safeUserPasswordStatusExport(Request $request): Response
    {
        $validated = $request->validate([
            'role_id' => 'nullable|exists:roles,id',
            'search' => 'nullable|string|max:100',
        ]);

        $query = User::with('role')
            ->whereHas('role', fn ($q) => $q->where('nama_role', '!=', 'siswa'));

        if (! empty($validated['role_id'])) {
            $query->where('role_id', $validated['role_id']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $filename = 'status_password_guru_staf_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['Username', 'Nama', 'Role', 'Status Password']);

            $query->orderBy('nama_lengkap')->chunk(500, function ($users) use ($output): void {
                foreach ($users as $user) {
                    fputcsv($output, [
                        $user->username,
                        $user->nama_lengkap,
                        $user->role?->nama_role ?? '-',
                        $user->is_password_default ? 'Masih default/sementara' : 'Sudah diubah',
                    ]);
                }
            });

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
