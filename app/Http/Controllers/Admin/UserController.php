<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     */
    public function index(Request $request)
    {
        $query = User::with('role');

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

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Form tambah user.
     */
    public function create()
    {
        $roles = Role::all();
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
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'nip_nis' => 'nullable|string|max:20|unique:users,nip_nis',
            'jenis_kelamin' => 'nullable|in:L,P',
            'is_active' => 'boolean',
            'nis' => 'nullable|string|max:20|unique:siswa,nis',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $isSiswa = $role->nama_role === 'siswa';

        if ($isSiswa) {
            $request->validate([
                'nis' => 'nullable|string|max:20|unique:siswa,nis',
                'kelas_id' => 'required|exists:kelas,id',
            ]);
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active');
        $nis = $request->filled('nis') ? $request->nis : $validated['username'];

        if ($isSiswa && Siswa::where('nis', $nis)->exists()) {
            throw ValidationException::withMessages([
                'nis' => 'NIS sudah digunakan oleh siswa lain.',
            ]);
        }

        unset($validated['nis'], $validated['kelas_id']);

        $user = User::create($validated);

        // Jika role siswa, buat juga data di tabel siswa
        if ($isSiswa) {
            Siswa::create([
                'user_id' => $user->id,
                'nis' => $nis,
                'kelas_id' => $request->kelas_id,
                'status' => 'aktif',
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('siswa');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'nama_lengkap' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'nip_nis' => 'nullable|string|max:20|unique:users,nip_nis,' . $user->id,
            'jenis_kelamin' => 'nullable|in:L,P',
            'is_active' => 'boolean',
            'nis' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('siswa', 'nis')->ignore($user->siswa?->id),
            ],
            'kelas_id' => 'nullable|exists:kelas,id',
            'status_siswa' => 'nullable|in:aktif,lulus,keluar',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $isSiswa = $role->nama_role === 'siswa';

        if ($isSiswa) {
            $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'status_siswa' => 'nullable|in:aktif,lulus,keluar',
            ]);
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $nis = $request->filled('nis') ? $request->nis : $validated['username'];

        if ($isSiswa && Siswa::where('nis', $nis)->where('user_id', '!=', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'nis' => 'NIS sudah digunakan oleh siswa lain.',
            ]);
        }

        unset($validated['nis'], $validated['kelas_id'], $validated['status_siswa']);

        $user->update($validated);

        // Update data siswa jika role siswa
        if ($isSiswa) {
            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis' => $nis,
                    'kelas_id' => $request->kelas_id,
                    'status' => $request->status_siswa ?? 'aktif',
                ]
            );
        } elseif ($user->siswa) {
            $user->siswa()->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
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
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status user berhasil diubah.');
    }
}
