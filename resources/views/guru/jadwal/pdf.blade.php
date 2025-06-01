<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Mengajar - {{ $guru->nama_lengkap }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .letterhead {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .letterhead h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .letterhead h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        
        .letterhead .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .document-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .guru-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        
        .guru-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .guru-info td {
            padding: 4px 8px;
            vertical-align: top;
        }
        
        .guru-info .label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        
        .statistics {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .stat-row {
            display: table-row;
        }
        
        .stat-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 12px;
            border: 1px solid #dee2e6;
            background: #e9ecef;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            display: block;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6c757d;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        .day-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
            border: 1px solid #dee2e6;
        }
        
        .day-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .schedule-table th,
        .schedule-table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .schedule-table th {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            color: #495057;
        }
        
        .schedule-table tr:nth-child(even) {
            background: #fdfdfe;
        }
        
        .schedule-table tr:hover {
            background: #e3f2fd;
        }
        
        .subject-name {
            font-weight: bold;
            color: #495057;
        }
        
        .subject-code {
            font-size: 9px;
            color: #6c757d;
            font-style: italic;
        }
        
        .class-name {
            font-weight: 600;
            color: #28a745;
        }
        
        .time-slot {
            font-weight: 600;
            color: #dc3545;
        }
        
        .no-schedule {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 25px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .weekly-summary {
            margin-top: 20px;
            border: 1px solid #dee2e6;
            background: #f8f9fa;
            padding: 15px;
        }
        
        .weekly-summary h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #495057;
            text-transform: uppercase;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            padding: 5px;
            border: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
                        font-size: 9px;
            color: #6c757d;
            border-top: 2px solid #495057;
            padding-top: 10px;
        }
        
        .footer p {
            margin: 2px 0;
        }
        
        .signature-section {
            margin-top: 30px;
            text-align: right;
            page-break-inside: avoid;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            margin-left: 50mm;
        }
        
        .signature-city {
            font-size: 10px;
            margin-bottom: 30px;
        }
        
        .signature-name {
            font-size: 10px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 20px;
        }
        
        .signature-title {
            font-size: 9px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 48px;
            color: rgba(0,0,0,0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>{{ config('app.name', 'SMA NEGERI') }}</h1>
        <h2>SISTEM INFORMASI JADWAL MENGAJAR</h2>
        <div class="subtitle">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }} Semester {{ date('n') <= 6 ? 'Genap' : 'Ganjil' }}</div>
    </div>
    
    <div class="document-title">Jadwal Mengajar Guru</div>
    
    <div class="guru-info">
        <table>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>: {{ $guru->nama_lengkap }}</td>
                <td class="label" style="text-align: right;">NIP</td>
                <td style="text-align: right;">: {{ $guru->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>: {{ $guru->email ?? '-' }}</td>
                <td class="label" style="text-align: right;">No. Telepon</td>
                <td style="text-align: right;">: {{ $guru->no_telepon ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>: {{ $guru->alamat ?? '-' }}</td>
                <td class="label" style="text-align: right;">Tanggal Cetak</td>
                <td style="text-align: right;">: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="statistics">
        <div class="stat-row">
            <div class="stat-item">
                <span class="stat-number">{{ $jadwalMengajar->pluck('kelas_id')->unique()->count() }}</span>
                <span class="stat-label">Total Kelas</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $jadwalMengajar->pluck('pelajaran_id')->unique()->count() }}</span>
                <span class="stat-label">Mata Pelajaran</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $jadwalMengajar->count() }}</span>
                <span class="stat-label">Total Jadwal</span>
            </div>
            <div class="stat-item">
                @php
                    $totalHours = $jadwalMengajar->sum(function($jadwal) {
                        $start = \Carbon\Carbon::parse($jadwal->jam_mulai);
                        $end = \Carbon\Carbon::parse($jadwal->jam_selesai);
                        return $start->diffInHours($end);
                    });
                @endphp
                <span class="stat-number">{{ $totalHours }}</span>
                <span class="stat-label">Jam/Minggu</span>
            </div>
        </div>
    </div>
    
    @foreach($hariMapping as $dayNumber => $dayName)
        <div class="day-section">
            <div class="day-header">
                {{ $dayName }}
                @if(isset($jadwalPerHari[$dayNumber]))
                    ({{ $jadwalPerHari[$dayNumber]->count() }} Jadwal)
                @endif
            </div>
            
            @if(isset($jadwalPerHari[$dayNumber]) && $jadwalPerHari[$dayNumber]->count() > 0)
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th style="width: 12%">Jam Ke</th>
                            <th style="width: 18%">Waktu</th>
                            <th style="width: 30%">Mata Pelajaran</th>
                            <th style="width: 20%">Kelas</th>
                            <th style="width: 20%">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalPerHari[$dayNumber]->sortBy('jam_mulai') as $jadwal)
                        <tr>
                            <td style="text-align: center;">{{ $jadwal->jam_ke ?? '-' }}</td>
                            <td class="time-slot">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - 
                                {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                            </td>
                            <td>
                                <div class="subject-name">{{ $jadwal->pelajaran->nama_pelajaran }}</div>
                                @if($jadwal->pelajaran->kode_pelajaran)
                                    <div class="subject-code">{{ $jadwal->pelajaran->kode_pelajaran }}</div>
                                @endif
                            </td>
                            <td class="class-name">{{ $jadwal->kelas->nama_kelas }}</td>
                            <td>{{ $jadwal->ruangan ?? 'Belum ditentukan' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-schedule">
                    <strong>Tidak ada jadwal mengajar pada hari {{ $dayName }}</strong>
                </div>
            @endif
        </div>
    @endforeach
    
    <div class="weekly-summary">
        <h3>Ringkasan Mingguan</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell"><strong>Hari</strong></div>
                @foreach($hariMapping as $dayNumber => $dayName)
                    <div class="summary-cell"><strong>{{ substr($dayName, 0, 3) }}</strong></div>
                @endforeach
            </div>
            <div class="summary-row">
                <div class="summary-cell"><strong>Jadwal</strong></div>
                @foreach($hariMapping as $dayNumber => $dayName)
                    <div class="summary-cell">
                        {{ isset($jadwalPerHari[$dayNumber]) ? $jadwalPerHari[$dayNumber]->count() : 0 }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-city">{{ ucfirst(strtolower(config('app.city', 'Kota'))) }}, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</div>
            <div style="height: 40px;"></div>
            <div class="signature-name">{{ $guru->nama_lengkap }}</div>
            <div class="signature-title">NIP. {{ $guru->nip ?? '-' }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>{{ config('app.name', 'AbsensiPro') }}</strong> - Sistem Informasi Akademik</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i:s') }}</p>
        <p>Dokumen ini dibuat secara otomatis oleh sistem</p>
    </div>
</body>
</html>
