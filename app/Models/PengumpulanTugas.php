<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengumpulanTugas extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'status',
        'nilai',
        'file_upload',
        'teks_jawaban',
        'catatan',
        'tanggal_kumpul',
    ];

    protected $casts = [
        'tanggal_kumpul' => 'datetime',
        'nilai' => 'decimal:2',
    ];

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PengumpulanFile::class, 'pengumpulan_id');
    }
}
