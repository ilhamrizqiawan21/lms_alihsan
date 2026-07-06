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
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasId = $request->input('kelas_id');
        $bulan = $request->input('bulan', date('Y-m'));

        $rekap = [];
        $tanggalList = [];
        $kelasNama = '';

        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
            $kelasNama = $kelas ? "{$kelas->tingkat} {$kelas->nama_kelas}" : '';

            $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();

            $tanggalList = Absensi::whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasId))
                ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
                ->orderBy('tanggal')->pluck('tanggal')->unique()->map(fn($d) => $d->format('Y-m-d'))->values();

            $absensiData = Absensi::whereIn('siswa_id', $siswaList->pluck('id'))
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

        return view('admin.rekap.absensi', compact('kelasList', 'rekap', 'tanggalList', 'kelasNama', 'bulan', 'kelasId'));
    }
    //Mengambil nilai berdasarkan kelas
    public function nilai(Request $request)
    {
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

            $spData = SikapSpiritual::whereIn('siswa_id', $siswaList->pluck('id'))
                ->where('tahun_ajaran_id', $taAktif->id)->where('semester', $semester)
                ->get()->keyBy('siswa_id');

            $soData = SikapSosial::whereIn('siswa_id', $siswaList->pluck('id'))
                ->where('tahun_ajaran_id', $taAktif->id)->where('semester', $semester)
                ->get()->keyBy('siswa_id');

            foreach ($siswaList as $s) {
                $sp = $spData->get($s->id);
                $so = $soData->get($s->id);
                $rekap[] = [
                    'nis' => $s->nis,
                    'nama' => $s->user->nama_lengkap ?? '-',
                    'spiritual' => $sp ? [
                        'taqwa' => $labelNilai[$sp->taqwa] ?? '-',
                        'kejujuran' => $labelNilai[$sp->kejujuran] ?? '-',
                        'disiplin' => $labelNilai[$sp->disiplin] ?? '-',
                        'sabar' => $labelNilai[$sp->sabar] ?? '-',
                        'syukur' => $labelNilai[$sp->syukur] ?? '-',
                        'tawadhu' => $labelNilai[$sp->tawadhu] ?? '-',
                    ] : null,
                    'sosial' => $so ? [
                        'empati' => $labelNilai[$so->empati] ?? '-',
                        'kerjasama' => $labelNilai[$so->kerjasama] ?? '-',
                        'toleransi' => $labelNilai[$so->toleransi] ?? '-',
                        'percaya_diri' => $labelNilai[$so->percaya_diri] ?? '-',
                        'komunikasi' => $labelNilai[$so->komunikasi] ?? '-',
                    ] : null,
                ];
            }
        }

        return view('admin.rekap.sikap', compact('kelasList', 'rekap', 'kelasNama', 'kelasId', 'taAktif', 'semester'));
    }
    //Rekap Tugas Siswa
    public function tugas(Request $request)
    {
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
                ->withCount(['pengumpulan as sudah_kumpul' => fn($q) => $q->where('status', 'sudah')])
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
