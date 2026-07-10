<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\NilaiAkhir;
use App\Models\Pengumuman;
use App\Models\SikapSosial;
use App\Models\SikapSpiritual;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function absensi(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'bulan' => 'nullable|date_format:Y-m',
            'semester' => 'nullable|in:1,2',
        ]);

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasId = $request->input('kelas_id');
        $bulan = $request->input('bulan', date('Y-m'));
        $taAktif = TahunAjaran::getAktif();
        $semester = $request->input('semester', \App\Models\Pengaturan::getValue('semester_aktif', '1'));

        $rekap = [];
        $tanggalList = [];
        $kelasNama = '';

        if ($kelasId && $taAktif) {
            $kelas = Kelas::find($kelasId);
            $kelasNama = $kelas ? "{$kelas->tingkat} {$kelas->nama_kelas}" : '';

            $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();

            $tanggalList = Absensi::whereHas('kelasMapel', fn($q) => $q
                    ->where('kelas_id', $kelasId)
                    ->where('tahun_ajaran_id', $taAktif->id)
                    ->where('semester', $semester))
                ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
                ->orderBy('tanggal')->pluck('tanggal')->unique()->map(fn($d) => $d->format('Y-m-d'))->values();

            $absensiData = Absensi::whereIn('siswa_id', $siswaList->pluck('id'))
                ->whereHas('kelasMapel', fn($q) => $q
                    ->where('kelas_id', $kelasId)
                    ->where('tahun_ajaran_id', $taAktif->id)
                    ->where('semester', $semester))
                ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
                ->get()->groupBy('siswa_id');

            foreach ($siswaList as $s) {
                $row = ['nis' => $s->nis, 'nama' => $s->user->nama_lengkap ?? '-', 'absensi' => [], 'hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0];
                $sa = $absensiData->get($s->id, collect());
                foreach ($tanggalList as $tgl) {
                    $ab = $sa->firstWhere('tanggal', $tgl);
                    $st = $ab ? $ab->status : null;
                    $row['absensi'][$tgl] = $st;
                    if ($st) $row[$st]++;
                }
                $rekap[] = $row;
            }
        }

        return view('admin.rekap.absensi', compact('kelasList', 'rekap', 'tanggalList', 'kelasNama', 'bulan', 'kelasId', 'taAktif', 'semester'));
    }
    //Mengambil nilai berdasarkan kelas
    public function nilai(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasId = $request->input('kelas_id');
        $taAktif = TahunAjaran::getAktif();
        $semester = $request->input('semester', \App\Models\Pengaturan::getValue('semester_aktif', '1'));

        $rekap = [];
        $mapelList = [];
        $kelasNama = '';

        if ($kelasId && $taAktif) {
            $kelas = Kelas::find($kelasId);
            $kelasNama = $kelas ? "{$kelas->tingkat} {$kelas->nama_kelas}" : '';

            $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();

            $mapelList = KelasMapel::with('mataPelajaran')
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $taAktif->id)
                ->where('semester', $semester)
                ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'kelas_mapel.mapel_id')
                ->orderBy('mata_pelajaran.urutan')
                ->orderBy('mata_pelajaran.nama_mapel')
                ->select('kelas_mapel.*')
                ->get()
                ->map(function ($kelasMapel) {
                    return (object) [
                        'id' => $kelasMapel->mapel_id,
                        'kelas_mapel_id' => $kelasMapel->id,
                        'nama_mapel' => $kelasMapel->mataPelajaran?->nama_mapel ?? '-',
                    ];
                });

            $nilaiData = NilaiAkhir::whereIn('siswa_id', $siswaList->pluck('id'))
                ->where('tahun_ajaran_id', $taAktif->id)->where('semester', $semester)
                ->get()->groupBy('siswa_id');

            foreach ($siswaList as $s) {
                $row = ['nis' => $s->nis, 'nama' => $s->user->nama_lengkap ?? '-', 'nilai' => []];
                $sn = $nilaiData->get($s->id, collect());
                foreach ($mapelList as $mp) {
                    $n = $sn->firstWhere('kelas_mapel_id', $mp->kelas_mapel_id);
                    $row['nilai'][$mp->id] = $n ? $n->rata_akhir : null;
                }
                $validNilai = array_filter($row['nilai'], fn($v) => !is_null($v));
                $row['rata'] = count($validNilai) > 0 ? round(array_sum($validNilai) / count($validNilai), 2) : null;
                $rekap[] = $row;
            }
        }

        return view('admin.rekap.nilai', compact('kelasList', 'rekap', 'mapelList', 'kelasNama', 'kelasId', 'taAktif', 'semester'));
    }
    //Nilai Sikap
    public function sikap(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasId = $request->input('kelas_id');
        $taAktif = TahunAjaran::getAktif();
        $semester = $request->input('semester', \App\Models\Pengaturan::getValue('semester_aktif', '1'));

        $rekap = [];
        $kelasNama = '';
        $labelNilai = [1=>'TB', 2=>'KB', 3=>'C', 4=>'B', 5=>'SB'];

        if ($kelasId && $taAktif) {
            $kelas = Kelas::find($kelasId);
            $kelasNama = $kelas ? "{$kelas->tingkat} {$kelas->nama_kelas}" : '';

            $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();
            $kelasMapelIds = KelasMapel::where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $taAktif->id)
                ->where('semester', $semester)
                ->pluck('id');

            $spData = SikapSpiritual::whereIn('siswa_id', $siswaList->pluck('id'))
                ->where('tahun_ajaran_id', $taAktif->id)->where('semester', $semester)
                ->whereIn('kelas_mapel_id', $kelasMapelIds)
                ->get()
                ->groupBy('siswa_id')
                ->map(function ($records) use ($labelNilai) {
                    return collect(['taqwa', 'kejujuran', 'disiplin', 'sabar', 'syukur', 'tawadhu'])
                        ->mapWithKeys(fn ($field) => [$field => $labelNilai[(int) round($records->avg($field))] ?? '-'])
                        ->all();
                });

            $soData = SikapSosial::whereIn('siswa_id', $siswaList->pluck('id'))
                ->where('tahun_ajaran_id', $taAktif->id)->where('semester', $semester)
                ->whereIn('kelas_mapel_id', $kelasMapelIds)
                ->get()
                ->groupBy('siswa_id')
                ->map(function ($records) use ($labelNilai) {
                    return collect(['empati', 'kerjasama', 'toleransi', 'percaya_diri', 'komunikasi'])
                        ->mapWithKeys(fn ($field) => [$field => $labelNilai[(int) round($records->avg($field))] ?? '-'])
                        ->all();
                });

            foreach ($siswaList as $s) {
                $sp = $spData->get($s->id);
                $so = $soData->get($s->id);
                $rekap[] = [
                    'nis' => $s->nis,
                    'nama' => $s->user->nama_lengkap ?? '-',
                    'spiritual' => $sp,
                    'sosial' => $so,
                ];
            }
        }

        return view('admin.rekap.sikap', compact('kelasList', 'rekap', 'kelasNama', 'kelasId', 'taAktif', 'semester'));
    }
    //Rekap Tugas Siswa
    public function tugas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasId = $request->input('kelas_id');
        $taAktif = TahunAjaran::getAktif();
        $semester = $request->input('semester', \App\Models\Pengaturan::getValue('semester_aktif', '1'));

        $tugasList = [];
        $kelasNama = '';

        if ($kelasId && $taAktif) {
            $kelas = Kelas::find($kelasId);
            $kelasNama = $kelas ? "{$kelas->tingkat} {$kelas->nama_kelas}" : '';

            $totalSiswa = Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->count();

            $tugasList = Tugas::with(['kelasMapel.mataPelajaran', 'kelasMapel.guru'])
                ->whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $taAktif->id)->where('semester', $semester))
                ->withCount(['pengumpulan as sudah_kumpul' => fn($q) => $q
                    ->whereIn('status', ['sudah', 'terlambat', 'dinilai'])
                    ->whereHas('siswa', fn($siswa) => $siswa->where('kelas_id', $kelasId)->where('status', 'aktif'))])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($t) use ($totalSiswa) {
                    $t->total_siswa = $totalSiswa;
                    return $t;
                });
        }

        return view('admin.rekap.tugas', compact('kelasList', 'tugasList', 'kelasNama', 'kelasId', 'taAktif', 'semester'));
    }
}
