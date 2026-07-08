<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Tugas</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; }
        h2, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8pt; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background-color: #4a5568; color: white; }
        .header-info { text-align: center; margin-bottom: 10px; font-size: 9pt; color: #555; }
        .judul { text-align: left; }
        .school-header { width: 100%; display: table; margin-bottom: 6px; }
        .school-logo { display: table-cell; width: 70px; vertical-align: middle; text-align: center; }
        .school-logo img { max-width: 56px; max-height: 56px; }
        .school-identity { display: table-cell; vertical-align: middle; text-align: center; padding-right: 70px; }
        .school-name { font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .school-address { font-size: 9pt; margin-top: 2px; }
        .school-contact { font-size: 8pt; color: #555; margin-top: 2px; }
        .header-line { border-top: 2px solid #111; border-bottom: 1px solid #111; height: 2px; margin-bottom: 10px; }
        .signature { width: 240px; margin-left: auto; margin-top: 24px; font-size: 8pt; text-align: left; }
        .signature-space { height: 42px; }
    </style>
</head>
<body>
    @include('exports.pdf._school-header')
    <h2>REKAP TUGAS</h2>
    <h4>Kelas {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</h4>
    <div class="header-info">
        Semester {{ $reportSchool['semester'] ?? $labelSemester }} Tahun Ajaran {{ $reportSchool['school_year'] ?? ($taAktif?->tahun ?? '-') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th style="text-align:left;">Judul Tugas</th>
                <th>Mapel</th>
                <th>Guru</th>
                <th>Deadline</th>
                <th>Kategori</th>
                <th>Sudah Kumpul</th>
                <th>Total</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tugasList as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="judul">{{ $t->judul }}</td>
                <td>{{ $t->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</td>
                <td>{{ $t->kelasMapel?->guru?->nama_lengkap ?? '-' }}</td>
                <td>{{ $t->batas_waktu ? date('d/m/Y H:i', strtotime($t->batas_waktu)) : '-' }}</td>
                <td>{{ $t->kategori_nilai ?? 'NH' }}</td>
                <td>{{ $t->sudah_kumpul }}</td>
                <td>{{ $totalSiswa }}</td>
                <td>{{ $totalSiswa > 0 ? round(($t->sudah_kumpul / $totalSiswa) * 100) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px; font-size:8pt; text-align:right;">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
    <div class="signature">
        Kepala Sekolah,
        <div class="signature-space"></div>
        <strong>{{ $reportSchool['principal_name'] }}</strong><br>
        @if($reportSchool['principal_nip'])
            NIP. {{ $reportSchool['principal_nip'] }}
        @elseif($reportSchool['principal_nuptk'])
            NUPTK. {{ $reportSchool['principal_nuptk'] }}
        @endif
    </div>
</body>
</html>
