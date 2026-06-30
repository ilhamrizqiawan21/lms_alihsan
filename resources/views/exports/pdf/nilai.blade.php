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
    </style>
</head>
<body>
    <h2>REKAP NILAI</h2>
    <h4>Kelas {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</h4>
    <div class="header-info">
        Semester {{ $labelSemester }} Tahun Ajaran {{ $taAktif?->tahun ?? '-' }}
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
</body>
</html>
