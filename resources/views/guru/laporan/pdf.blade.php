<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        h1 {
            font-size: 18px;
            margin: 5px 0;
        }
        h2 {
            font-size: 14px;
            font-weight: normal;
            margin: 5px 0 15px;
        }
        .teacher-info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
        }
        td {
            padding: 6px 8px;
        }
        .summary {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .summary-table {
            width: auto;
            margin-right: 40px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
            width: 200px;
            float: right;
        }
        .status-hadir {
            background-color: #d4edda;
        }
        .status-izin {
            background-color: #d1ecf1;
        }
        .status-sakit {
            background-color: #fff3cd;
        }
        .status-alpha {
            background-color: #f8d7da;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>{{ $subtitle }}</h2>
    </div>
    
    <div class="teacher-info">
        <table style="border: none; margin-bottom: 20px;">
            <tr>
                <td style="border: none; width: 100px;"><strong>Nama Guru</strong></td>
                <td style="border: none; width: 10px;">:</td>
                <td style="border: none;">{{ $guru->nama_lengkap }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>NIP</strong></td>
                <td style="border: none;">:</td>
                <td style="border: none;">{{ $guru->nip ?: 'N/A' }}</td>
            </tr>
        </table>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="10%">NISN</th>
                <th width="20%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="25%">Mata Pelajaran</th>
                <th width="10%">Status</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($attendanceData as $item)
            <tr class="status-{{ $item->status }}">
                <td>{{ $no++ }}</td>
                <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                <td>{{ $item->siswa->nisn ?? '-' }}</td>
                <td>{{ $item->siswa->nama_lengkap ?? 'Unknown' }}</td>
                <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $item->jadwal->pelajaran->nama_pelajaran ?? '-' }} ({{ $item->jadwal->pelajaran->kode_pelajaran ?? '-' }})</td>
                <td>{{ ucfirst($item->status) }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data kehadiran yang ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="summary">
        <h3>Ringkasan Kehadiran:</h3>
        <table class="summary-table" style="width: 300px;">
            <tr>
                <th>Status</th>
                <th>Jumlah</th>
                <th>Persentase</th>
            </tr>
            <tr class="status-hadir">
                <td>Hadir</td>
                <td>{{ $summary['hadir'] }}</td>
                <td>{{ $summary['total'] > 0 ? round(($summary['hadir'] / $summary['total']) * 100, 2) : 0 }}%</td>
            </tr>
            <tr class="status-izin">
                <td>Izin</td>
                <td>{{ $summary['izin'] }}</td>
                <td>{{ $summary['total'] > 0 ? round(($summary['izin'] / $summary['total']) * 100, 2) : 0 }}%</td>
            </tr>
            <tr class="status-sakit">
                <td>Sakit</td>
                <td>{{ $summary['sakit'] }}</td>
                <td>{{ $summary['total'] > 0 ? round(($summary['sakit'] / $summary['total']) * 100, 2) : 0 }}%</td>
            </tr>
            <tr class="status-alpha">
                <td>Alpha</td>
                <td>{{ $summary['alpha'] }}</td>
                <td>{{ $summary['total'] > 0 ? round(($summary['alpha'] / $summary['total']) * 100, 2) : 0 }}%</td>
            </tr>
            <tr>
                <th>Total</th>
                <th>{{ $summary['total'] }}</th>
                <th>100%</th>
            </tr>
        </table>
    </div>
    
    <div class="signature">
        <p>{{ date('d F Y') }}</p>
        <p>Guru Pengampu</p>
        <br><br><br>
        <p><strong>{{ $guru->nama_lengkap }}</strong></p>
        <p>NIP: {{ $guru->nip ?: 'N/A' }}</p>
    </div>
    
    <div style="clear: both;"></div>
    
    <div class="footer">
        <p>Laporan dibuat secara otomatis oleh Sistem Absensi pada {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
