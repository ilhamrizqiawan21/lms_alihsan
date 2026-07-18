<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\SiswaImportService;
use App\Services\SiswaTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderName;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\BorderWidth;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     */
    public function index(Request $request)
    {
        $query = User::with('role')
            ->whereHas('role', fn ($query) => $query->where('nama_role', '!=', 'siswa'));

        // Filter role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $roles = $this->staffRoles();

        return Inertia::render('Admin/Users/Index', [
            'users' => [
                'data' => $users->getCollection()->map(fn (User $user) => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email,
                    'role' => [
                        'id' => $user->role?->id,
                        'nama_role' => $user->role?->nama_role,
                    ],
                    'is_active' => (bool) $user->is_active,
                    'password_is_default' => (bool) $user->is_password_default,
                    'password_status' => $user->is_password_default
                        ? 'Masih default'
                        : 'Sudah diubah',
                ])->values(),
                'links' => $users->linkCollection(),
                'meta' => [
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                    'total' => $users->total(),
                ],
            ],
            'roles' => $roles->map(fn (Role $role) => [
                'id' => $role->id,
                'nama_role' => $role->nama_role,
            ])->values(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'role_id' => $request->string('role_id')->toString(),
            ],
            'exportUrl' => route('admin.users.export.excel'),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'role_id' => 'nullable|exists:roles,id',
            'search' => 'nullable|string|max:100',
        ]);

        $query = User::with('role')
            ->whereHas('role', fn ($query) => $query->where('nama_role', '!=', 'siswa'));

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $writer = new Writer();
        $filePath = tempnam(sys_get_temp_dir(), 'guru_staf_');
        $filename = 'status_password_guru_staf_' . date('Ymd_His') . '.xlsx';

        $writer->openToFile($filePath);
        $writer->getCurrentSheet()->setColumnWidth(22, 1);
        $writer->getCurrentSheet()->setColumnWidth(32, 2);
        $writer->getCurrentSheet()->setColumnWidth(18, 3);
        $writer->getCurrentSheet()->setColumnWidth(18, 4);
        $writer->getCurrentSheet()->setColumnWidth(24, 5);

        $styles = $this->excelStyles();
        $writer->addRow(Row::fromValuesWithStyle([school_setting('school_name', 'Nama Sekolah')], $styles['school'], 24));
        $writer->addRow(Row::fromValuesWithStyle(['STATUS PASSWORD GURU & STAF'], $styles['title'], 24));
        $writer->addRow(Row::fromValuesWithStyle(['Tanggal Export', now()->format('d/m/Y H:i')], $styles['meta'], 18));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValuesWithStyle([
            'Username',
            'Nama',
            'Role',
            'Password Default',
            'Status Password',
        ], $styles['tableHeader'], 24));

        $query->orderBy('nama_lengkap')->get()->values()->each(function (User $user, int $index) use ($writer, $styles) {
            $isDefaultPassword = (bool) $user->is_password_default;

            $writer->addRow(Row::fromValuesWithStyle([
                $user->username,
                $user->nama_lengkap,
                $user->role?->nama_role ? str_replace('_', ' ', ucwords($user->role->nama_role, '_')) : '-',
                User::DEFAULT_PASSWORD,
                $isDefaultPassword ? 'Masih default' : 'Sudah diubah',
            ], $index % 2 === 0 ? $styles['row'] : $styles['alternateRow'], 20));
        });

        $writer->close();

        return response()
            ->download($filePath, $filename)
            ->deleteFileAfterSend(true);
    }

    /**
     * Form tambah user.
     */
    public function create()
    {
        $roles = $this->staffRoles();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'nama_lengkap' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'nip_nis' => 'nullable|string|max:20|unique:users,nip_nis',
            'jenis_kelamin' => 'nullable|in:L,P',
            'is_active' => 'boolean',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $this->ensureStaffRole($role);

        $plainPassword = $request->filled('password') ? $validated['password'] : User::DEFAULT_PASSWORD;
        $validated['password'] = Hash::make($plainPassword);
        $validated['is_password_default'] = $plainPassword === User::DEFAULT_PASSWORD;
        $validated['is_active'] = $request->boolean('is_active');

        DB::transaction(fn () => User::create($validated));

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun guru/staf berhasil ditambahkan.');
    }

    /**
     * Unduh template import siswa.
     */
    public function downloadSiswaTemplate(SiswaTemplateService $templateService)
    {
        return response()
            ->download($templateService->createTemplateFile(), SiswaTemplateService::FILENAME)
            ->deleteFileAfterSend(true);
    }

    /**
     * Import banyak siswa dari file Excel.
     */
    public function importSiswa(Request $request, SiswaImportService $importService)
    {
        $request->validate([
            'file_siswa' => 'required|file|mimes:xlsx|max:5120',
        ]);

        $result = $importService->import($request->file('file_siswa')->getRealPath());

        if ($result['errors'] !== []) {
            return back()->with('import_errors', $result['errors']);
        }

        return back()->with('success', $result['imported'] . ' siswa berhasil diimport.');
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        $this->ensureNotSiswaUser($user);
        $roles = $this->staffRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $this->ensureNotSiswaUser($user);

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'nama_lengkap' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'nip_nis' => 'nullable|string|max:20|unique:users,nip_nis,' . $user->id,
            'jenis_kelamin' => 'nullable|in:L,P',
            'password' => 'nullable|string|min:6',
            'is_active' => 'boolean',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $this->ensureStaffRole($role);

        if ((int) $user->id === (int) Auth::id()
            && (!$request->boolean('is_active') || (int) $validated['role_id'] !== (int) $user->role_id)) {
            throw ValidationException::withMessages([
                'role_id' => 'Anda tidak dapat menonaktifkan atau mengubah role akun sendiri.',
            ]);
        }

        if ((int) $validated['role_id'] !== (int) $user->role_id
            && $user->kelasMapel()->exists()) {
            throw ValidationException::withMessages([
                'role_id' => 'Role tidak dapat diubah karena user ini sudah memiliki data siswa atau penugasan mengajar. Buat akun baru agar riwayat data tetap aman.',
            ]);
        }

        if ($this->isLastActiveAdmin($user)
            && (!$request->boolean('is_active') || $role->nama_role !== 'admin')) {
            throw ValidationException::withMessages([
                'role_id' => 'Sistem harus memiliki setidaknya satu admin aktif.',
            ]);
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
            $validated['is_password_default'] = $request->password === User::DEFAULT_PASSWORD;
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        DB::transaction(fn () => $user->update($validated));

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        $this->ensureNotSiswaUser($user);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($this->isLastActiveAdmin($user)) {
            return back()->with('error', 'User tidak dapat dihapus karena merupakan admin aktif terakhir.');
        }

        if ($user->kelasMapel()->exists()) {
            return back()->with('error', 'User tidak dapat dihapus karena masih memiliki penugasan mengajar.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleActive(User $user)
    {
        $this->ensureNotSiswaUser($user);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->is_active && $this->isLastActiveAdmin($user)) {
            return back()->with('error', 'Sistem harus memiliki setidaknya satu admin aktif.');
        }

        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status user berhasil diubah.');
    }

    /**
     * Reset password guru/staf ke password default.
     */
    public function resetPassword(User $user)
    {
        $this->ensureNotSiswaUser($user);

        $user->update([
            'password' => Hash::make(User::DEFAULT_PASSWORD),
            'is_password_default' => true,
        ]);

        return back()->with('success', "Password {$user->nama_lengkap} berhasil direset ke " . User::DEFAULT_PASSWORD . '.');
    }

    private function isLastActiveAdmin(User $user): bool
    {
        return $user->is_active
            && $user->hasRole('admin')
            && User::where('is_active', true)
                ->whereHas('role', fn ($query) => $query->where('nama_role', 'admin'))
                ->count() <= 1;
    }

    private function staffRoles()
    {
        return Role::where('nama_role', '!=', 'siswa')->orderBy('nama_role')->get();
    }

    private function ensureStaffRole(Role $role): void
    {
        if ($role->nama_role === 'siswa') {
            throw ValidationException::withMessages([
                'role_id' => 'Akun siswa hanya dapat dibuat melalui menu Kelas & Siswa.',
            ]);
        }
    }

    private function ensureNotSiswaUser(User $user): void
    {
        if ($user->isSiswa()) {
            abort(404);
        }
    }

    private function excelStyles(): array
    {
        $border = new Border(
            new BorderPart(BorderName::TOP, 'CBD5E1', BorderWidth::THIN),
            new BorderPart(BorderName::RIGHT, 'CBD5E1', BorderWidth::THIN),
            new BorderPart(BorderName::BOTTOM, 'CBD5E1', BorderWidth::THIN),
            new BorderPart(BorderName::LEFT, 'CBD5E1', BorderWidth::THIN),
        );

        $base = (new Style())
            ->withFontName('Arial')
            ->withFontSize(10)
            ->withShouldWrapText(true)
            ->withCellVerticalAlignment(CellVerticalAlignment::CENTER);

        return [
            'school' => $base
                ->withFontBold(true)
                ->withFontSize(14)
                ->withFontColor('0F172A')
                ->withCellAlignment(CellAlignment::CENTER),
            'title' => $base
                ->withFontBold(true)
                ->withFontSize(13)
                ->withFontColor(Color::WHITE)
                ->withBackgroundColor('1D4ED8')
                ->withCellAlignment(CellAlignment::CENTER),
            'meta' => $base
                ->withFontColor('475569')
                ->withBackgroundColor('F8FAFC'),
            'tableHeader' => $base
                ->withFontBold(true)
                ->withFontColor(Color::WHITE)
                ->withBackgroundColor('334155')
                ->withCellAlignment(CellAlignment::CENTER)
                ->withBorder($border),
            'row' => $base
                ->withBackgroundColor(Color::WHITE)
                ->withBorder($border),
            'alternateRow' => $base
                ->withBackgroundColor('F8FAFC')
                ->withBorder($border),
        ];
    }
}
