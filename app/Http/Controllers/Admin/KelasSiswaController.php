<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use App\Services\SiswaImportService;
use App\Services\SiswaTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\UniqueConstraintViolationException;

class KelasSiswaController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        //Urutan User berdasarkan NIS/Kode Guru
        $query = Siswa::with(['user', 'kelas'])
            ->whereHas('user')
            ->orderBy('nis');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        $siswa = $query->paginate(25)->appends($request->query());

        return view('admin.kelas-siswa.index', compact('kelasList', 'siswa'));
    }

    public function downloadTemplate(SiswaTemplateService $templateService)
    {
        return response()
            ->download($templateService->createTemplateFile(), SiswaTemplateService::FILENAME)
            ->deleteFileAfterSend(true);
    }

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

    //Save new Siswa
    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis|unique:users,username',
            'nama_lengkap' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
        ], [
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
        ]);

        try {
            $created = DB::transaction(function () use ($validated) {
                $siswaRoleId = Role::where('nama_role', 'siswa')->value('id');
                $password = $this->generateInitialPassword();

                if (!$siswaRoleId) {
                    throw new \RuntimeException('Role siswa belum tersedia.');
                }

                // Buat user dulu
                $user = User::create([
                    'username' => $validated['nis'],
                    'password' => Hash::make($password),
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'role_id' => $siswaRoleId,
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'is_active' => true,
                ]);

                // Buat siswa
                Siswa::create([
                    'user_id' => $user->id,
                    'nis' => $validated['nis'],
                    'kelas_id' => $validated['kelas_id'],
                    'status' => 'aktif',
                ]);

                return compact('user', 'password');
            });

            return back()->with(
                'success',
                "Siswa {$validated['nama_lengkap']} berhasil ditambahkan."
            )->with('student_password', [
                'title' => 'Password awal siswa',
                'name' => $validated['nama_lengkap'],
                'username' => $created['user']->username,
                'password' => $created['password'],
            ]);
        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->with('error', 'Data sudah ada di database. Silakan periksa kembali NIS atau username yang dimasukkan.');
        }
    }
    //Edit Siswa
    public function updateSiswa(Request $request, Siswa $siswa)
    {
        $userId = $siswa->user_id;

        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $siswa->id . '|unique:users,username,' . $userId,
            'nama_lengkap' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tinggal_kelas' => 'boolean',
        ]);

        DB::transaction(function () use ($siswa, $validated) {
            $siswa->update([
                'nis' => $validated['nis'],
                'kelas_id' => $validated['kelas_id'],
                'tinggal_kelas' => $validated['tinggal_kelas'] ?? false,
            ]);

            $siswa->user->update([
                'username' => $validated['nis'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ]);
        });

        return back()->with('success', 'Data siswa berhasil diperbarui.');
    }
    //Reset password ke password acak baru.
    public function resetPassword(Siswa $siswa)
    {
        $password = $this->generateInitialPassword();

        $siswa->user->update(['password' => Hash::make($password)]);

        return back()
            ->with('success', 'Password siswa berhasil direset.')
            ->with('student_password', [
                'title' => 'Password baru siswa',
                'name' => $siswa->user->nama_lengkap,
                'username' => $siswa->user->username,
                'password' => $password,
            ]);
    }
    //Delete Siswa beserta Usernya
    public function destroySiswa(Siswa $siswa)
    {
        $nama = $siswa->user->nama_lengkap;
        $userId = $siswa->user_id;
        $siswa->delete();
        User::destroy($userId);
        return back()->with('success', "Siswa {$nama} berhasil dihapus.");
    }
    //Tampilkan daftar siswa yang sudah lulus
    public function luluskanKelas(Kelas $kelas)
    {
        if ($kelas->tingkat !== 'IX') {
            return back()->with('error', 'Hanya kelas IX yang bisa diluluskan.');
        }

        $count = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->update(['status' => 'lulus']);

        return back()->with('success', "{$count} siswa kelas {$kelas->nama_kelas} berhasil diluluskan.");
    }

    private function generateInitialPassword(): string
    {
        return Str::upper(Str::random(4)) . random_int(1000, 9999);
    }
}
