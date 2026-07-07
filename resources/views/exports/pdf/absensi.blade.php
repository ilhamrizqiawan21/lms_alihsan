<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; }
        h2, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 7pt; }
        th, td { border: 1px solid #333; padding: 2px 3px; text-align: center; }
        th { background-color: #4a5568; color: white; font-size: 6pt; }
        .hadir { font-weight: bold; }
        .sakit { color: #d97706; }
        .izin { color: #3b82f6; }
        .alpha { color: #ef4444; font-weight: bold; }
        .total-h { background-color: #dcfce7; }
        .total-s { background-color: #fef3c7; }
        .total-i { background-color: #dbeafe; }
        .total-a { background-color: #fee2e2; }
        .header-info { text-align: center; margin-bottom: 10px; font-size: 9pt; color: #555; }
        .legend { font-size: 7pt; margin-top: 5px; }
    </style>
</head>
<body>
    <h2>REKAP ABSENSI</h2>
    <h4>Kelas {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</h4>
    <div class="header-info">
        Bulan {{ $namaBulan }}<br>
        Semester {{ $labelSemester ?? '-' }} Tahun Ajaran {{ $taAktif?->tahun ?? '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                @foreach($tanggalList as $tgl)
                <th>{{ date('d', strtotime($tgl)) }}</th>
                @endforeach
                <th class="total-h">H</th>
                <th class="total-s">S</th>
                <th class="total-i">I</th>
                <th class="total-a">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r['nis'] }}</td>
                <td style="text-align:left;">{{ $r['nama'] }}</td>
                @foreach($tanggalList as $tgl)
                <td>
                    @php $st = $r['absensi'][$tgl] ?? null; @endphp
                    @if($st === 'hadir') <span class="hadir">H</span>
                    @elseif($st === 'sakit') <span class="sakit">S</span>
                    @elseif($st === 'izin') <span class="izin">I</span>
                    @elseif($st === 'alpha') <span class="alpha">A</span>
                    @else -
                    @endif
                </td>
                @endforeach
                <td class="total-h">{{ $r['hadir'] }}</td>
                <td class="total-s">{{ $r['sakit'] }}</td>
                <td class="total-i">{{ $r['izin'] }}</td>
                <td class="total-a">{{ $r['alpha'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <strong>Keterangan:</strong> H = Hadir | S = Sakit | I = Izin | A = Alpha
    </div>

    <div style="margin-top:20px; font-size:8pt; text-align:right;">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
