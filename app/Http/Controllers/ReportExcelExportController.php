<?php

namespace App\Http\Controllers;

use App\Services\Reports\Exports\AbsensiExportService;
use App\Services\Reports\Exports\NilaiExportService;
use App\Services\Reports\Exports\TugasExportService;
use Illuminate\Http\Request;

final class ReportExcelExportController extends Controller
{
    public function __construct(
        private readonly NilaiExportService $nilai,
        private readonly AbsensiExportService $absensi,
        private readonly TugasExportService $tugas,
    ) {}

    public function nilai(Request $request)
    {
        $filters = $this->filters($request);
        [$path, $filename] = $this->nilai->export($filters['kelas_id'], $filters['semester']);
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function absensi(Request $request)
    {
        $filters = $this->filters($request, true);
        [$path, $filename] = $this->absensi->export($filters['kelas_id'], $filters['semester'], $filters['bulan']);
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function tugas(Request $request)
    {
        $filters = $this->filters($request);
        [$path, $filename] = $this->tugas->export($filters['kelas_id'], $filters['semester']);
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    private function filters(Request $request, bool $withMonth = false): array
    {
        $rules = [
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'semester' => ['required', 'in:1,2'],
        ];
        if ($withMonth) $rules['bulan'] = ['required', 'date_format:Y-m'];
        return $request->validate($rules);
    }
}
