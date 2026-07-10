<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\MataPelajaran;
use App\Models\NilaiAkhir;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $taAktif = TahunAjaran::getAktif();
        $semester = \App\Models\Pengaturan::getValue('semester_aktif', '1');

        // GPA (rata-rata nilai akhir)
        $gpa = NilaiAkhir::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $taAktif?->id)
            ->where('semester', $semester)
            ->avg('rata_akhir');

        // Kehadiran bulan ini
        $bulanIni = date('Y-m');
        $absenData = Absensi::where('siswa_id', $siswa->id)
            ->whereHas('kelasMapel', fn($q) => $q->aktif($semester))
            ->whereBetween('tanggal', [date('Y-m-01'), date('Y-m-t')])
            ->get();
        $hadir = $absenData->where('status', 'hadir')->count();
        $sakit = $absenData->where('status', 'sakit')->count();
        $izin = $absenData->where('status', 'izin')->count();
        $alpha = $absenData->where('status', 'alpha')->count();
        $totalAbsen = $hadir + $sakit + $izin + $alpha;
        $persenHadir = $totalAbsen > 0 ? round(($hadir / $totalAbsen) * 100) : 0;

        // Penyelesaian tugas
        $totalTugas = \App\Models\Tugas::whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester))->count();
        $selesai = PengumpulanTugas::where('siswa_id', $siswa->id)
            ->whereIn('status', ['sudah', 'terlambat', 'dinilai'])
            ->whereHas('tugas.kelasMapel', fn($q) => $q->where('kelas_id', $siswa->kelas_id)
                ->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester))
            ->count();
        $persenTugas = $totalTugas > 0 ? round(($selesai / $totalTugas) * 100) : 0;

        // Nilai per mapel
        $subjectScores = MataPelajaran::whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester))
            ->orderBy('urutan')
            ->get()
            ->map(function ($mp) use ($siswa, $taAktif, $semester) {
                $nilai = NilaiAkhir::where('siswa_id', $siswa->id)
                    ->whereHas('kelasMapel', fn($q) => $q->where('mapel_id', $mp->id)
                        ->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester))
                    ->first();
                return [
                    'nama_mapel' => $mp->nama_mapel,
                    'rata' => $nilai?->rata_akhir,
                ];
            });

        return view('siswa.progress', compact(
            'siswa', 'gpa', 'persenHadir', 'persenTugas',
            'hadir', 'sakit', 'izin', 'alpha', 'totalAbsen', 'totalTugas', 'selesai',
            'subjectScores', 'taAktif', 'semester'
        ));
    }
}
