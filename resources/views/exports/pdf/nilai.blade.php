<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; }
        h2, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8pt; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
        th { background-color: #4a5568; color: white; }
        .rata { font-weight: bold; background-color: #e2e8f0; }
        .header-info { text-align: center; margin-bottom: 10px; font-size: 9pt; color: #555; }
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
    <h2>REKAP NILAI</h2>
    <h4>Kelas {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</h4>
    <div class="header-info">
        Semester {{ $reportSchool['semester'] ?? $labelSemester }} Tahun Ajaran {{ $reportSchool['school_year'] ?? ($taAktif?->tahun ?? '-') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                @foreach($mapelList as $mp)
                <th>{{ $mp->nama_mapel }}</th>
                @endforeach
                <th>Rata²</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r['nis'] }}</td>
                <td style="text-align:left;">{{ $r['nama'] }}</td>
                @foreach($mapelList as $mp)
                <td>{{ $r['nilai'][$mp->id] ?? '-' }}</td>
                @endforeach
                <td class="rata">{{ $r['rata'] ?? '-' }}</td>
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
