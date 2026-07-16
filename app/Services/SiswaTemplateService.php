<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\User;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class SiswaTemplateService
{
    public const FILENAME = 'template_import_siswa.xlsx';

    public const HEADERS = [
        'username',
        'nama_lengkap',
        'nis',
        'kelas_id',
        'password',
        'jenis_kelamin',
        'angkatan',
        'status',
        'is_active',
    ];

    public function createTemplateFile(): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'template_import_siswa_');
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $contohKelas = $kelasList->first();

        $writer = new Writer();
        $writer->openToFile($filePath);

        $writer->getCurrentSheet()->setName('Template Siswa');
        $writer->addRow(Row::fromValues(self::HEADERS));
        $writer->addRow(Row::fromValues([
            'siswa001',
            'Nama Siswa Contoh',
            '2026001',
            $contohKelas?->id ?? '',
            User::DEFAULT_PASSWORD,
            'L',
            '2026',
            'aktif',
            '1',
        ]));

        $kelasSheet = $writer->addNewSheetAndMakeItCurrent();
        $kelasSheet->setName('Daftar Kelas');
        $writer->addRow(Row::fromValues(['kelas_id', 'tingkat', 'nama_kelas', 'label']));

        $kelasList->each(function (Kelas $kelas) use ($writer) {
            $writer->addRow(Row::fromValues([
                $kelas->id,
                $kelas->tingkat,
                $kelas->nama_kelas,
                trim("{$kelas->tingkat} {$kelas->nama_kelas}"),
            ]));
        });

        $writer->close();

        return $filePath;
    }
}
