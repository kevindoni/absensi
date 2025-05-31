@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Guru Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Guru::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Siswa Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Siswa::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orang Tua Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Orang Tua</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\OrangTua::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Kelas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Kelas::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Kehadiran (30 Hari Terakhir)</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Status Kehadiran (Hari Ini)</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Hadir
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Izin
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Sakit
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Alpa
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Records -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $recentAttendance = App\Models\Absensi::with(['siswa', 'siswa.kelas'])
                                                    ->orderBy('created_at', 'desc')
                                                    ->take(5)
                                                    ->get();
                                @endphp
                                
                                @forelse($recentAttendance as $item)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                    <td>{{ $item->siswa->nisn }}</td>
                                    <td>{{ $item->siswa->nama_lengkap }}</td>
                                    <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>                                    <td>
                                        @if($item->status == 'hadir')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif($item->status == 'terlambat')
                                            <span class="badge badge-success">Hadir (Terlambat)</span>
                                        @elseif($item->status == 'izin')
                                            <span class="badge badge-warning">Izin</span>
                                        @elseif($item->status == 'sakit')
                                            <span class="badge badge-info">Sakit</span>
                                        @else
                                            <span class="badge badge-danger">Alpa</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data kehadiran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Area Chart - Last 30 days attendance
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @php
                    $dates = [];
                    for ($i = 29; $i >= 0; $i--) {
                        $date = \Carbon\Carbon::now()->subDays($i)->format('d/m');
                        echo "'$date'" . ($i > 0 ? ', ' : '');
                    }
                    @endphp
                ],                  datasets: [{
                    label: 'Hadir',
                    data: [
                        @php
                        for ($i = 29; $i >= 0; $i--) {
                            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                            $count = App\Models\Absensi::where('tanggal', $date)->whereIn('status', ['hadir', 'terlambat'])->count();
                            echo $count . ($i > 0 ? ', ' : '');
                        }
                        @endphp
                    ],
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                    tension: 0.3
                },
                {
                    label: 'Izin',
                    data: [
                        @php
                        for ($i = 29; $i >= 0; $i--) {
                            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                            $count = App\Models\Absensi::where('tanggal', $date)->where('status', 'izin')->count();
                            echo $count . ($i > 0 ? ', ' : '');
                        }
                        @endphp
                    ],
                    backgroundColor: 'rgba(246, 194, 62, 0.1)',
                    borderColor: 'rgba(246, 194, 62, 1)',
                    pointBackgroundColor: 'rgba(246, 194, 62, 1)',
                    tension: 0.3
                },
                {
                    label: 'Sakit',
                    data: [
                        @php
                        for ($i = 29; $i >= 0; $i--) {
                            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                            $count = App\Models\Absensi::where('tanggal', $date)->where('status', 'sakit')->count();
                            echo $count . ($i > 0 ? ', ' : '');
                        }
                        @endphp
                    ],
                    backgroundColor: 'rgba(54, 185, 204, 0.1)',
                    borderColor: 'rgba(54, 185, 204, 1)',
                    pointBackgroundColor: 'rgba(54, 185, 204, 1)',
                    tension: 0.3
                },
                {
                    label: 'Alpha',
                    data: [
                        @php
                        for ($i = 29; $i >= 0; $i--) {
                            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                            $count = App\Models\Absensi::where('tanggal', $date)->whereIn('status', ['alpa', 'alpha'])->count();
                            echo $count . ($i > 0 ? ', ' : '');
                        }
                        @endphp
                    ],
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    pointBackgroundColor: 'rgba(231, 74, 59, 1)',
                    tension: 0.3
                }]
            },            
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });        // Pie Chart - Today's attendance status
        @php        
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $hadir = App\Models\Absensi::where('tanggal', $today)->whereIn('status', ['hadir', 'terlambat'])->count();
        $izin = App\Models\Absensi::where('tanggal', $today)->where('status', 'izin')->count();
        $sakit = App\Models\Absensi::where('tanggal', $today)->where('status', 'sakit')->count();
        $alpa = App\Models\Absensi::where('tanggal', $today)->whereIn('status', ['alpa', 'alpha'])->count();
        @endphp        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    data: [{{ $hadir }}, {{ $izin }}, {{ $sakit }}, {{ $alpa }}],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#36b9cc', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#dda20a', '#2c9faf', '#be2617'],
                }],
            },            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
            }
        });
    });
</script>
@endsection
