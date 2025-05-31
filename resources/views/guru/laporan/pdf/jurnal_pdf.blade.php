<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Jurnal Mengajar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin-bottom: 5px;
        }
        .header p {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10pt;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>JURNAL MENGAJAR</h2>
            <p>Kelas {{ $kelas->nama_kelas }} - Bulan {{ $bulan }} {{ $tahun }}</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Mata Pelajaran</th>
                    <th>Materi</th>
                    <th>Kegiatan</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @if(count($data) > 0)
                    @foreach($data as $row)
                    <tr>
                        <td style="width: 5%">{{ $row['no'] }}</td>
                        <td style="width: 10%">{{ $row['tanggal'] }}</td>
                        <td style="width: 15%">{{ $row['pelajaran'] }}</td>
                        <td style="width: 20%">{{ $row['materi'] }}</td>
                        <td style="width: 25%">{{ $row['kegiatan'] }}</td>
                        <td style="width: 25%">{{ $row['catatan'] }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data jurnal mengajar</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="signature">
            <p>{{ now()->format('d F Y') }}</p>
            <p>Guru Pengampu,</p>
            <br><br><br>
            <p><strong>{{ $guru->nama_lengkap }}</strong></p>
            <p>NIP. {{ $guru->nip ?? '-' }}</p>
        </div>
    </div>
</body>
</html>
