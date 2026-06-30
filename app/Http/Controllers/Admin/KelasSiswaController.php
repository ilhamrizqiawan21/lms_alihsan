<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            $user = DB::transaction(function () use ($validated) {
                // Buat user dulu
                $user = User::create([
                    'username' => $validated['nis'],
                    'password' => Hash::make('123456'),
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'role_id' => 3,
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

                return $user;
            });

            return back()->with('success', "Siswa {$validated['nama_lengkap']} berhasil ditambahkan.");
        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->with('error', 'Data sudah ada di database. Silakan periksa kembali NIS atau username yang dimasukkan.');
        }
    }
    //Edit Siswa
    public function updateSiswa(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tinggal_kelas' => 'boolean',
        ]);

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

        return back()->with('success', 'Data siswa berhasil diperbarui.');
    }
    //Reset Password balik lagi ke default yaitu  123456
    public function resetPassword(Siswa $siswa)
    {
        $siswa->user->update(['password' => Hash::make('123456')]);
        return back()->with('success', 'Password siswa direset menjadi 123456.');
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
}
