@extends('layouts.guru')

@section('title', 'Jadwal Mingguan')

@section('styles')
<style>
    .weekly-calendar {
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .week-header {
        background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .week-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .day-column {
        border-right: 1px solid #e3e6f0;
        min-height: 500px;
    }
    
    .day-column:last-child {
        border-right: none;
    }
    
    .day-header {
        background: #f8f9fc;
        padding: 15px;
        text-align: center;
        font-weight: 600;
        color: #5a5c69;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .schedule-item {
        padding: 10px;
        margin: 8px;
        border-radius: 8px;
        border-left: 4px solid;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    
    .schedule-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .schedule-time {
        font-size: 0.85rem;
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 5px;
    }
    
    .schedule-subject {
        font-weight: 600;
        margin-bottom: 3px;
    }
    
    .schedule-class {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .no-schedule {
        text-align: center;
        color: #6c757d;
        padding: 20px;
        font-style: italic;
    }
    
    @media (max-width: 768px) {
        .day-column {
            border-right: none;
            border-bottom: 1px solid #e3e6f0;
            min-height: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-week mr-2"></i>Jadwal Mingguan
        </h1>
        <div>
            <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-calendar mr-1"></i> Kembali ke Kalender
            </a>
            <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Weekly Calendar -->
    <div class="weekly-calendar">
        <div class="week-header">
            <div class="week-navigation">
                <a href="{{ route('guru.jadwal.weekly', ['week' => $startOfWeek->copy()->subWeek()->format('Y-\WW')]) }}" 
                   class="btn btn-light btn-sm">
                    <i class="fas fa-chevron-left"></i> Minggu Sebelumnya
                </a>
                <h4 class="mb-0">
                    {{ $startOfWeek->locale('id')->translatedFormat('d F') }} - 
                    {{ $endOfWeek->locale('id')->translatedFormat('d F Y') }}
                </h4>
                <a href="{{ route('guru.jadwal.weekly', ['week' => $startOfWeek->copy()->addWeek()->format('Y-\WW')]) }}" 
                   class="btn btn-light btn-sm">
                    Minggu Selanjutnya <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
        
        <div class="row no-gutters">
            @foreach($hariMapping as $dayNumber => $dayName)
            <div class="col-md day-column">
                <div class="day-header">
                    {{ $dayName }}
                    <div class="small text-muted">
                        {{ $startOfWeek->copy()->addDays($dayNumber - 1)->locale('id')->translatedFormat('d M') }}
                    </div>
                </div>
                
                <div class="day-content">
                    @if(isset($jadwalPerHari[$dayNumber]) && $jadwalPerHari[$dayNumber]->count() > 0)
                        @foreach($jadwalPerHari[$dayNumber]->sortBy('jam_mulai') as $jadwal)
                        <div class="schedule-item" style="border-left-color: {{ ['#3788d8', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de'][$loop->index % 5] }}">
                            <div class="schedule-time">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - 
                                {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                            </div>
                            <div class="schedule-subject">
                                {{ $jadwal->pelajaran->nama_pelajaran }}
                            </div>
                            <div class="schedule-class">
                                <i class="fas fa-users mr-1"></i>{{ $jadwal->kelas->nama_kelas }}
                            </div>
                            @if($jadwal->ruangan)
                            <div class="schedule-class">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $jadwal->ruangan }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <div class="no-schedule">
                            <i class="fas fa-calendar-times mb-2"></i><br>
                            Tidak ada jadwal
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
