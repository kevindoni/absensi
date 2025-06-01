<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Mengajar Kompak - {{ $guru->nama_lengkap }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header .subtitle {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        
        .guru-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 8px;
        }
        
        .guru-info-row {
            display: table-row;
        }
        
        .guru-info-cell {
            display: table-cell;
            padding: 2px 5px;
            width: 25%;
        }
        
        .schedule-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .schedule-grid th,
        .schedule-grid td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
            vertical-align: top;
            font-size: 8px;
        }
        
        .schedule-grid th {
            background: #333;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        
        .time-header {
            background: #666 !important;
            color: white;
            writing-mode: vertical-lr;
            text-orientation: mixed;
            width: 30px;
        }
        
        .day-header {
            background: #4a5568;
            color: white;
            font-weight: bold;
        }
        
        .schedule-cell {
            height: 35px;
            position: relative;
            background: #f7fafc;
        }
        
        .schedule-cell.has-class {
            background: #e6fffa;
            border: 2px solid #38b2ac;
        }
        
        .subject-name {
            font-weight: bold;
            font-size: 7px;
            color: #2d3748;
            margin-bottom: 1px;
        }
        
        .class-name {
            font-size: 6px;
            color: #4a5568;
            font-style: italic;
        }
        
        .time-range {
            font-size: 6px;
            color: #718096;
        }
        
        .summary-section {
            margin-top: 15px;
            display: table;
            width: 100%;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
            background: #f1f5f9;
            font-size: 8px;
        }
        
        .summary-header {
            background: #334155;
            color: white;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        
        .no-class {
            color: #a0aec0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Jadwal Mengajar Mingguan - {{ $guru->nama_lengkap }}</h1>
        <div class="subtitle">{{ config('app.name', 'SMA NEGERI') }} | Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    </div>
    
    <div class="guru-info">
        <div class="guru-info-row">
            <div class="guru-info-cell"><strong>NIP:</strong> {{ $guru->nip ?? '-' }}</div>
            <div class="guru-info-cell"><strong>Email:</strong> {{ $guru->email ?? '-' }}</div>
            <div class="guru-info-cell"><strong>Telepon:</strong> {{ $guru->no_telepon ?? '-' }}</div>
            <div class="guru-info-cell"><strong>Dicetak:</strong> {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d/m/Y H:i') }}</div>
        </div>
    </div>
    
    @php
        $timeSlots = [
            '07:00-07:45', '07:45-08:30', '08:30-09:15', '09:15-10:00',
            '10:15-11:00', '11:00-11:45', '11:45-12:30', '12:30-13:15',
            '13:15-14:00', '14:00-14:45', '14:45-15:30', '15:30-16:15'
        ];
        
        $jamKeMapping = [
            1 => '07:00-07:45', 2 => '07:45-08:30', 3 => '08:30-09:15', 4 => '09:15-10:00',
            5 => '10:15-11:00', 6 => '11:00-11:45', 7 => '11:45-12:30', 8 => '12:30-13:15',
            9 => '13:15-14:00', 10 => '14:00-14:45', 11 => '14:45-15:30', 12 => '15:30-16:15'
        ];
    @endphp
    
    <table class="schedule-grid">
        <thead>
            <tr>
                <th class="time-header">Jam</th>
                @foreach($hariMapping as $dayNumber => $dayName)
                    <th class="day-header">{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for($jamKe = 1; $jamKe <= 12; $jamKe++)
                <tr>
                    <td class="time-header">
                        <div>{{ $jamKe }}</div>
                        <div style="font-size: 6px;">{{ $jamKeMapping[$jamKe] ?? '' }}</div>
                    </td>
                    @foreach($hariMapping as $dayNumber => $dayName)
                        @php
                            $scheduleForSlot = null;
                            if (isset($jadwalPerHari[$dayNumber])) {
                                $scheduleForSlot = $jadwalPerHari[$dayNumber]->where('jam_ke', $jamKe)->first();
                            }
                        @endphp
                        <td class="schedule-cell {{ $scheduleForSlot ? 'has-class' : '' }}">
                            @if($scheduleForSlot)
                                <div class="subject-name">{{ $scheduleForSlot->pelajaran->nama_pelajaran }}</div>
                                <div class="class-name">{{ $scheduleForSlot->kelas->nama_kelas }}</div>
                                <div class="time-range">
                                    {{ date('H:i', strtotime($scheduleForSlot->jam_mulai)) }}-{{ date('H:i', strtotime($scheduleForSlot->jam_selesai)) }}
                                </div>
                            @else
                                <div class="no-class">-</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>
    
    <div class="summary-section">
        <div class="summary-row">
            <div class="summary-cell summary-header">Total Kelas</div>
            <div class="summary-cell summary-header">Mata Pelajaran</div>
            <div class="summary-cell summary-header">Total Jadwal</div>
            <div class="summary-cell summary-header">Jam/Minggu</div>
            <div class="summary-cell summary-header">Hari Aktif</div>
        </div>
        <div class="summary-row">
            <div class="summary-cell">{{ $statistics['total_classes'] }}</div>
            <div class="summary-cell">{{ $statistics['total_subjects'] }}</div>
            <div class="summary-cell">{{ $statistics['total_schedules'] }}</div>
            <div class="summary-cell">{{ $statistics['weekly_hours'] }}</div>
            <div class="summary-cell">{{ $statistics['active_days'] }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>Mata Pelajaran yang Diampu:</strong> {{ $statistics['subjects_list']->implode(', ') }}</p>
        <p><strong>Kelas yang Diajar:</strong> {{ $statistics['classes_list']->implode(', ') }}</p>
        <p>Dokumen ini dibuat secara otomatis pada {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i:s') }}</p>
    </div>
</body>
</html>
