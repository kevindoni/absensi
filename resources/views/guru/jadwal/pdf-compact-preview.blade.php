@extends('layouts.guru')

@section('title', 'Preview PDF Kompak Jadwal')

@section('styles')
<style>
    .pdf-preview-compact {
        background: white;
        max-width: 297mm; /* A4 landscape width */
        margin: 20px auto;
        padding: 10mm;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow-x: auto;
    }
    
    .preview-actions {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .preview-actions h6 {
        margin-bottom: 10px;
        color: #5a5c69;
        font-weight: 600;
    }
    
    .header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    
    .header h1 {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .header .subtitle {
        font-size: 12px;
        color: #666;
        margin-top: 3px;
    }
    
    .guru-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 10px;
        background: #f8f9fa;
        padding: 8px;
        border-radius: 4px;
    }
    
    .guru-info-item {
        flex: 1;
        text-align: center;
    }
    
    .schedule-grid {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 8px;
    }
    
    .schedule-grid th,
    .schedule-grid td {
        border: 1px solid #333;
        padding: 4px;
        text-align: center;
        vertical-align: top;
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
        width: 40px;
        font-size: 7px;
    }
    
    .day-header {
        background: #4a5568;
        color: white;
        font-weight: bold;
        padding: 8px 4px;
    }
    
    .schedule-cell {
        height: 40px;
        position: relative;
        background: #f7fafc;
        min-width: 80px;
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
        line-height: 1.2;
    }
    
    .class-name {
        font-size: 6px;
        color: #4a5568;
        font-style: italic;
        line-height: 1.1;
    }
    
    .time-range {
        font-size: 6px;
        color: #718096;
        line-height: 1.1;
    }
    
    .summary-section {
        margin-top: 15px;
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }
    
    .summary-item {
        flex: 1;
        text-align: center;
        padding: 8px;
        border: 1px solid #333;
        background: #f1f5f9;
        font-size: 8px;
        border-radius: 4px;
    }
    
    .summary-header {
        background: #334155;
        color: white;
        font-weight: bold;
    }
    
    .footer {
        margin-top: 15px;
        text-align: center;
        font-size: 8px;
        color: #666;
        border-top: 1px solid #ccc;
        padding-top: 8px;
    }
    
    .no-class {
        color: #a0aec0;
        font-style: italic;
        font-size: 7px;
    }
    
    .landscape-note {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 15px;
        text-align: center;
        color: #1565c0;
    }
    
    @media print {
        .preview-actions, .landscape-note {
            display: none;
        }
        
        body {
            margin: 0;
        }
        
        .pdf-preview-compact {
            margin: 0;
            box-shadow: none;
            border-radius: 0;
            max-width: none;
        }
        
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    }
    
    @media (max-width: 768px) {
        .pdf-preview-compact {
            margin: 10px;
            padding: 5mm;
        }
        
        .preview-actions {
            position: relative;
            margin-bottom: 20px;
            right: auto;
            top: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Preview Actions -->
    <div class="preview-actions">
        <h6><i class="fas fa-tools mr-1"></i> Aksi</h6>
        <div class="btn-group-vertical w-100">
            <button onclick="window.print()" class="btn btn-primary btn-sm mb-2">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="{{ route('guru.jadwal.export-compact-pdf') }}" class="btn btn-success btn-sm mb-2">
                <i class="fas fa-download mr-1"></i> Download PDF
            </a>
            <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Landscape Note -->
    <div class="landscape-note">
        <i class="fas fa-info-circle mr-1"></i>
        <strong>Info:</strong> Format ini dirancang untuk orientasi landscape (horizontal). 
        Pastikan pengaturan printer Anda dalam mode landscape untuk hasil terbaik.
    </div>
    
    <!-- PDF Preview Content -->
    <div class="pdf-preview-compact">
        <div class="header">
            <h1>Jadwal Mengajar Mingguan - {{ $guru->nama_lengkap }}</h1>
            <div class="subtitle">{{ config('app.name', 'SMA NEGERI') }} | Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
        </div>
        
        <div class="guru-info">
            <div class="guru-info-item"><strong>NIP:</strong> {{ $guru->nip ?? '-' }}</div>
            <div class="guru-info-item"><strong>Email:</strong> {{ $guru->email ?? '-' }}</div>
            <div class="guru-info-item"><strong>Telepon:</strong> {{ $guru->no_telepon ?? '-' }}</div>
            <div class="guru-info-item"><strong>Dicetak:</strong> {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d/m/Y H:i') }}</div>
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
                            <div style="font-weight: bold;">{{ $jamKe }}</div>
                            <div style="font-size: 6px; margin-top: 2px;">{{ $jamKeMapping[$jamKe] ?? '' }}</div>
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
                                    @if($scheduleForSlot->ruangan)
                                        <div class="time-range">{{ $scheduleForSlot->ruangan }}</div>
                                    @endif
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
            <div class="summary-item summary-header">Total Kelas</div>
            <div class="summary-item summary-header">Mata Pelajaran</div>
            <div class="summary-item summary-header">Total Jadwal</div>
            <div class="summary-item summary-header">Jam/Minggu</div>
            <div class="summary-item summary-header">Hari Aktif</div>
        </div>
        
        <div class="summary-section" style="margin-top: 5px;">
            <div class="summary-item">{{ $statistics['total_classes'] }}</div>
            <div class="summary-item">{{ $statistics['total_subjects'] }}</div>
            <div class="summary-item">{{ $statistics['total_schedules'] }}</div>
            <div class="summary-item">{{ $statistics['weekly_hours'] }}</div>
            <div class="summary-item">{{ $statistics['active_days'] }}</div>
        </div>
        
        <div class="footer">
            <p><strong>Mata Pelajaran yang Diampu:</strong> {{ $statistics['subjects_list']->implode(', ') }}</p>
            <p><strong>Kelas yang Diajar:</strong> {{ $statistics['classes_list']->implode(', ') }}</p>
            <p>Dokumen ini dibuat secara otomatis pada {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i:s') }}</p>
        </div>
    </div>
</div>
@endsection
