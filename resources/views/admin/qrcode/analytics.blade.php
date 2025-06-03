@extends('layouts.admin')

@section('title', 'Analytics QR Code')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s;
        border-left: 4px solid #4e73df;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .progress-wrapper {
        margin-bottom: 1rem;
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .analytics-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-pie mr-2"></i>Analytics QR Code
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.qrcode.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Kembali
            </a>
            <button class="btn btn-primary" onclick="refreshData()">
                <i class="fas fa-sync mr-1"></i>Refresh Data
            </button>
        </div>
    </div>    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Siswa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total QR Generated
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGenerated }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-qrcode fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                QR Code Valid
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $validQrCodes }}</div>
                            <div class="progress-wrapper">
                                <div class="progress-label">
                                    <span>Coverage Rate</span>
                                    <span>{{ $healthMetrics['qr_coverage'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $healthMetrics['qr_coverage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                QR Expired
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiredCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & QR Status by Class -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-2"></i>Status QR Code per Kelas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="classStatsTable">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Total Siswa</th>
                                    <th>QR Generated</th>
                                    <th>QR Valid</th>
                                    <th>QR Expired</th>
                                    <th>Progress</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classStats as $stat)
                                <tr>
                                    <td>
                                        <strong>{{ $stat['kelas_name'] }}</strong>
                                    </td>
                                    <td>{{ $stat['total_students'] }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $stat['qr_generated'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $stat['qr_valid'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $stat['qr_expired'] }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ $stat['completion_percentage'] }}%"
                                                 data-toggle="tooltip" 
                                                 title="{{ number_format($stat['completion_percentage'], 1) }}% Complete">
                                                {{ number_format($stat['completion_percentage'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.kelas.qrcodes', $stat['kelas_id']) }}" 
                                               class="btn btn-primary btn-sm" title="Lihat QR">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-warning btn-sm" 
                                                    onclick="regenerateClassQR({{ $stat['kelas_id'] }})" 
                                                    title="Regenerate QR">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-block mb-2" onclick="generateAllMissingQR()">
                            <i class="fas fa-magic mr-2"></i>Generate Semua QR yang Hilang
                        </button>
                        <button class="btn btn-warning btn-block mb-2" onclick="refreshExpiredQR()">
                            <i class="fas fa-refresh mr-2"></i>Refresh QR yang Expired
                        </button>
                        <button class="btn btn-info btn-block mb-2" onclick="downloadReport()">
                            <i class="fas fa-download mr-2"></i>Download Laporan
                        </button>
                        <a href="{{ route('admin.qrcode.settings') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-cogs mr-2"></i>Pengaturan QR
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Aktivitas Terkini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker">
                                <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $activity['title'] }}</h6>
                                <p class="timeline-text">{{ $activity['description'] }}</p>
                                <small class="text-muted">{{ $activity['time'] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Check -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-heartbeat mr-2"></i>System Health Check
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-indicator {{ $health['academic_year'] ? 'bg-success' : 'bg-danger' }}"></div>
                                <h6>Tahun Akademik</h6>
                                <p class="text-muted">{{ $health['academic_year'] ? 'Aktif' : 'Tidak Aktif' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-indicator {{ $health['qr_settings'] ? 'bg-success' : 'bg-warning' }}"></div>
                                <h6>QR Settings</h6>
                                <p class="text-muted">{{ $health['qr_settings'] ? 'Configured' : 'Needs Setup' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-indicator {{ $health['schedules'] ? 'bg-success' : 'bg-warning' }}"></div>
                                <h6>Jadwal Mengajar</h6>
                                <p class="text-muted">{{ $health['schedules'] ? 'Available' : 'Limited' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-indicator {{ $health['storage'] ? 'bg-success' : 'bg-danger' }}"></div>
                                <h6>Storage</h6>
                                <p class="text-muted">{{ $health['storage'] ? 'OK' : 'Issue' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize DataTable
    $('#classStatsTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
        }
    });
});

function refreshData() {
    location.reload();
}

function generateAllMissingQR() {
    if(confirm('Generate QR Code untuk semua siswa yang belum memiliki QR? Proses ini mungkin memakan waktu.')) {
        showLoading('Generating QR Codes...');
        
        $.ajax({
            url: '/admin/qrcode/generate-all-missing',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if(response.success) {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                        location.reload();
                    });
                }
            },
            error: function() {
                hideLoading();
                Swal.fire('Error!', 'Gagal generate QR Code', 'error');
            }
        });
    }
}

function refreshExpiredQR() {
    if(confirm('Refresh semua QR Code yang expired? QR Code lama akan diganti dengan yang baru.')) {
        showLoading('Refreshing expired QR Codes...');
        
        $.ajax({
            url: '/admin/qrcode/refresh-expired',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if(response.success) {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                        location.reload();
                    });
                }
            },
            error: function() {
                hideLoading();
                Swal.fire('Error!', 'Gagal refresh QR Code', 'error');
            }
        });
    }
}

function regenerateClassQR(kelasId) {
    if(confirm('Regenerate QR Code untuk seluruh siswa di kelas ini?')) {
        showLoading('Regenerating class QR Codes...');
        
        $.ajax({
            url: '/admin/qrcode/bulk-reset',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                kelas_id: kelasId,
                confirm: true
            },
            success: function(response) {
                hideLoading();
                Swal.fire('Berhasil!', 'QR Code kelas berhasil di-regenerate', 'success').then(() => {
                    location.reload();
                });
            },
            error: function() {
                hideLoading();
                Swal.fire('Error!', 'Gagal regenerate QR Code kelas', 'error');
            }
        });
    }
}

function downloadReport() {
    window.open('/admin/qrcode/download-report', '_blank');
}

function showLoading(message) {
    Swal.fire({
        title: 'Please wait...',
        text: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideLoading() {
    Swal.close();
}
</script>

<style>
.health-item {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fc;
    margin-bottom: 1rem;
}

.health-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin: 0 auto 10px;
}

.timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    margin-bottom: 1rem;
}

.timeline-marker {
    flex-shrink: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f8f9fc;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.timeline-content {
    flex-grow: 1;
}

.timeline-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.timeline-text {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
    color: #6c757d;
}
</style>
@endsection
