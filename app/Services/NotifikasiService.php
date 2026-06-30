<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Siswa;
use App\Models\User;

class NotifikasiService
{
    /**
     * Buat notifikasi untuk semua siswa dalam satu kelas_mapel.
     */
    public function notifikasiKelasMapel(int $kelasMapelId, string $tipe, string $judul, string $pesan, ?string $link = null): void
    {
        $siswas = Siswa::whereHas('kelas.kelasMapel', function ($q) use ($kelasMapelId) {
            $q->where('kelas_mapel.id', $kelasMapelId);
        })->where('status', 'aktif')->get();

        foreach ($siswas as $siswa) {
            Notifikasi::create([
                'user_id' => $siswa->user_id,
                'tipe' => $tipe,
                'judul' => $judul,
                'pesan' => $pesan,
                'link' => $link,
            ]);
        }
    }

    /**
     * Buat notifikasi untuk satu user.
     */
    public function notifikasiUser(int $userId, string $tipe, string $judul, string $pesan, ?string $link = null): Notifikasi
    {
        return Notifikasi::create([
            'user_id' => $userId,
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
        ]);
    }
}
