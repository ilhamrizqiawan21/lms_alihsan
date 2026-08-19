<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\User;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderName;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\BorderWidth;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class SiswaExportService
{
    public function export($query): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'siswa_export_');
        $writer = new Writer();
        $writer->openToFile($filePath);
        $writer->getCurrentSheet()->setName('Data Siswa');

        foreach ([22, 32, 20, 18, 24, 12] as $column => $width) {
            $writer->getCurrentSheet()->setColumnWidth($width, $column + 1);
        }

        $styles = $this->styles();
        $school = school_setting('school_name', 'Nama Sekolah');

        $writer->addRow(Row::fromValuesWithStyle([$school], $styles['school'], 24));
        $writer->addRow(Row::fromValuesWithStyle(['EXPORT DATA SISWA'], $styles['title'], 24));
        $writer->addRow(Row::fromValuesWithStyle(['Tanggal Export', now()->format('d/m/Y H:i')], $styles['meta'], 18));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValuesWithStyle([
            'NIS', 'Username', 'Nama', 'Kelas', 'Jenis Kelamin', 'Status',
        ], $styles['tableHeader'], 24));

        $query->orderBy('nis')->chunk(500, function ($students) use ($writer, $styles) {
            foreach ($students as $index => $siswa) {
                $kelas = trim(($siswa->kelas?->tingkat ? $siswa->kelas->tingkat.' ' : '').($siswa->kelas?->nama_kelas ?? '')) ?: '-';
                $values = [
                    $siswa->nis ?? '-',
                    $siswa->user?->username ?? '-',
                    $siswa->user?->nama_lengkap ?? '-',
                    $kelas,
                    $siswa->user?->jenis_kelamin ?? '-',
                    $siswa->status ?? '-',
                ];
                $writer->addRow(Row::fromValuesWithStyle(
                    $values,
                    $index % 2 === 0 ? $styles['row'] : $styles['alternateRow'],
                    20
                ));
            }
        });

        $writer->close();
        return $filePath;
    }

    private function styles(): array
    {
        $border = new Border(
            new BorderPart(BorderName::TOP, Color::GRAY, BorderWidth::THIN),
            new BorderPart(BorderName::BOTTOM, Color::GRAY, BorderWidth::THIN),
            new BorderPart(BorderName::LEFT, Color::GRAY, BorderWidth::THIN),
            new BorderPart(BorderName::RIGHT, Color::GRAY, BorderWidth::THIN),
        );

        return [
            'school' => (new Style())->setFontBold()->setFontSize(16)->setHorizontalAlignment(CellAlignment::CENTER)->setVerticalAlignment(CellVerticalAlignment::CENTER),
            'title' => (new Style())->setFontBold()->setFontSize(13)->setHorizontalAlignment(CellAlignment::CENTER)->setVerticalAlignment(CellVerticalAlignment::CENTER),
            'meta' => (new Style())->setFontSize(10)->setBorder($border),
            'tableHeader' => (new Style())->setFontBold()->setFontSize(10)->setBorder($border)->setHorizontalAlignment(CellAlignment::CENTER)->setVerticalAlignment(CellVerticalAlignment::CENTER),
            'row' => (new Style())->setFontSize(10)->setBorder($border),
            'alternateRow' => (new Style())->setFontSize(10)->setBorder($border),
        ];
    }
}
