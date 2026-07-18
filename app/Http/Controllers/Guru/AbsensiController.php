<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AcademicAuditLog;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|date_format:Y-m',
            'kelas_mapel_id' => 'nullable|integer',
        ]);

        $guruId = Auth::id();
        $bulan = $request->input('bulan', date('Y-m'));
        $bulanNum = (int) substr($bulan, 5, 2);
        $tahun = (int) substr($bulan, 0, 4);
        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $kelasMapelId = $request->input('kelas_mapel_id');

        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('guru_id', $guruId)
            ->aktif()
            ->get();

        $kmData = null;
        $siswaList = collect();
        $meetings = collect();
        $absensiData = [];

        if ($kelasMapelId) {
            $kmData = $kelasMapel->firstWhere('id', (int) $kelasMapelId);

            if ($kmData) {
                $siswaList = Siswa::with('user')
                    ->where('kelas_id', $kmData->kelas_id)
                    ->where('status', 'aktif')
                    ->orderBy('nis')
                    ->get();

                $meetings = $this->attendanceMeetings($bulan, (int) $kmData->pertemuan_per_minggu);

                // Ambil data absensi
                $absensiRaw = Absensi::where('kelas_mapel_id', $kmData->id)
                    ->whereIn('siswa_id', $siswaList->pluck('id'))
                    ->whereBetween('tanggal', ["{$bulan}-01", date('Y-m-t', strtotime("{$bulan}-01"))])
                    ->get();

                foreach ($siswaList as $s) {
                    $absensiData[$s->id] = [];
                }
                foreach ($absensiRaw as $a) {
                    $tgl = $a->tanggal instanceof \Carbon\Carbon ? $a->tanggal->format('Y-m-d') : $a->tanggal;
                    $absensiData[$a->siswa_id][$tgl] = $a->status;
                }
            }
        }

        return Inertia::render('Guru/Absensi/Index', [
            'kelasMapel' => $kelasMapel->map(fn (KelasMapel $item) => [
                'id' => $item->id,
                'label' => trim(($item->kelas?->nama_kelas ?? '-') . ' - ' . ($item->mataPelajaran?->nama_mapel ?? '-') . ' (' . (int) $item->pertemuan_per_minggu . 'x/minggu)'),
                'kelas' => $item->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $item->mataPelajaran?->nama_mapel ?? '-',
                'pertemuan_per_minggu' => (int) $item->pertemuan_per_minggu,
            ])->values(),
            'filters' => [
                'kelas_mapel_id' => $kelasMapelId ? (string) $kelasMapelId : '',
                'bulan' => $bulan,
            ],
            'selected' => $kmData ? [
                'id' => $kmData->id,
                'kelas' => $kmData->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $kmData->mataPelajaran?->nama_mapel ?? '-',
                'pertemuan_per_minggu' => (int) $kmData->pertemuan_per_minggu,
                'store_url' => route('guru.absensi.store', $kmData),
                'export_excel_url' => route('guru.absensi.export.excel', $kmData),
                'export_pdf_url' => route('guru.absensi.export.pdf', $kmData),
            ] : null,
            'weeks' => $meetings,
            'students' => $siswaList->values()->map(function (Siswa $siswa, int $index) use ($meetings, $absensiData) {
                $weekly = [];

                foreach ($meetings as $meeting) {
                    $date = $meeting['date'];
                    $weekly[$meeting['key']] = $date ? ($absensiData[$siswa->id][$date] ?? '') : '';
                }

                return [
                    'id' => $siswa->id,
                    'no' => $index + 1,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->user?->nama_lengkap ?? '-',
                    'absensi' => $weekly,
                ];
            }),
        ]);
    }

    public function create(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        return redirect()->route('guru.absensi.index', ['kelas_mapel_id' => $kelasMapel->id]);
    }

    //Simpan Absensi
    public function store(Request $request, KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $validated = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'absensi' => 'nullable|array',
            'absensi.*' => 'array',
            'absensi.*.*' => 'nullable|in:hadir,sakit,izin,alpha',
        ]);

        $absensiInput = $validated['absensi'] ?? [];
        $validSiswaIds = Siswa::where('kelas_id', $kelasMapel->kelas_id)
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn($id) => (string) $id);

        if (collect(array_keys($absensiInput))->diff($validSiswaIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'absensi' => 'Data absensi berisi siswa yang tidak termasuk kelas ini.',
            ]);
        }

        $bulan = $validated['bulan'];
        $meetings = $this->attendanceMeetings($bulan, (int) $kelasMapel->pertemuan_per_minggu)
            ->keyBy('key');

        $invalidMeetingKeys = collect($absensiInput)
            ->flatMap(fn (array $meetingData) => array_keys($meetingData))
            ->unique()
            ->diff($meetings->keys());

        if ($invalidMeetingKeys->isNotEmpty()) {
            throw ValidationException::withMessages([
                'absensi' => 'Data absensi berisi pertemuan yang tidak valid untuk bulan ini.',
            ]);
        }

        $meetingDates = $meetings->pluck('date')->filter()->values();
        $existingAbsensi = Absensi::where('kelas_mapel_id', $kelasMapel->id)
            ->whereIn('siswa_id', $validSiswaIds->map(fn ($id) => (int) $id))
            ->whereIn('tanggal', $meetingDates)
            ->get()
            ->keyBy(fn (Absensi $absensi) => $absensi->siswa_id . '|' . $absensi->tanggal->format('Y-m-d'));
        $siswasForLog = Siswa::with('user')
            ->whereIn('id', $validSiswaIds->map(fn ($id) => (int) $id))
            ->get()
            ->keyBy('id');
        $changedSiswaIds = collect();

        if ($absensiInput) {
            foreach ($absensiInput as $siswaId => $mingguData) {
                foreach ($mingguData as $meetingKey => $status) {
                    $meeting = $meetings->get($meetingKey);

                    if ($meeting) {
                        $scope = [
                            'siswa_id' => (int) $siswaId,
                            'kelas_mapel_id' => $kelasMapel->id,
                            'tanggal' => $meeting['date'],
                        ];
                        $existingKey = ((int) $siswaId) . '|' . $meeting['date'];
                        $existing = $existingAbsensi->get($existingKey);
                        $existingStatus = $existing?->status;

                        if (($existingStatus ?? '') === ($status ?? '')) {
                            continue;
                        }

                        $changedSiswaIds->push((int) $siswaId);

                        if (!$status) {
                            Absensi::where($scope)->delete();
                            $this->logAbsensiChange($kelasMapel, $siswasForLog->get((int) $siswaId), $meeting['date'], $existing, $existingStatus, null);
                            continue;
                        }

                        $absensi = Absensi::updateOrCreate(
                            $scope,
                            ['status' => $status]
                        );
                        $this->logAbsensiChange($kelasMapel, $siswasForLog->get((int) $siswaId), $meeting['date'], $absensi, $existingStatus, $status);
                    }
                }
            }
        }

        // Kirim notifikasi hanya untuk siswa yang datanya benar-benar berubah.
        if ($changedSiswaIds->isNotEmpty()) {
            $siswaIds = $changedSiswaIds->unique()->values();
            $siswas = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');
            $bulanLabel = $validated['bulan'];

            foreach ($siswaIds as $siswaId) {
                $siswa = $siswas->get((int) $siswaId);
                if (!$siswa || !$siswa->user_id) continue;

                Notifikasi::firstOrCreate([
                    'user_id' => $siswa->user_id,
                    'tipe' => 'absensi',
                    'judul' => 'Absensi Diperbarui',
                    'pesan' => "Absensi {$kelasMapel->mataPelajaran?->nama_mapel} bulan {$bulanLabel} telah dicatat.",
                    'link' => route('siswa.progress'),
                    'is_read' => false,
                ]);
            }
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function rekap(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        return redirect()->route('guru.absensi.index', ['kelas_mapel_id' => $kelasMapel->id]);
    }

    private function attendanceMeetings(string $bulan, int $meetingsPerWeek): \Illuminate\Support\Collection
    {
        $meetingsPerWeek = max(1, min($meetingsPerWeek, 6));
        $monthNumber = (int) substr($bulan, 5, 2);
        $firstDay = \Carbon\Carbon::create((int) substr($bulan, 0, 4), $monthNumber, 1);
        $firstMonday = $firstDay->copy();

        if ($firstDay->dayOfWeek !== 1) {
            $firstMonday->addDays((8 - $firstDay->dayOfWeek) % 7);
        }

        $meetings = [];

        for ($week = 1; $week <= 5; $week++) {
            $weekStart = $firstMonday->copy()->addDays(($week - 1) * 7);

            for ($meeting = 1; $meeting <= $meetingsPerWeek; $meeting++) {
                $offset = (int) round((($meeting - 1) * 6) / $meetingsPerWeek);
                $date = $weekStart->copy()->addDays($offset);

                if ((int) $date->format('m') !== $monthNumber) {
                    continue;
                }

                $meetings[] = [
                    'key' => "{$week}-{$meeting}",
                    'week' => $week,
                    'meeting' => $meeting,
                    'date' => $date->format('Y-m-d'),
                    'label' => $date->format('d/m'),
                    'title' => $meetingsPerWeek > 1 ? "M{$week} P{$meeting}" : "Minggu {$week}",
                ];
            }
        }

        return collect($meetings);
    }

    private function logAbsensiChange(KelasMapel $kelasMapel, ?Siswa $siswa, string $tanggal, ?Absensi $absensi, ?string $before, ?string $after): void
    {
        if (! $siswa) {
            return;
        }

        try {
            AcademicAuditLog::create([
                'actor_id' => Auth::id(),
                'module' => 'absensi',
                'action' => $after ? 'update' : 'delete',
                'auditable_type' => Absensi::class,
                'auditable_id' => $absensi?->id,
                'before_values' => ['status' => $before ?: '-'],
                'after_values' => ['status' => $after ?: '-'],
                'metadata' => [
                    'siswa' => $siswa->user?->nama_lengkap ?? $siswa->nis,
                    'nis' => $siswa->nis,
                    'kelas' => $kelasMapel->kelas?->nama_kelas ?? '-',
                    'mata_pelajaran' => $kelasMapel->mataPelajaran?->nama_mapel ?? '-',
                    'tanggal' => $tanggal,
                ],
            ]);
        } catch (\Throwable) {
            // Audit log tidak boleh menggagalkan simpan absensi.
        }
    }
}
