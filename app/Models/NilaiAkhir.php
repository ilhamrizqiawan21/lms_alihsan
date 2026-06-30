<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiAkhir extends Model
{
    protected $table = 'nilai_akhir';

    protected $fillable = [
        'siswa_id',
        'kelas_mapel_id',
        'tahun_ajaran_id',
        'semester',
        'sum1',
        'sum2',
        'sum3',
        'sum4',
        'nilai_harian',
        'sts',
        'sas',
        'sat',
    ];

    public $timestamps = false;

    // rata_akhir is a MySQL GENERATED ALWAYS AS column — excluded from fillable

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function kelasMapel(): BelongsTo
    {
        return $this->belongsTo(KelasMapel::class, 'kelas_mapel_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
