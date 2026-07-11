<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\NilaiAkhir;
use App\Models\SikapSosial;
use App\Models\SikapSpiritual;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\Pengaturan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class ExportController extends Controller
{
    // ─────────────────────────────────────────────
    // EXPORT EXCEL - REKAP NILAI
    // ─────────────────────────────────────────────
    public function excelNilai(Request $request)
    {
        $filters = $this->validatedExportFilters($request);
        $kelasId = $filters['kelas_id'];
        $semester = $filters['semester'];
        $taAktif = TahunAjaran::getAktif();

        $kelas = Kelas::findOrFail($kelasId);
        $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();
        $mapelList = $this->mapelListUntukKelas($kelasId, $taAktif?->id, $semester);

        $nilaiData = NilaiAkhir::whereIn('siswa_id', $siswaList->pluck('id'))
            ->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester)
            ->get()->groupBy('siswa_id');

        $writer = new Writer();
        $filename = "rekap_nilai_{$kelas->tingkat}_{$kelas->nama_kelas}_semester_{$semester}.xlsx";
        $filePath = $this->temporaryExcelPath('rekap_nilai_');

        $writer->openToFile($filePath);

        $reportSchool = $this->reportSchool($taAktif, $semester);
        foreach ($this->excelReportHeader('REKAP NILAI', $reportSchool, "Kelas {$kelas->tingkat} {$kelas->nama_kelas}") as $header) {
            $writer->addRow(Row::fromValues($header));
        }

        $headerRow = Row::fromValues(array_merge(
            ['No', 'NIS', 'Nama'],
            $mapelList->pluck('nama_mapel')->toArray(),
            ['Rata-rata']
        ));
        $writer->addRow($headerRow);

        // Data
        foreach ($siswaList as $i => $s) {
            $sn = $nilaiData->get($s->id, collect());
            $nilaiRow = [];
            foreach ($mapelList as $mp) {
                $n = $sn->firstWhere('kelas_mapel_id', $mp->kelas_mapel_id);
                $nilaiRow[] = $n ? (float) $n->rata_akhir : '';
            }
            $validNilai = array_filter($nilaiRow, fn($v) => $v !== '');
            $rata = count($validNilai) > 0 ? round(array_sum($validNilai) / count($validNilai), 2) : '';

            $dataRow = Row::fromValues(array_merge(
                [$i + 1, $s->nis, $s->user->nama_lengkap ?? '-'],
                $nilaiRow,
                [$rata]
            ));
            $writer->addRow($dataRow);
        }

        $writer->close();

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    // ─────────────────────────────────────────────
    // EXPORT EXCEL - REKAP ABSENSI
    // ─────────────────────────────────────────────
    public function excelAbsensi(Request $request)
    {
        $filters = $this->validatedExportFilters($request, withMonth: true);
        $kelasId = $filters['kelas_id'];
        $bulan = $filters['bulan'];
        $semester = $filters['semester'];
        $taAktif = TahunAjaran::getAktif();

        $kelas = Kelas::findOrFail($kelasId);
        $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();

        $tanggalList = Absensi::whereHas('kelasMapel', fn($q) => $q
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $taAktif?->id)
                ->where('semester', $semester))
            ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
            ->orderBy('tanggal')->pluck('tanggal')->unique()->map(fn($d) => $d->format('Y-m-d'))->values();

        $absensiData = Absensi::whereIn('siswa_id', $siswaList->pluck('id'))
            ->whereHas('kelasMapel', fn($q) => $q
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $taAktif?->id)
                ->where('semester', $semester))
            ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
            ->get()->groupBy('siswa_id');

        $writer = new Writer();
        $filename = "rekap_absensi_{$kelas->tingkat}_{$kelas->nama_kelas}_{$bulan}.xlsx";
        $filePath = $this->temporaryExcelPath('rekap_absensi_');

        $writer->openToFile($filePath);

        $reportSchool = $this->reportSchool($taAktif, $semester);
        foreach ($this->excelReportHeader('REKAP ABSENSI', $reportSchool, "Kelas {$kelas->tingkat} {$kelas->nama_kelas} - Bulan {$bulan}") as $header) {
            $writer->addRow(Row::fromValues($header));
        }

        $headerRow = Row::fromValues(array_merge(
            ['No', 'NIS', 'Nama'],
            $tanggalList->map(fn($t) => date('d', strtotime($t)))->toArray(),
            ['H', 'S', 'I', 'A']
        ));
        $writer->addRow($headerRow);

        // Data
        foreach ($siswaList as $i => $s) {
            $sa = $absensiData->get($s->id, collect());
            $absenRow = [];
            $hadir = $sakit = $izin = $alpha = 0;
            foreach ($tanggalList as $tgl) {
                $ab = $sa->firstWhere('tanggal', $tgl);
                $st = $ab ? $ab->status : '';
                $absenRow[] = match($st) {
                    'hadir' => 'H',
                    'sakit' => 'S',
                    'izin' => 'I',
                    'alpha' => 'A',
                    default => ''
                };
                if ($st === 'hadir') $hadir++;
                elseif ($st === 'sakit') $sakit++;
                elseif ($st === 'izin') $izin++;
                elseif ($st === 'alpha') $alpha++;
            }

            $dataRow = Row::fromValues(array_merge(
                [$i + 1, $s->nis, $s->user->nama_lengkap ?? '-'],
                $absenRow,
                [$hadir, $sakit, $izin, $alpha]
            ));
            $writer->addRow($dataRow);
        }

        $writer->close();

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    // ─────────────────────────────────────────────
    // EXPORT EXCEL - REKAP TUGAS
    // ─────────────────────────────────────────────
    public function excelTugas(Request $request)
    {
        $filters = $this->validatedExportFilters($request);
        $kelasId = $filters['kelas_id'];
        $semester = $filters['semester'];
        $taAktif = TahunAjaran::getAktif();

        $kelas = Kelas::findOrFail($kelasId);
        $totalSiswa = Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->count();

        $tugasList = Tugas::with(['kelasMapel.mataPelajaran', 'kelasMapel.guru'])
            ->whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester))
            ->withCount(['pengumpulan as sudah_kumpul' => fn($q) => $q
                ->whereIn('status', ['sudah', 'terlambat', 'dinilai'])
                ->whereHas('siswa', fn($siswa) => $siswa->where('kelas_id', $kelasId)->where('status', 'aktif'))])
            ->orderBy('created_at', 'desc')
            ->get();

        $writer = new Writer();
        $filename = "rekap_tugas_{$kelas->tingkat}_{$kelas->nama_kelas}_semester_{$semester}.xlsx";
        $filePath = $this->temporaryExcelPath('rekap_tugas_');

        $writer->openToFile($filePath);

        $reportSchool = $this->reportSchool($taAktif, $semester);
        foreach ($this->excelReportHeader('REKAP TUGAS', $reportSchool, "Kelas {$kelas->tingkat} {$kelas->nama_kelas}") as $header) {
            $writer->addRow(Row::fromValues($header));
        }

        $headerRow = Row::fromValues(['No', 'Judul Tugas', 'Mata Pelajaran', 'Guru', 'Deadline', 'Kategori', 'Sudah Kumpul', 'Total Siswa', 'Persentase']);
        $writer->addRow($headerRow);

        // Data
        foreach ($tugasList as $i => $t) {
            $persen = $totalSiswa > 0 ? round(($t->sudah_kumpul / $totalSiswa) * 100, 2) : 0;
            $dataRow = Row::fromValues([
                $i + 1,
                $t->judul,
                $t->kelasMapel?->mataPelajaran?->nama_mapel ?? '-',
                $t->kelasMapel?->guru?->nama_lengkap ?? '-',
                $t->batas_waktu ? date('d/m/Y H:i', strtotime($t->batas_waktu)) : '-',
                $t->kategori_nilai ?? 'NH',
                $t->sudah_kumpul,
                $totalSiswa,
                "{$persen}%",
            ]);
            $writer->addRow($dataRow);
        }

        $writer->close();

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    // ─────────────────────────────────────────────
    // EXPORT PDF - REKAP NILAI
    // ─────────────────────────────────────────────
    public function pdfNilai(Request $request)
    {
        $filters = $this->validatedExportFilters($request);
        $kelasId = $filters['kelas_id'];
        $semester = $filters['semester'];
        $taAktif = TahunAjaran::getAktif();

        $kelas = Kelas::findOrFail($kelasId);
        $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();
        $mapelList = $this->mapelListUntukKelas($kelasId, $taAktif?->id, $semester);

        $nilaiData = NilaiAkhir::whereIn('siswa_id', $siswaList->pluck('id'))
            ->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester)
            ->get()->groupBy('siswa_id');

        $rekap = [];
        foreach ($siswaList as $s) {
            $sn = $nilaiData->get($s->id, collect());
            $row = ['nis' => $s->nis, 'nama' => $s->user->nama_lengkap ?? '-', 'nilai' => []];
            foreach ($mapelList as $mp) {
                $n = $sn->firstWhere('kelas_mapel_id', $mp->kelas_mapel_id);
                $row['nilai'][$mp->id] = $n ? $n->rata_akhir : null;
            }
            $validNilai = array_filter($row['nilai'], fn($v) => !is_null($v));
            $row['rata'] = count($validNilai) > 0 ? round(array_sum($validNilai) / count($validNilai), 2) : null;
            $rekap[] = $row;
        }

        $labelSemester = $semester == '1' ? 'Ganjil' : 'Genap';
        $reportSchool = $this->reportSchool($taAktif, $semester);
        $pdf = Pdf::loadView('exports.pdf.nilai', compact('rekap', 'mapelList', 'kelas', 'labelSemester', 'taAktif', 'reportSchool'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("rekap_nilai_{$kelas->tingkat}_{$kelas->nama_kelas}_semester_{$semester}.pdf");
    }

    // ─────────────────────────────────────────────
    // EXPORT PDF - REKAP ABSENSI
    // ─────────────────────────────────────────────
    public function pdfAbsensi(Request $request)
    {
        $filters = $this->validatedExportFilters($request, withMonth: true);
        $kelasId = $filters['kelas_id'];
        $bulan = $filters['bulan'];
        $semester = $filters['semester'];
        $taAktif = TahunAjaran::getAktif();

        $kelas = Kelas::findOrFail($kelasId);
        $siswaList = Siswa::with('user')->where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get();

        $tanggalList = Absensi::whereHas('kelasMapel', fn($q) => $q
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $taAktif?->id)
                ->where('semester', $semester))
            ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
            ->orderBy('tanggal')->pluck('tanggal')->unique()->map(fn($d) => $d->format('Y-m-d'))->values();

        $absensiData = Absensi::whereIn('siswa_id', $siswaList->pluck('id'))
            ->whereHas('kelasMapel', fn($q) => $q
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $taAktif?->id)
                ->where('semester', $semester))
            ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
            ->get()->groupBy('siswa_id');

        $rekap = [];
        foreach ($siswaList as $s) {
            $sa = $absensiData->get($s->id, collect());
            $row = ['nis' => $s->nis, 'nama' => $s->user->nama_lengkap ?? '-', 'absensi' => [], 'hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0];
            foreach ($tanggalList as $tgl) {
                $ab = $sa->firstWhere('tanggal', $tgl);
                $st = $ab ? $ab->status : null;
                $row['absensi'][$tgl] = $st;
                if ($st) $row[$st]++;
            }
            $rekap[] = $row;
        }

        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = $bulanIndo[(int) substr($bulan, 5, 2)] . ' ' . substr($bulan, 0, 4);

        $labelSemester = $semester == '1' ? 'Ganjil' : 'Genap';
        $reportSchool = $this->reportSchool($taAktif, $semester);
        $pdf = Pdf::loadView('exports.pdf.absensi', compact('rekap', 'tanggalList', 'kelas', 'namaBulan', 'labelSemester', 'taAktif', 'reportSchool'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("rekap_absensi_{$kelas->tingkat}_{$kelas->nama_kelas}_{$bulan}.pdf");
    }

    // ─────────────────────────────────────────────
    // EXPORT PDF - REKAP TUGAS
    // ─────────────────────────────────────────────
    public function pdfTugas(Request $request)
    {
        $filters = $this->validatedExportFilters($request);
        $kelasId = $filters['kelas_id'];
        $semester = $filters['semester'];
        $taAktif = TahunAjaran::getAktif();

        $kelas = Kelas::findOrFail($kelasId);
        $totalSiswa = Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->count();

        $tugasList = Tugas::with(['kelasMapel.mataPelajaran', 'kelasMapel.guru'])
            ->whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $taAktif?->id)->where('semester', $semester))
            ->withCount(['pengumpulan as sudah_kumpul' => fn($q) => $q
                ->whereIn('status', ['sudah', 'terlambat', 'dinilai'])
                ->whereHas('siswa', fn($siswa) => $siswa->where('kelas_id', $kelasId)->where('status', 'aktif'))])
            ->orderBy('created_at', 'desc')
            ->get();

        $labelSemester = $semester == '1' ? 'Ganjil' : 'Genap';
        $reportSchool = $this->reportSchool($taAktif, $semester);
        $pdf = Pdf::loadView('exports.pdf.tugas', compact('tugasList', 'kelas', 'labelSemester', 'taAktif', 'totalSiswa', 'reportSchool'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("rekap_tugas_{$kelas->tingkat}_{$kelas->nama_kelas}_semester_{$semester}.pdf");
    }

    private function mapelListUntukKelas(int|string|null $kelasId, int|string|null $tahunAjaranId, string $semester)
    {
        return KelasMapel::with('mataPelajaran')
            ->where('kelas_id', $kelasId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
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
    }

    private function validatedExportFilters(Request $request, bool $withMonth = false): array
    {
        $rules = [
            'kelas_id' => 'required|integer|exists:kelas,id',
            'semester' => 'nullable|in:1,2',
        ];

        if ($withMonth) {
            $rules['bulan'] = 'nullable|date_format:Y-m';
        }

        $validated = $request->validate($rules);

        return [
            'kelas_id' => (int) $validated['kelas_id'],
            'semester' => $validated['semester'] ?? Pengaturan::getValue('semester_aktif', '1'),
            'bulan' => $validated['bulan'] ?? date('Y-m'),
        ];
    }

    private function reportSchool(?TahunAjaran $tahunAjaran, string $semester): array
    {
        $labelSemester = $semester === '1' ? 'Ganjil' : ($semester === '2' ? 'Genap' : $semester);
        $logoPath = school_setting('logo_path');

        return [
            'name' => school_setting('school_name', 'Nama Sekolah'),
            'short_name' => school_setting('school_short_name', 'LMS'),
            'address' => school_setting('address', 'Alamat sekolah belum diatur'),
            'phone' => school_setting('phone'),
            'email' => school_setting('email'),
            'website' => school_setting('website'),
            'principal_name' => school_setting('principal_name', 'Nama Kepala Sekolah'),
            'principal_nip' => school_setting('principal_nip'),
            'principal_nuptk' => school_setting('principal_nuptk'),
            'school_year' => $tahunAjaran?->tahun ?? school_setting('school_year', '-'),
            'semester' => $labelSemester,
            'logo' => $this->logoDataUri($logoPath),
        ];
    }

    private function logoDataUri(?string $path): string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return school_logo_url();
        }

        $fullPath = Storage::disk('public')->path($path);
        $contents = file_get_contents($fullPath);
        $mime = mime_content_type($fullPath) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    private function excelReportHeader(string $title, array $school, string $context): array
    {
        $principalId = $school['principal_nip'] ?: $school['principal_nuptk'];

        return [
            [$school['name']],
            [$school['address']],
            [$title],
            [$context],
            ['Tahun Ajaran', $school['school_year'], 'Semester', $school['semester']],
            ['Kepala Sekolah', $school['principal_name'], 'NIP/NUPTK', $principalId ?: '-'],
            [],
        ];
    }

    private function temporaryExcelPath(string $prefix): string
    {
        return tempnam(sys_get_temp_dir(), $prefix);
    }
}
