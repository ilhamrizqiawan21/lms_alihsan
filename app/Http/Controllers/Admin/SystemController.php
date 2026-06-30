<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use App\Models\LogLogin;
use App\Models\Pengaturan;
use App\Models\SystemError;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function logLogin(Request $request)
    {
        //Mengecek aktivitas login user
        $query = LogLogin::orderBy('login_time', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('username', 'like', "%{$s}%")->orWhere('nama_lengkap', 'like', "%{$s}%"));
        }

        $logs = $query->paginate(25);

        return view('admin.log-login', compact('logs'));
    }
    //Menampilkan riwayat login sistem
    public function logError(Request $request)
    {
        $query = SystemError::orderBy('created_at', 'desc');

        if ($request->filled('level')) {
            $query->where('error_level', $request->level);
        }

        $errors = $query->paginate(25);
        $levels = SystemError::select('error_level')->distinct()->pluck('error_level');

        return view('admin.log-error', compact('errors', 'levels'));
    }
    //Pengaturan sistem seperti warna tema, nama sekolah, semester aktif, tahun ajaran aktif, dan mode kenaikan kelas
    public function pengaturan()
    {
        $settings = Pengaturan::pluck('value', 'key')->toArray();
        return view('admin.pengaturan', compact('settings'));
    }
    //Simpan pengaturan sistem
    public function savePengaturan(Request $request)
    {
        $data = $request->validate([
            'nama_sekolah' => 'nullable|string|max:100',
            'warna_tema' => 'nullable|in:hijau,biru-azure,biru-aqua',
            'semester_aktif' => 'nullable|in:1,2',
            'tahun_ajaran_aktif' => 'nullable|string|max:9',
            'mode_kenaikan' => 'nullable|in:manual,auto',
        ]);

        foreach ($data as $key => $value) {
            if ($value !== null) {
                Pengaturan::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
    //Memblokir IP tertentu agar tidak bisa mengakses sistem
    public function blockedIps()
    {
        $ips = BlockedIp::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.blocked-ips', compact('ips'));
    }
    //Membuka blokir IP tertentu agar bisa mengakses sistem kembali
    public function unblockIp(BlockedIp $blockedIp)
    {
        $blockedIp->delete();
        return back()->with('success', 'IP berhasil di-unblock.');
    }
}
