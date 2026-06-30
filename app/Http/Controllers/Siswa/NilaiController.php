<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\NilaiAkhir;
use App\Models\Siswa;
use App\Services\NilaiService;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    protected NilaiService $nilaiService;

    public function __construct(NilaiService $nilaiService)
    {
        $this->nilaiService = $nilaiService;
    }

    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $nilaiList = NilaiAkhir::with(['kelasMapel.mataPelajaran', 'tahunAjaran'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('tahun_ajaran_id', 'desc')
            ->orderBy('semester', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->tahunAjaran?->tahun . ' - Semester ' . $item->semester;
            });

        return view('siswa.nilai.index', compact('nilaiList', 'siswa'));
    }
}
