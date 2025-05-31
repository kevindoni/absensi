<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Absensi</title>
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
            text-align: center;
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
        .summary {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .text-danger {
            color: #ff0000;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>LAPORAN ABSENSI SISWA</h2>
            <p>Kelas {{ $kelas->nama_kelas }} - Bulan {{ $bulan }} {{ $tahun }}</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>NISN</th>
                    <th>Hadir</th>
                    <th>Terlambat</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpha</th>
                    <th>Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td style="text-align: left">{{ $row['nama'] }}</td>
                    <td>{{ $row['nisn'] }}</td>
                    <td>{{ $row['hadir'] }}</td>
                    <td>{{ $row['terlambat'] }}</td>
                    <td>{{ $row['izin'] }}</td>
                    <td>{{ $row['sakit'] }}</td>
                    <td>{{ $row['alpha'] }}</td>
                    <td>{{ $row['kehadiran'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="summary">
            <p><strong>Total Hari Sekolah:</strong> {{ $totalHariSekolah }} hari</p>
        </div>
        
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
