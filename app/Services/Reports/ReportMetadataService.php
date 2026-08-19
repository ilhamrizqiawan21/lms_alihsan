<?php

namespace App\Services\Reports;

use App\Models\Pengaturan;
use App\Models\TahunAjaran;

final class ReportMetadataService
{
    public function context(?string $semester = null): ReportContext
    {
        $tahunAjaran = TahunAjaran::getAktif();
        $semester = $semester ?: (string) Pengaturan::getValue('semester_aktif', '1');

        return new ReportContext(
            tahunAjaran: $tahunAjaran,
            semester: $semester,
            school: $this->school(),
        );
    }

    public function school(): array
    {
        return [
            'nama' => $this->firstSetting(['nama_sekolah', 'school_name', 'nama_instansi'], 'Sekolah'),
            'alamat' => $this->firstSetting(['alamat_sekolah', 'school_address', 'alamat'], ''),
            'telepon' => $this->firstSetting(['telepon_sekolah', 'school_phone', 'telepon'], ''),
            'email' => $this->firstSetting(['email_sekolah', 'school_email', 'email'], ''),
            'npsn' => $this->firstSetting(['npsn'], ''),
            'logo' => $this->logoPath(),
        ];
    }

    private function firstSetting(array $keys, string $default): string
    {
        foreach ($keys as $key) {
            $value = Pengaturan::getValue($key);
            if ($value !== null && trim($value) !== '') {
                return trim($value);
            }
        }

        return $default;
    }

    private function logoPath(): ?string
    {
        $configured = $this->firstSetting(['logo_sekolah', 'school_logo', 'logo'], '');
        if ($configured === '') {
            return null;
        }

        if (is_file(public_path($configured))) {
            return public_path($configured);
        }

        if (is_file(storage_path('app/public/'.$configured))) {
            return storage_path('app/public/'.$configured);
        }

        return null;
    }
}
