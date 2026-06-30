<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    public $timestamps = false;

    protected $fillable = [
        'tingkat',
        'nama_kelas',
    ];

    // Relasi: kelas memiliki banyak siswa melalui tabel siswa
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    // Relasi: kelas memiliki banyak kelas_mapel
    public function kelasMapel(): HasMany
    {
        return $this->hasMany(KelasMapel::class, 'kelas_id');
    }

    // Relasi: kelas memiliki banyak mata pelajaran melalui pivot
    public function mataPelajaran(): BelongsToMany
    {
        return $this->belongsToMany(MataPelajaran::class, 'kelas_mapel', 'kelas_id', 'mapel_id')
            ->withPivot(['guru_id', 'tahun_ajaran_id', 'semester']);
    }
}
