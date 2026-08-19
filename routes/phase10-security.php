<?php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Guru\ChatController as GuruChatController;
use App\Http\Controllers\Guru\NotifikasiController as GuruNotifikasiController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Guru\SikapController;
use App\Http\Controllers\Guru\TugasController as GuruTugasController;
use App\Http\Controllers\Guru\WaliKelasController as GuruWaliKelasController;
use Illuminate\Support\Facades\Route;

// Phase 10 security route completion. These routes are intentionally isolated
// so the existing route map is not rewritten while security hardening is applied.
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/tugas', [GuruTugasController::class, 'index'])->name('tugas.index');
    Route::post('/tugas/store', [GuruTugasController::class, 'storeBulk'])->name('tugas.store.bulk');
    Route::get('/tugas/{kelasMapel}/list', [GuruTugasController::class, 'list'])->name('tugas.list')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/export/excel', [ExportController::class, 'guruTugasExcel'])->name('tugas.export.excel')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/export/pdf', [ExportController::class, 'guruTugasPdf'])->name('tugas.export.pdf')->middleware('can:mengajar,kelasMapel');
    Route::post('/tugas/{kelasMapel}/store', [GuruTugasController::class, 'store'])->name('tugas.store')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/{tugas}/pengumpulan', [GuruTugasController::class, 'pengumpulan'])->name('tugas.pengumpulan')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/{tugas}/pengumpulan/export/excel', [ExportController::class, 'guruPengumpulanTugasExcel'])->name('tugas.pengumpulan.export.excel')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/{tugas}/pengumpulan/export/pdf', [ExportController::class, 'guruPengumpulanTugasPdf'])->name('tugas.pengumpulan.export.pdf')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/{tugas}/pengumpulan/{file}/download', [GuruTugasController::class, 'downloadFile'])->name('tugas.file.download')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/{tugas}/pengumpulan/{pengumpulan}/legacy-download', [GuruTugasController::class, 'downloadLegacyFile'])->name('tugas.pengumpulan.download')->middleware('can:mengajar,kelasMapel');
    Route::post('/tugas/{kelasMapel}/{tugas}/siswa/{siswa}/nilai', [GuruTugasController::class, 'nilai'])->name('tugas.nilai')->middleware('can:mengajar,kelasMapel');
    Route::delete('/tugas/{tugas}', [GuruTugasController::class, 'destroy'])->name('tugas.destroy')->middleware('can:mengajar-tugas,tugas');

    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::post('/nilai/store', [NilaiController::class, 'storeBulk'])->name('nilai.store.bulk');
    Route::get('/nilai/{kelasMapel}/input', [NilaiController::class, 'input'])->name('nilai.input')->middleware('can:mengajar,kelasMapel');
    Route::get('/nilai/{kelasMapel}/export/excel', [ExportController::class, 'guruNilaiExcel'])->name('nilai.export.excel')->middleware('can:mengajar,kelasMapel');
    Route::get('/nilai/{kelasMapel}/export/pdf', [ExportController::class, 'guruNilaiPdf'])->name('nilai.export.pdf')->middleware('can:mengajar,kelasMapel');
    Route::post('/nilai/{kelasMapel}/store', [NilaiController::class, 'store'])->name('nilai.store')->middleware('can:mengajar,kelasMapel');

    Route::get('/sikap', [SikapController::class, 'index'])->name('sikap.index');
    Route::post('/sikap/store', [SikapController::class, 'storeBulk'])->name('sikap.store.bulk');
    Route::get('/sikap/{kelasMapel}/input', [SikapController::class, 'input'])->name('sikap.input')->middleware('can:mengajar,kelasMapel');
    Route::get('/sikap/{kelasMapel}/export/excel', [ExportController::class, 'guruSikapExcel'])->name('sikap.export.excel')->middleware('can:mengajar,kelasMapel');
    Route::get('/sikap/{kelasMapel}/export/pdf', [ExportController::class, 'guruSikapPdf'])->name('sikap.export.pdf')->middleware('can:mengajar,kelasMapel');
    Route::post('/sikap/{kelasMapel}/store', [SikapController::class, 'store'])->name('sikap.store')->middleware('can:mengajar,kelasMapel');
    Route::get('/rekap-nilai', [NilaiController::class, 'rekap'])->name('rekap-nilai');
    Route::get('/rekap-sikap', [SikapController::class, 'rekap'])->name('rekap-sikap');

    Route::get('/wali-kelas', [GuruWaliKelasController::class, 'index'])->name('wali-kelas.index');
    Route::get('/wali-kelas/{waliKelas}/absensi', [GuruWaliKelasController::class, 'absensi'])->name('wali-kelas.absensi')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::post('/wali-kelas/{waliKelas}/absensi', [GuruWaliKelasController::class, 'storeAbsensi'])->name('wali-kelas.absensi.store')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::get('/wali-kelas/{waliKelas}/pertemuan', [GuruWaliKelasController::class, 'pertemuan'])->name('wali-kelas.pertemuan')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::post('/wali-kelas/{waliKelas}/pertemuan', [GuruWaliKelasController::class, 'storePertemuan'])->name('wali-kelas.pertemuan.store')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::delete('/wali-kelas/{waliKelas}/pertemuan/{pertemuan}', [GuruWaliKelasController::class, 'destroyPertemuan'])->name('wali-kelas.pertemuan.destroy')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::get('/wali-kelas/{waliKelas}/penanganan', [GuruWaliKelasController::class, 'penanganan'])->name('wali-kelas.penanganan')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::post('/wali-kelas/{waliKelas}/penanganan', [GuruWaliKelasController::class, 'storePenanganan'])->name('wali-kelas.penanganan.store')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::put('/wali-kelas/{waliKelas}/penanganan/{penanganan}', [GuruWaliKelasController::class, 'updatePenanganan'])->name('wali-kelas.penanganan.update')->middleware('can:kelola-wali-kelas,waliKelas');
    Route::delete('/wali-kelas/{waliKelas}/penanganan/{penanganan}', [GuruWaliKelasController::class, 'destroyPenanganan'])->name('wali-kelas.penanganan.destroy')->middleware('can:kelola-wali-kelas,waliKelas');

    Route::get('/chat', [GuruChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{kelasMapel}', [GuruChatController::class, 'chat'])->name('chat.show')->middleware('can:mengajar,kelasMapel');
    Route::post('/chat/{kelasMapel}/send', [GuruChatController::class, 'send'])->name('chat.send')->middleware('can:mengajar,kelasMapel');
    Route::get('/notifikasi', [GuruNotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/read', [GuruNotifikasiController::class, 'markRead'])->name('notifikasi.mark-read');
    Route::post('/notifikasi/mark-all-read', [GuruNotifikasiController::class, 'markAllRead'])->name('notifikasi.mark-all-read');
});

Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/pengaturan', [AccountSettingsController::class, 'edit'])->name('pengaturan');
    Route::put('/pengaturan', [AccountSettingsController::class, 'update'])->name('pengaturan.update');
    Route::get('/profil', [AccountSettingsController::class, 'edit'])->name('profil');
    Route::put('/profil', [AccountSettingsController::class, 'update'])->name('profil.update');
});
