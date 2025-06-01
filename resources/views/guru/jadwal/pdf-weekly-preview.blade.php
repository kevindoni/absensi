@extends('layouts.guru')

@section('title', 'Preview Jadwal Mingguan PDF')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-week text-primary"></i>
            Preview Jadwal Mingguan PDF
        </h1>
        <div class="btn-group">
            <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('guru.jadwal.export-weekly-pdf', request()->all()) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Week Selection -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-calendar-alt"></i> Pilih Periode Mingguan
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('guru.jadwal.preview-weekly-pdf') }}">
                        <div class="form-group">
                            <label for="week">Pilih Minggu:</label>
                            <input type="week" 
                                   class="form-control" 
                                   id="week" 
                                   name="week" 
                                   value="{{ request('week', $startOfWeek->format('Y-\WW')) }}">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Update Preview
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> Informasi Periode
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Periode:</strong><br>
                        {{ $startOfWeek->format('d F Y') }} - {{ $endOfWeek->format('d F Y') }}
                    </p>
                    <p class="mb-0">
                        <strong>Total Hari:</strong> 7 hari (Senin - Minggu)
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Jadwal
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistics['total_schedules'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Kelas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistics['total_classes'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Mata Pelajaran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistics['total_subjects'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Jam Mengajar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistics['weekly_hours'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-pdf"></i> Preview Jadwal Mingguan PDF
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Opsi PDF:</div>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.export-weekly-pdf', request()->all()) }}">
                        <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                        Download PDF
                    </a>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.preview-pdf') }}">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                        Preview Detail
                    </a>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.preview-compact-pdf') }}">
                        <i class="fas fa-th fa-sm fa-fw mr-2 text-gray-400"></i>
                        Preview Kompak
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="pdf-preview" style="border: 1px solid #e3e6f0; border-radius: 0.35rem; background-color: #f8f9fc; padding: 20px; min-height: 600px;">
                <!-- Letterhead Preview -->
                <div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 20px;">
                    <h3 style="margin: 0; font-weight: bold;">SISTEM MANAJEMEN ABSENSI</h3>
                    <h4 style="margin: 5px 0; font-weight: normal;">SMA Negeri 1 Contoh</h4>
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">
                        Jl. Pendidikan No. 123, Kota Contoh, Provinsi Contoh<br>
                        Telp: (021) 123-4567 | Email: info@sman1contoh.sch.id
                    </p>
                </div>

                <!-- Document Title -->
                <div style="text-align: center; margin: 20px 0; padding: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px;">
                    <h4 style="margin: 0; text-transform: uppercase; letter-spacing: 1px;">JADWAL MENGAJAR MINGGUAN</h4>
                </div>

                <!-- Guru Info -->
                <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 10px 0; color: #495057;">Informasi Guru</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama Lengkap:</strong> {{ $guru->nama_lengkap }}</p>
                            <p><strong>Email:</strong> {{ $guru->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>NIP:</strong> {{ $guru->nip }}</p>
                            <p><strong>No. Telepon:</strong> {{ $guru->no_telepon ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Week Info -->
                <div style="background: #e3f2fd; border: 1px solid #2196f3; border-radius: 6px; padding: 15px; margin-bottom: 20px; text-align: center;">
                    <h5 style="margin: 0 0 10px 0; color: #1976d2;">Periode Mingguan</h5>
                    <p style="font-size: 14px; color: #1565c0; font-weight: bold; margin: 0;">
                        {{ $startOfWeek->format('d F Y') }} - {{ $endOfWeek->format('d F Y') }}
                    </p>
                </div>

                <!-- Weekly Schedule -->
                @if($jadwalPerHari->isEmpty())
                    <div class="alert alert-warning text-center">
                        <h5>Tidak Ada Jadwal</h5>
                        <p>Tidak ada jadwal mengajar untuk periode ini.</p>
                    </div>
                @else
                    @for($day = 1; $day <= 7; $day++)
                        @php
                            $currentDate = $startOfWeek->copy()->addDays($day - 1);
                            $daySchedules = $jadwalPerHari->get($day, collect());
                        @endphp
                        
                        <div class="mb-3">
                            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; font-weight: bold; text-align: center; padding: 8px; border-radius: 6px;">
                                {{ $hariMapping[$day] ?? 'Hari ' . $day }} - {{ $currentDate->format('d F Y') }}
                            </div>

                            @if($daySchedules->isEmpty())
                                <div style="text-align: center; padding: 15px; color: #6c757d; font-style: italic; background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 6px; margin-top: 8px;">
                                    Tidak ada jadwal mengajar
                                </div>
                            @else
                                <div class="mt-2">
                                    @foreach($daySchedules as $jadwal)
                                        <div style="background: white; border: 1px solid #e9ecef; border-radius: 6px; padding: 10px; margin-bottom: 8px;">
                                            <div style="font-weight: bold; color: #495057;">
                                                {{ Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                                {{ Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                (Jam ke-{{ $jadwal->jam_ke }})
                                            </div>
                                            <div style="color: #007bff; font-weight: bold; margin: 2px 0;">
                                                {{ $jadwal->pelajaran->nama_pelajaran }}
                                            </div>
                                            <div style="color: #28a745; font-weight: bold;">
                                                Kelas: {{ $jadwal->kelas->nama_kelas }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endfor
                @endif

                <!-- Footer Info -->
                <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d;">
                    <div class="row">
                        <div class="col-md-8">
                            <strong>Statistik Mingguan:</strong><br>
                            • Total Hari Aktif: {{ $statistics['active_days'] }} hari<br>
                            • Total Jam Mengajar: {{ $statistics['weekly_hours'] }} jam<br>
                            • Mata Pelajaran: {{ $statistics['subjects_list']->implode(', ') }}<br>
                            • Kelas yang Diajar: {{ $statistics['classes_list']->implode(', ') }}
                        </div>
                        <div class="col-md-4 text-right">
                            <strong>Dicetak pada:</strong><br>
                            {{ Carbon\Carbon::now()->format('d F Y, H:i') }} WIB<br>
                            <em>Sistem Manajemen Absensi</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12 text-center">
            <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
            </a>
            <a href="{{ route('guru.jadwal.export-weekly-pdf', request()->all()) }}" class="btn btn-success mr-2">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <a href="{{ route('guru.jadwal.preview-pdf') }}" class="btn btn-info mr-2">
                <i class="fas fa-list"></i> Preview Detail
            </a>
            <a href="{{ route('guru.jadwal.preview-compact-pdf') }}" class="btn btn-warning">
                <i class="fas fa-th"></i> Preview Kompak
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set current week as default if no week is selected
    if (!$('#week').val()) {
        var today = new Date();
        var weekStart = new Date(today.setDate(today.getDate() - today.getDay() + 1));
        var year = weekStart.getFullYear();
        var week = getWeekNumber(weekStart);
        $('#week').val(year + '-W' + (week < 10 ? '0' + week : week));
    }
    
    function getWeekNumber(date) {
        var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        var dayNum = d.getUTCDay() || 7;
        d.setUTCDate(d.getUTCDate() + 4 - dayNum);
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
        return Math.ceil((((d - yearStart) / 86400000) + 1)/7);
    }
});
</script>
@endpush
