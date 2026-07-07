<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
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
        $mingguCount = 4;
        $tanggalMinggu = [];
        $absensiData = [];

        if ($kelasMapelId) {
            $kmData = $kelasMapel->firstWhere('id', (int) $kelasMapelId);

            if ($kmData) {
                $siswaList = Siswa::with('user')
                    ->where('kelas_id', $kmData->kelas_id)
                    ->where('status', 'aktif')
                    ->orderBy('nis')
                    ->get();

                // Hitung minggu dalam bulan
                $firstDay = \Carbon\Carbon::create($tahun, $bulanNum, 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startDow = $firstDay->dayOfWeek;

                $tanggalMinggu = [];
                $minggu = 1;
                // Minggu 1: Senin pertama
                $seninPertama = $firstDay->copy();
                if ($startDow != 1) {
                    $seninPertama->addDays((8 - $startDow) % 7);
                }
                for ($w = 1; $w <= 5; $w++) {
                    $tgl = $seninPertama->copy()->addDays(($w - 1) * 7);
                    if ((int) $tgl->format('m') === $bulanNum) {
                        $tanggalMinggu[$w] = $tgl->format('Y-m-d');
                        $mingguCount = max($mingguCount, $w);
                    }
                }

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

        return view('guru.absensi.index', compact(
            'kelasMapel', 'kelasMapelId', 'kmData', 'siswaList',
            'mingguCount', 'tanggalMinggu', 'absensiData',
            'bulan', 'bulanNum', 'tahun', 'bulanIndo'
        ));
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

        if ($absensiInput) {
            foreach ($absensiInput as $siswaId => $mingguData) {
                foreach ($mingguData as $minggu => $status) {
                    if (!$status) continue;
                    // Reconstruct tanggal dari minggu
                    $bulan = $validated['bulan'];
                    $firstDay = \Carbon\Carbon::create((int) substr($bulan, 0, 4), (int) substr($bulan, 5, 2), 1);
                    $startDow = $firstDay->dayOfWeek;
                    $seninPertama = $firstDay->copy();
                    if ($startDow != 1) {
                        $seninPertama->addDays((8 - $startDow) % 7);
                    }
                    $tgl = $seninPertama->copy()->addDays(((int) $minggu - 1) * 7);

                    if ((int) $tgl->format('m') === (int) substr($bulan, 5, 2)) {
                        Absensi::updateOrCreate(
                            [
                                'siswa_id' => (int) $siswaId,
                                'kelas_mapel_id' => $kelasMapel->id,
                                'tanggal' => $tgl->format('Y-m-d'),
                            ],
                            ['status' => $status]
                        );
                    }
                }
            }
        }

        // Kirim notifikasi ke siswa yang diabsen
        if ($absensiInput) {
            $siswaIds = array_keys($absensiInput);
            $siswas = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');
            $bulanLabel = $validated['bulan'];

            foreach ($absensiInput as $siswaId => $mingguData) {
                $siswa = $siswas->get((int) $siswaId);
                if (!$siswa || !$siswa->user_id) continue;

                // Ambil status terakhir (dari minggu terakhir yang diisi)
                $statusTerakhir = null;
                foreach ($mingguData as $status) {
                    if ($status) $statusTerakhir = $status;
                }

                $statusLabel = [
                    'hadir' => 'Hadir',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'alpha' => 'Alpha',
                ][$statusTerakhir] ?? $statusTerakhir;

                Notifikasi::create([
                    'user_id' => $siswa->user_id,
                    'tipe' => 'absensi',
                    'judul' => 'Absensi Diperbarui',
                    'pesan' => "Absensi {$kelasMapel->mataPelajaran?->nama_mapel} bulan {$bulanLabel} telah dicatat.",
                    'link' => route('siswa.progress'),
                ]);
            }
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}
