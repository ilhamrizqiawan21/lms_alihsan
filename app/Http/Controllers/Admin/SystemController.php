<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use App\Models\LogLogin;
use App\Models\Pengaturan;
use App\Models\SchoolSetting;
use App\Models\SystemError;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        $logs = $query->paginate(25)
            ->withQueryString()
            ->through(fn (LogLogin $log) => [
                'id' => $log->id,
                'login_time' => optional($log->login_time)->format('d M Y H:i:s'),
                'username' => $log->username,
                'nama_lengkap' => $log->nama_lengkap,
                'role' => $log->role,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
            ]);

        return Inertia::render('Admin/LogLogin/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search']),
        ]);
    }
    //Menampilkan riwayat login sistem
    public function logError(Request $request)
    {
        $query = SystemError::orderBy('created_at', 'desc');

        if ($request->filled('level')) {
            $query->where('error_level', $request->level);
        }

        $errors = $query->paginate(25)
            ->withQueryString()
            ->through(fn (SystemError $error) => [
                'id' => $error->id,
                'error_level' => $error->error_level,
                'created_at' => optional($error->created_at)->format('d/m H:i'),
                'message' => $error->message,
                'file' => $error->file,
                'line' => $error->line,
                'url' => $error->url,
            ]);
        $levels = SystemError::select('error_level')->distinct()->pluck('error_level');

        return Inertia::render('Admin/LogError/Index', [
            'errors' => $errors,
            'levels' => $levels,
            'filters' => $request->only(['level']),
        ]);
    }
    //Pengaturan sistem seperti warna tema, nama sekolah, semester aktif, tahun ajaran aktif, dan mode kenaikan kelas
    public function pengaturan()
    {
        $settings = Pengaturan::pluck('value', 'key')->toArray();
        $tahunAjaranAktif = TahunAjaran::getAktif();
        $schoolSetting = SchoolSetting::query()->first() ?: new SchoolSetting(SchoolSetting::fallback());

        return view('admin.pengaturan', compact('settings', 'tahunAjaranAktif', 'schoolSetting'));
    }
    //Simpan pengaturan sistem
    public function savePengaturan(Request $request)
    {
        $data = $request->validate([
            'warna_tema' => 'nullable|in:hijau,biru-azure,biru-aqua',
            'semester_aktif' => 'nullable|in:1,2',
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
