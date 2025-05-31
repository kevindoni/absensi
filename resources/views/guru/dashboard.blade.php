@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('styles')
<style>
    .chart-area, .chart-pie {
        height: 20rem;
    }
    .badge-presensi {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    .card-counter {
        border-left: 0.25rem solid !important;
    }
    .presensi-actions .btn {
        margin-right: 0.3rem;
        margin-bottom: 0.3rem;
    }
    @media (max-width: 768px) {
        .presensi-actions .btn {
            width: 100%;
            margin-right: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Guru</h1>
        <div class="d-flex align-items-center">
            @php
                $now = now();
                $currentTime = $now->format('H:i:s');
                
                // Get current/ongoing classes (could be multiple due to overlaps)
                $currentClasses = $jadwalHariIni
                    ->filter(function($jadwal) use ($currentTime) {
                        $jamMulai = Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
                        $jamSelesai = Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i:s');
                        return $currentTime >= $jamMulai && $currentTime <= $jamSelesai;
                    });
                
                // Get next classes that haven't been checked in
                $nextClasses = $jadwalHariIni
                    ->filter(function($jadwal) use ($currentTime) {
                        $jamMulai = Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
                        return $currentTime < $jamMulai &&
                            (!isset($presensiHariIni[$jadwal->id]) || 
                            !$presensiHariIni[$jadwal->id]->waktu_keluar);
                    })
                    ->sortBy('jam_mulai');
                
                // Get active class (currently being taught)
                $activeClass = $currentClasses
                    ->first(function($jadwal) use ($presensiHariIni) {
                        return isset($presensiHariIni[$jadwal->id]) && 
                               !$presensiHariIni[$jadwal->id]->waktu_keluar;
                    });
                
                // Get next class that needs check-in
                $nextClass = $currentClasses
                    ->first(function($jadwal) use ($presensiHariIni) {
                        return !isset($presensiHariIni[$jadwal->id]);
                    }) ?? $nextClasses->first();
            @endphp
            
            <div class="mr-3 d-none d-sm-block">
                <span class="text-gray-600">
                    <i class="fas fa-calendar-day mr-1"></i> {{ $now->translatedFormat('l, d F Y') }}
                </span>
            </div>
            
            @if($activeClass)
                <form action="{{ route('guru.presensi.checkOut') }}" method="POST">
                    @csrf
                    <input type="hidden" name="presensi_id" value="{{ $presensiHariIni[$activeClass->id]->id }}">
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="fas fa-sign-out-alt fa-fw"></i> Check Out {{ $activeClass->kelas->nama_kelas }}
                    </button>
                </form>
            @elseif($nextClass)
                <form action="{{ route('guru.presensi.checkIn') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $nextClass->id }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-sign-in-alt fa-fw"></i> Check In {{ $nextClass->kelas->nama_kelas }}
                        ({{ Carbon\Carbon::parse($nextClass->jam_mulai)->format('H:i') }})
                    </button>
                </form>
            @elseif($jadwalHariIni->isEmpty())
                <button class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-calendar-xmark fa-fw"></i> Tidak Ada Jadwal Hari Ini
                </button>
            @else
                <button class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-check-circle fa-fw"></i> Semua Kelas Selesai
                </button>
            @endif
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Kelas yang Diajar Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-counter border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Kelas yang Diajar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kelasYangDiajarCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Total kelas yang Anda ajar
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Siswa Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-counter border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSiswa) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Jumlah siswa di kelas Anda
                    </div>
                </div>
            </div>
        </div>

        <!-- Kehadiran Hari Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-counter border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kehadiran Hari Ini</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $persentaseKehadiran }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                            style="width: {{ $persentaseKehadiran }}%" 
                                            aria-valuenow="{{ $persentaseKehadiran }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Persentase kehadiran siswa hari ini
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Kehadiran Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-counter border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Status Kehadiran</div>
                            <div class="h5 mb-0">
                                <span class="badge badge-success badge-presensi">H: {{ $totalHadir }}</span>
                                <span class="badge badge-info badge-presensi">I: {{ $totalIzin }}</span>
                                <span class="badge badge-warning badge-presensi">S: {{ $totalSakit }}</span>
                                <span class="badge badge-danger badge-presensi">A: {{ $totalAlpha }}</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Distribusi status kehadiran
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Attendance Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Grafik Kehadiran Siswa (7 Hari Terakhir)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opsi:</div>
                            <a class="dropdown-item" href="{{ route('guru.absensi.riwayat') }}">Lihat Riwayat Lengkap</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                    <div class="mt-3 small text-muted text-center">
                        <i class="fas fa-info-circle"></i> Grafik menunjukkan persentase kehadiran siswa dalam 7 hari terakhir
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Status Kehadiran Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Hadir ({{ $totalHadir }})
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Izin ({{ $totalIzin }})
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Sakit ({{ $totalSakit }})
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Alpha ({{ $totalAlpha }})
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teaching Schedule -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Jadwal Mengajar Hari Ini</h6>
                    <div>
                        <span class="badge badge-light">
                            {{ $now->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($jadwalHariIni->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">Tidak ada jadwal mengajar hari ini</h5>
                            <p class="small text-muted">Anda tidak memiliki kelas yang harus diajar pada hari ini</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="12%">Jam</th>
                                        <th width="18%">Kelas</th>
                                        <th width="30%">Mata Pelajaran</th>
                                        <th width="15%">Status</th>
                                        <th width="25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalHariIni as $jadwal)
                                    <tr>
                                        <td>
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </td>
                                        <td>
                                            <strong>{{ $jadwal->kelas->nama_kelas ?? 'N/A' }}</strong>
                                            <div class="small text-muted">
                                                {{ $jadwal->kelas->jurusan->nama_jurusan ?? '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">
                                                {{ $jadwal->pelajaran->nama_pelajaran ?? 'N/A' }}
                                            </div>
                                            <div class="small">
                                                <span class="badge badge-secondary">
                                                    {{ $jadwal->pelajaran->kode_pelajaran ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if(isset($presensiHariIni[$jadwal->id]))
                                                @if($presensiHariIni[$jadwal->id]->waktu_keluar)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Selesai
                                                    </span>
                                                @else
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-chalkboard-teacher"></i> Mengajar
                                                    </span>
                                                @endif
                                            @elseif(isset($absensiHariIni[$jadwal->id]) && $absensiHariIni[$jadwal->id]->is_completed)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Absensi Selesai
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Belum Mulai
                                                </span>
                                            @endif
                                        </td>
                                        <td class="presensi-actions">
                                            @if(isset($presensiHariIni[$jadwal->id]) && !$presensiHariIni[$jadwal->id]->waktu_keluar)
                                                <form action="{{ route('guru.presensi.checkOut') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="presensi_id" value="{{ $presensiHariIni[$jadwal->id]->id }}">
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-sign-out-alt fa-fw"></i> Check Out
                                                    </button>
                                                </form>
                                            @elseif(!isset($presensiHariIni[$jadwal->id]))
                                                <form action="{{ route('guru.presensi.checkIn') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-sign-in-alt fa-fw"></i> Check In
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if(isset($absensiHariIni[$jadwal->id]) && $absensiHariIni[$jadwal->id]->is_completed)
                                                <a href="{{ route('guru.absensi.detail', ['jadwal' => $jadwal->id, 'tanggal' => $now->toDateString()]) }}" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye fa-fw"></i> Lihat Absensi
                                                </a>
                                            @else
                                                <a href="{{ route('guru.absensi.takeAttendance', $jadwal->id) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-clipboard-list fa-fw"></i> Absensi
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="fas fa-info-circle"></i> Total {{ $jadwalHariIni->count() }} jadwal mengajar hari ini
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
// Attendance Trend Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx1 = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: @json($last7Days),
            datasets: [{
                label: 'Persentase Kehadiran',
                data: @json($attendanceData),
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "#fff",
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "#fff",
                pointHitRadius: 10,
                pointBorderWidth: 2,
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + '% kehadiran';
                        }
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Status Distribution Chart
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [{{ $totalHadir }}, {{ $totalIzin }}, {{ $totalSakit }}, {{ $totalAlpha }}],
                backgroundColor: [
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)'
                ],
                borderColor: [
                    'rgba(28, 200, 138, 1)',
                    'rgba(54, 185, 204, 1)',
                    'rgba(246, 194, 62, 1)',
                    'rgba(231, 74, 59, 1)'
                ],
                borderWidth: 1,
                hoverOffset: 10
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%',
        }
    });
});
</script>
@endsection