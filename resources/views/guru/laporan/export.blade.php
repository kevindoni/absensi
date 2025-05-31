<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Guru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18pt;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16pt;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 12pt;
        }
        
        .content {
            margin-bottom: 20px;
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
            font-size: 11pt;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        
        .signature-space {
            height: 80px;
        }
        
        .info-table {
            width: 100%;
            border: none;
            margin-bottom: 20px;
        }
        
        .info-table td {
            border: none;
            padding: 3px 10px;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $sekolah->nama ?? 'NAMA SEKOLAH' }}</h1>
        <p>{{ $sekolah->alamat ?? 'Alamat Sekolah' }}</p>
        <p>Telp. {{ $sekolah->telepon ?? '-' }} | Email: {{ $sekolah->email ?? '-' }}</p>
    </div>
    
    <div class="content">
        <h2 style="text-align: center;">LAPORAN ABSENSI GURU</h2>
        <p style="text-align: center;">Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
        
        <table class="info-table" border="0">
            <tr>
                <td class="label">Nama Guru</td>
                <td>: {{ $guru->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td>: {{ $guru->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Mata Pelajaran</td>
                <td>: {{ implode(', ', $mapel) }}</td>
            </tr>
        </table>
        
        <table>
            <thead>                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Tanggal</th>
                    <th width="10%">Kelas</th>
                    <th width="15%">Mata Pelajaran</th>
                    <th width="20%">Materi</th>
                    <th width="8%">Hadir</th>
                    <th width="8%">Tidak Hadir</th>
                    <th width="22%">Ketidakhadiran Siswa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanAbsensi as $index => $laporan)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $laporan->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $laporan->jadwal->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $laporan->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                    <td>{{ $laporan->materi ?? '-' }}</td>
                    <td style="text-align: center;">{{ $laporan->hadir }}</td>
                    <td style="text-align: center;">{{ $laporan->total_siswa - $laporan->hadir }}</td>                    <td>
                        @php
                            $absenceData = App\Models\Absensi::where('jadwal_id', $laporan->jadwal_id)
                                ->whereDate('tanggal', $laporan->tanggal)
                                ->whereIn('status', ['alpha', 'izin', 'sakit'])
                                ->with('siswa')
                                ->get();
                            
                            // Group by status
                            $absentByStatus = [];
                            foreach ($absenceData as $absence) {
                                $status = strtoupper($absence->status);
                                if (!isset($absentByStatus[$status])) {
                                    $absentByStatus[$status] = [];
                                }
                                $absentByStatus[$status][] = $absence->siswa->nama_lengkap;
                            }
                            
                            // Format the output by status group
                            $formattedAbsences = [];
                            foreach ($absentByStatus as $status => $names) {
                                sort($names); // Sort names alphabetically
                                $formattedAbsences[] = implode(', ', $names) . ' (' . $status . ')';
                            }
                            
                            $absentStudents = implode('; ', $formattedAbsences);
                        @endphp
                        {{ $absentStudents ?: '-' }}
                    </td>
                </tr>
                @endforeach
                
                @if($laporanAbsensi->count() == 0)
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data absensi pada periode ini.</td>
                </tr>
                @endif
            </tbody>
        </table>
        
        <div class="signature">
            <p>{{ $sekolah->kota ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Guru Mata Pelajaran</p>
            
            <div class="signature-space"></div>
            
            <p><b>{{ $guru->nama_lengkap }}</b></p>
            <p>NIP: {{ $guru->nip ?? '-' }}</p>
        </div>
    </div>
</body>
</html>
