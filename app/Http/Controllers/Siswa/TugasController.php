<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\PengumpulanFile;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\Tugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TugasController extends Controller
{
    private const MAX_UPLOAD_FILES = 5;

    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $tugas = Tugas::with(['kelasMapel.mataPelajaran', 'pengumpulan' => function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        }])
            ->whereHas('kelasMapel', function ($q) use ($siswa) {
                $q->where('kelas_id', $siswa->kelas_id);
                $q->aktif();
            })
            ->orderBy('batas_waktu', 'desc')
            ->get();

        return Inertia::render('Siswa/Tugas/Index', [
            'tugas' => $tugas->map(function (Tugas $item) use ($siswa) {
                $pengumpulan = $item->pengumpulan->where('siswa_id', $siswa->id)->first();

                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'mata_pelajaran' => $item->kelasMapel?->mataPelajaran?->nama_mapel ?? '-',
                    'batas_waktu' => $item->batas_waktu ? Carbon::parse($item->batas_waktu)->format('d/m/Y') : '-',
                    'status' => $pengumpulan?->status,
                    'nilai' => $pengumpulan?->nilai ?? '-',
                    'show_url' => route('siswa.tugas.show', $item),
                ];
            })->values(),
        ]);
    }

    public function show(Tugas $tugas)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $tugas->loadMissing('kelasMapel.tahunAjaran');

        $this->ensureTugasAktifUntukSiswa($tugas, $siswa);

        $pengumpulan = PengumpulanTugas::with('files')
            ->where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        $tugas->loadMissing(['kelasMapel.mataPelajaran', 'kelasMapel.guru', 'kelasMapel.kelas']);

        return Inertia::render('Siswa/Tugas/Show', [
            'tugas' => [
                'id' => $tugas->id,
                'judul' => $tugas->judul,
                'kategori_nilai' => $tugas->kategori_nilai ?? 'NH',
                'mata_pelajaran' => $tugas->kelasMapel?->mataPelajaran?->nama_mapel ?? '-',
                'guru' => $tugas->kelasMapel?->guru?->nama_lengkap ?? '-',
                'kelas' => trim(($tugas->kelasMapel?->kelas?->tingkat ? $tugas->kelasMapel?->kelas?->tingkat . ' ' : '') . ($tugas->kelasMapel?->kelas?->nama_kelas ?? '')),
                'batas_waktu' => $tugas->batas_waktu ? Carbon::parse($tugas->batas_waktu)->format('d M Y') : '-',
                'is_late' => $tugas->batas_waktu ? now()->gt($tugas->batas_waktu) : false,
                'deskripsi' => $tugas->deskripsi ?? 'Tidak ada deskripsi',
                'store_url' => route('siswa.tugas.kumpul', $tugas),
                'back_url' => route('siswa.tugas.index'),
            ],
            'pengumpulan' => $pengumpulan ? [
                'id' => $pengumpulan->id,
                'status' => $pengumpulan->status,
                'tanggal_kumpul' => $pengumpulan->tanggal_kumpul ? Carbon::parse($pengumpulan->tanggal_kumpul)->format('d M Y H:i') : '-',
                'nilai' => $pengumpulan->nilai,
                'teks_jawaban' => $pengumpulan->teks_jawaban,
                'catatan' => $pengumpulan->catatan,
                'legacy_file_url' => $pengumpulan->file_upload ? route('siswa.tugas.pengumpulan.download', [$tugas, $pengumpulan]) : null,
                'files' => $pengumpulan->files->map(fn (PengumpulanFile $file) => [
                    'id' => $file->id,
                    'name' => $file->file_name,
                    'url' => route('siswa.tugas.file.download', [$tugas, $file]),
                ])->values(),
            ] : null,
            'canSubmit' => !$pengumpulan || $pengumpulan->status === 'belum',
        ]);
    }

    public function store(Request $request, Tugas $tugas)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        $validated = $request->validate([
            'files' => 'nullable|array|max:' . self::MAX_UPLOAD_FILES,
            'file_upload' => 'nullable|file|mimes:png,jpg,jpeg,pdf|extensions:png,jpg,jpeg,pdf|max:5120',
            'files.*' => 'nullable|file|mimes:png,jpg,jpeg,pdf|extensions:png,jpg,jpeg,pdf|max:5120',
            'teks_jawaban' => 'nullable|string|max:5000',
        ]);

        $tugas->loadMissing('kelasMapel.tahunAjaran');

        $this->ensureTugasAktifUntukSiswa($tugas, $siswa);

        $hasTextJawaban = filled($validated['teks_jawaban'] ?? null);
        $hasSingleFile = $request->hasFile('file_upload');
        $hasMultipleFiles = collect($request->file('files', []))->filter()->isNotEmpty();
        $totalUploadedFiles = ($hasSingleFile ? 1 : 0) + collect($request->file('files', []))->filter()->count();

        if (!$hasTextJawaban && !$hasSingleFile && !$hasMultipleFiles) {
            return back()
                ->withInput()
                ->withErrors(['file_upload' => 'Upload file atau isi jawaban teks terlebih dahulu.']);
        }

        if ($totalUploadedFiles > self::MAX_UPLOAD_FILES) {
            return back()
                ->withInput()
                ->withErrors(['files' => 'Maksimal ' . self::MAX_UPLOAD_FILES . ' file untuk satu pengumpulan tugas.']);
        }

        $existingPengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        if ($existingPengumpulan && $existingPengumpulan->status !== 'belum') {
            return back()->with('error', 'Tugas ini sudah dikumpulkan dan tidak dapat diubah.');
        }

        $statusPengumpulan = $tugas->batas_waktu && now()->gt($tugas->batas_waktu)
            ? 'terlambat'
            : 'sudah';

        // Simpan atau update pengumpulan
        $pengumpulan = PengumpulanTugas::updateOrCreate(
            [
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
            ],
            [
                'status' => $statusPengumpulan,
                'file_upload' => null, // akan diisi path pertama jika ada
                'teks_jawaban' => $validated['teks_jawaban'] ?? null,
                'tanggal_kumpul' => now(),
            ]
        );

        $uploadedFiles = [];

        // Upload single file (kompatibilitas dengan form lama)
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $path = $file->store('tugas/' . $tugas->id . '/' . $siswa->id, 'local');
            $uploadedFiles[] = [
                'pengumpulan_id' => $pengumpulan->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'uploaded_at' => now(),
            ];
        }

        // Upload multiple files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('tugas/' . $tugas->id . '/' . $siswa->id, 'local');
                $uploadedFiles[] = [
                    'pengumpulan_id' => $pengumpulan->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ];
            }
        }

        // Simpan ke tabel pengumpulan_files
        if (count($uploadedFiles) > 0) {
            PengumpulanFile::insert($uploadedFiles);
            // Set file_upload ke file pertama untuk kompatibilitas
            $pengumpulan->update(['file_upload' => $uploadedFiles[0]['file_path']]);
        }

        $guruId = $tugas->kelasMapel->guru_id;
        $notifikasiService = app(\App\Services\NotifikasiService::class);
        $notifikasiService->notifikasiUser(
            $guruId,
            'kumpul_tugas',
            'Siswa mengumpulkan tugas',
            "{$user->nama_lengkap} telah mengumpulkan tugas '{$tugas->judul}'.",
            route('guru.tugas.pengumpulan', [$tugas->kelas_mapel_id, $tugas->id])
        );

        return redirect()->route('siswa.tugas.show', $tugas)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    public function downloadFile(Tugas $tugas, PengumpulanFile $file)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $tugas->loadMissing('kelasMapel.tahunAjaran');

        $this->ensureTugasAktifUntukSiswa($tugas, $siswa);
        $file->loadMissing('pengumpulan');
        abort_unless($file->pengumpulan, 404);
        $this->ensurePengumpulanMilikSiswaDanTugas($file->pengumpulan, $tugas, $siswa);

        return $this->downloadPengumpulanPath($file->file_path, $file->file_name);
    }

    public function downloadLegacyFile(Tugas $tugas, PengumpulanTugas $pengumpulan)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $tugas->loadMissing('kelasMapel.tahunAjaran');

        $this->ensureTugasAktifUntukSiswa($tugas, $siswa);
        $this->ensurePengumpulanMilikSiswaDanTugas($pengumpulan, $tugas, $siswa);

        return $this->downloadPengumpulanPath($pengumpulan->file_upload, basename((string) $pengumpulan->file_upload));
    }

    private function ensureTugasAktifUntukSiswa(Tugas $tugas, ?Siswa $siswa): void
    {
        abort_unless(
            $siswa
            && $tugas->kelasMapel
            && (int) $siswa->kelas_id === (int) $tugas->kelasMapel->kelas_id
            && $tugas->kelasMapel->isAktif(),
            403,
            'Anda tidak memiliki akses ke tugas ini.'
        );
    }

    private function ensurePengumpulanMilikSiswaDanTugas(PengumpulanTugas $pengumpulan, Tugas $tugas, ?Siswa $siswa): void
    {
        abort_unless(
            $siswa
            && (int) $pengumpulan->siswa_id === (int) $siswa->id
            && (int) $pengumpulan->tugas_id === (int) $tugas->id,
            403
        );
    }

    private function downloadPengumpulanPath(?string $path, string $downloadName)
    {
        abort_unless($path, 404);

        $disk = Storage::disk('local');
        if (!$disk->exists($path)) {
            $disk = Storage::disk('public');
        }
        abort_unless($disk->exists($path), 404);

        return response()->download($disk->path($path), $downloadName);
    }
}
