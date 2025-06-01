<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Mingguan - {{ $guru->nama_lengkap }}</title>
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
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .guru-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .guru-info h3 {
            margin: 0 0 10px 0;
            color: #495057;
            font-size: 13px;
            font-weight: bold;
        }
        
        .guru-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .guru-info td {
            padding: 4px 8px;
            font-size: 11px;
        }
        
        .guru-info .label {
            font-weight: bold;
            width: 30%;
            color: #495057;
        }
        
        .week-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .week-info h3 {
            margin: 0 0 10px 0;
            color: #1976d2;
            font-size: 14px;
            font-weight: bold;
        }
        
        .week-info .dates {
            font-size: 12px;
            color: #1565c0;
            font-weight: bold;
        }
        
        .statistics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .schedule-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .schedule-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 10px;
            text-align: center;
            vertical-align: middle;
        }
        
        .schedule-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .schedule-table tbody tr:hover {
            background-color: #e3f2fd;
        }
        
        .day-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .no-schedule {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 6px;
        }
        
        .schedule-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .schedule-time {
            font-weight: bold;
            color: #495057;
            font-size: 11px;
        }
        
        .schedule-subject {
            color: #007bff;
            font-weight: bold;
            font-size: 11px;
            margin: 2px 0;
        }
        
        .schedule-class {
            color: #28a745;
            font-size: 10px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            font-size: 10px;
            color: #6c757d;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        
        .signature-box {
            text-align: center;
            padding: 15px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
            color: #495057;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 40px;
        }
        
        .signature-name {
            font-size: 10px;
            color: #6c757d;
        }
        
        @media print {
            .schedule-table {
                box-shadow: none;
            }
            
            .stat-card {
                box-shadow: none;
            }
            
            .schedule-item {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>Sistem Manajemen Absensi</h1>
        <h2>SMA Negeri 1 Contoh</h2>
        <div class="subtitle">
            Jl. Pendidikan No. 123, Kota Contoh, Provinsi Contoh<br>
            Telp: (021) 123-4567 | Email: info@sman1contoh.sch.id
        </div>
    </div>

    <div class="document-title">
        Jadwal Mengajar Mingguan
    </div>

    <div class="guru-info">
        <h3>Informasi Guru</h3>
        <table>
            <tr>
                <td class="label">Nama Lengkap:</td>
                <td>{{ $guru->nama_lengkap }}</td>
                <td class="label">NIP:</td>
                <td>{{ $guru->nip }}</td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td>{{ $guru->email }}</td>
                <td class="label">No. Telepon:</td>
                <td>{{ $guru->no_telepon ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="week-info">
        <h3>Periode Mingguan</h3>
        <div class="dates">
            {{ $startOfWeek->format('d F Y') }} - {{ $endOfWeek->format('d F Y') }}
        </div>
    </div>

    <div class="statistics">
        <div class="stat-card">
            <div class="stat-number">{{ $statistics['total_schedules'] }}</div>
            <div class="stat-label">Total Jadwal</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statistics['total_classes'] }}</div>
            <div class="stat-label">Kelas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statistics['total_subjects'] }}</div>
            <div class="stat-label">Mata Pelajaran</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statistics['weekly_hours'] }}</div>
            <div class="stat-label">Jam Mengajar</div>
        </div>
    </div>

    @if($jadwalPerHari->isEmpty())
        <div class="no-schedule">
            <h3>Tidak Ada Jadwal</h3>
            <p>Tidak ada jadwal mengajar untuk periode ini.</p>
        </div>
    @else
        @for($day = 1; $day <= 7; $day++)
            @php
                $currentDate = $startOfWeek->copy()->addDays($day - 1);
                $daySchedules = $jadwalPerHari->get($day, collect());
            @endphp
            
            <div class="day-header">
                {{ $hariMapping[$day] ?? 'Hari ' . $day }} - {{ $currentDate->format('d F Y') }}
            </div>

            @if($daySchedules->isEmpty())
                <div class="no-schedule">
                    <p>Tidak ada jadwal mengajar</p>
                </div>
            @else
                @foreach($daySchedules as $jadwal)
                    <div class="schedule-item">
                        <div class="schedule-time">
                            {{ Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                            {{ Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            (Jam ke-{{ $jadwal->jam_ke }})
                        </div>
                        <div class="schedule-subject">
                            {{ $jadwal->pelajaran->nama_pelajaran }}
                        </div>
                        <div class="schedule-class">
                            Kelas: {{ $jadwal->kelas->nama_kelas }}
                        </div>
                    </div>
                @endforeach
            @endif
        @endfor
    @endif

    <div class="footer">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <strong>Statistik Mingguan:</strong><br>
                • Total Hari Aktif: {{ $statistics['active_days'] }} hari<br>
                • Total Jam Mengajar: {{ $statistics['weekly_hours'] }} jam<br>
                • Mata Pelajaran: {{ $statistics['subjects_list']->implode(', ') }}<br>
                • Kelas yang Diajar: {{ $statistics['classes_list']->implode(', ') }}
            </div>
            <div style="text-align: right;">
                <strong>Dicetak pada:</strong><br>
                {{ Carbon\Carbon::now()->format('d F Y, H:i') }} WIB<br>
                <em>Sistem Manajemen Absensi</em>
            </div>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Mengetahui,<br>Kepala Sekolah</div>
            <div class="signature-line"></div>
            <div class="signature-name">
                ________________________<br>
                NIP. ________________
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-title">{{ Carbon\Carbon::now()->format('d F Y') }}<br>Guru Mata Pelajaran</div>
            <div class="signature-line"></div>
            <div class="signature-name">
                {{ $guru->nama_lengkap }}<br>
                NIP. {{ $guru->nip }}
            </div>
        </div>
    </div>
</body>
</html>
