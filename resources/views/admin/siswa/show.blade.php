@extends('layouts.admin')

@section('title', 'Detail Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Siswa</h1>
        <a href="{{ route('admin.siswa.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Siswa</h6>
            <div>
                <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">NISN</th>
                        <td>{{ $siswa->nisn }}</td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td>{{ $siswa->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td>{{ $siswa->kelas->nama_kelas ?? 'Belum diatur' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Lahir</th>
                        <td>{{ $siswa->tanggal_lahir ? date('d F Y', strtotime($siswa->tanggal_lahir)) : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $siswa->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Terdaftar Sejak</th>
                        <td>{{ date('d F Y', strtotime($siswa->created_at)) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>    <!-- Attendance Statistics -->
    <div class="row mb-4">
        <!-- Hadir Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendanceStats['hadir'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Izin Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Izin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendanceStats['izin'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sakit Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sakit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendanceStats['sakit'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heartbeat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alpha Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendanceStats['alpha'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>        </div>    </div>

    <!-- Percentage Chart Row -->
    <div class="row mb-4">
        <!-- Percentage Line Chart -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Persentase Kehadiran Bulanan</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Export Options:</div>
                            <a class="dropdown-item" href="#" id="exportLineChartPNG">
                                <i class="fas fa-file-image fa-sm fa-fw mr-2 text-gray-400"></i>
                                Export as PNG
                            </a>
                            <a class="dropdown-item" href="#" id="exportLineChartPDF">
                                <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>
                                Export as PDF
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-line">
                        <canvas id="attendanceLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

      <!-- Attendance Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Kehadiran</h6>
            <div>
                <button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="fas fa-filter fa-sm"></i> Filter
                </button>
            </div>
        </div>
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card-body bg-light">
                <form action="{{ route('admin.siswa.show', $siswa->id) }}" method="GET" class="form-inline">
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="dateFrom" class="mr-2">Dari Tanggal</label>
                        <input type="date" class="form-control form-control-sm" id="dateFrom" name="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="dateTo" class="mr-2">Sampai Tanggal</label>
                        <input type="date" class="form-control form-control-sm" id="dateTo" name="to_date" value="{{ request('to_date') }}">
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="statusFilter" class="mr-2">Status</label>
                        <select class="form-control form-control-sm" id="statusFilter" name="status">
                            <option value="">-- Semua Status --</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 btn-sm">Apply Filter</button>
                    <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="btn btn-secondary mb-2 btn-sm ml-2">Clear</a>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($attendanceHistory->isEmpty())
                <p class="mb-0 text-center">Belum ada riwayat kehadiran untuk siswa ini</p>
                
                @if(session('debug_info'))
                <div class="alert alert-info mt-3">
                    <h5>Debug Info:</h5>
                    <p>Has Records: {{ session('debug_info.has_records') ? 'Yes' : 'No' }}</p>
                    <p>Record Count: {{ session('debug_info.record_count') }}</p>
                    
                    @if(session('debug_info.record_count') > 0)
                        <h6>Records:</h6>
                        <ul>
                            @foreach(session('debug_info.records') as $record)
                                <li>ID: {{ $record['id'] }} | Status: {{ $record['status'] }} | Date: {{ $record['date'] }} | AbsensiID: {{ $record['absensi_id'] }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @endif
                
                @if(session('monthly_debug'))
                <div class="alert alert-warning mt-3">
                    <h5>Monthly Chart Debug:</h5>
                    <p>Start Date: {{ session('monthly_debug.start_date') }}</p>
                    <p>End Date: {{ session('monthly_debug.end_date') }}</p>
                    <p>Record Count: {{ session('monthly_debug.record_count') }}</p>
                    
                    @if(session('monthly_debug.record_count') > 0)
                        <h6>Dates:</h6>
                        <ul>
                            @foreach(session('monthly_debug.all_dates') as $date)
                                <li>{{ $date }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @endif
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Status</th>
                                <th>Waktu Scan</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            @foreach($attendanceHistory as $record)
                                <tr class="{{ $record->status == 'hadir' ? 'table-success' : ($record->status == 'izin' ? 'table-warning' : ($record->status == 'sakit' ? 'table-info' : 'table-danger')) }}">
                                    <td>{{ $record->tanggal->format('d-m-Y') }}</td>
                                    <td>{{ $record->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                                    <td>{{ $record->jadwal->guru->nama_lengkap ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $record->status == 'hadir' ? 'success' : ($record->status == 'izin' ? 'warning' : ($record->status == 'sakit' ? 'info' : 'danger')) }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td>{{ isset($record->created_at) ? $record->created_at->format('H:i:s') : '-' }}</td>
                                    <td>{{ $record->keterangan ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $attendanceHistory->links() }}
                </div>            
                @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .chart-pie canvas,
    .chart-bar canvas,
    .chart-line canvas {
        min-height: 300px;
    }
</style>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="{{ asset('sbadmin2/vendor/chart.js/Chart.min.js') }}"></script>
<!-- HTML2Canvas and jsPDF for exporting charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Preparing for Percentage Line Chart

// Line Chart for Attendance Percentage
var ctx3 = document.getElementById("attendanceLineChart");
var attendanceLineChart = new Chart(ctx3, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyAttendance['labels']) !!},
        datasets: [{
            label: "Persentase Kehadiran (%)",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: {!! json_encode($monthlyAttendance['percentages']) !!},
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: 'month'
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    min: 0,
                    max: 100,
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value) {
                        return value + "%";
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: true
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel + '%';
                }
            }
        }
    }
});

// Export chart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Function to export chart as PNG
    function exportChartAsPNG(chartId, fileName) {
        const canvas = document.getElementById(chartId);
        const link = document.createElement('a');
        link.download = fileName + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
    
    // Function to export chart as PDF
    function exportChartAsPDF(chartId, fileName) {
        const canvas = document.getElementById(chartId);
        const imgData = canvas.toDataURL('image/png');
        
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');
        
        // Calculate dimensions to fit the PDF
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth() - 20;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        
        // Add image to PDF
        doc.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight);
        
        // Save the PDF
        doc.save(fileName + '.pdf');
    }
      // Export functionality for pie and bar charts has been removed
    
    // Export line chart as PNG
    document.getElementById('exportLineChartPNG').addEventListener('click', function(e) {
        e.preventDefault();
        exportChartAsPNG('attendanceLineChart', 'attendance_percentage');
    });
    
    // Export line chart as PDF
    document.getElementById('exportLineChartPDF').addEventListener('click', function(e) {
        e.preventDefault();
        exportChartAsPDF('attendanceLineChart', 'attendance_percentage');
    });
});
</script>
@endsection
