@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Kehadiran</h1>
    </div>

    <!-- Report Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kelas_id">Kelas</label>
                            <select class="form-control" id="kelas_id" name="kelas_id">
                                <option value="">Semua Kelas</option>
                                @foreach(\App\Models\Kelas::all() as $kelas)
                                    <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai', date('Y-m-01')) }}">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter fa-sm"></i> Filter
                    </button>
                    <button type="submit" class="btn btn-success" name="export" value="excel">
                        <i class="fas fa-file-excel fa-sm"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger" name="export" value="pdf">
                        <i class="fas fa-file-pdf fa-sm"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran</h6>
        </div>
        <div class="card-body">
            @if(isset($data) && count($data) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>                                    <td>{{ $item->siswa->nisn }}</td>
                                    <td>{{ $item->siswa->nama_lengkap }}</td>
                                    <td>{{ $item->siswa->kelas->nama_kelas }}</td>
                                    <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                    <td>                                          @if(strtolower($item->status) == 'hadir')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif(strtolower($item->status) == 'terlambat')
                                            <span class="badge badge-success">Hadir (Terlambat)</span>
                                        @elseif(strtolower($item->status) == 'izin')
                                            <span class="badge badge-warning">Izin</span>
                                        @elseif(strtolower($item->status) == 'sakit')
                                            <span class="badge badge-info">Sakit</span>
                                        @else
                                            <span class="badge badge-danger">Alpha</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(strtolower($item->status) == 'terlambat' && $item->minutes_late > 0)
                                            @php
                                                $minutes = abs($item->minutes_late);
                                                $hours = floor($minutes / 60);
                                                $remainingMinutes = $minutes % 60;
                                                
                                                if ($hours > 0 && $remainingMinutes > 0) {
                                                    $timeText = $hours . ' jam ' . $remainingMinutes . ' menit';
                                                } elseif ($hours > 0) {
                                                    $timeText = $hours . ' jam';
                                                } else {
                                                    $timeText = $remainingMinutes . ' menit';
                                                }
                                            @endphp
                                            Terlambat {{ $timeText }}
                                        @elseif(strtolower($item->status) == 'hadir')
                                            Hadir tepat waktu
                                        @elseif(strtolower($item->status) == 'alpha')
                                            Tidak hadir
                                        @else
                                            {{ ucfirst($item->status) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted mb-0">Tidak ada data kehadiran atau belum melakukan filter</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Report Summary -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan</h6>
                </div>
                <div class="card-body">
                    @if(isset($summary))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Total Kehadiran</th>
                                    <td>{{ $summary->total ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Hadir</th>
                                    <td>{{ $summary->hadir ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->hadir / $summary->total) * 100, 2) : 0 }}%)</td>
                                </tr>
                                <tr>
                                    <th>Izin</th>
                                    <td>{{ $summary->izin ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->izin / $summary->total) * 100, 2) : 0 }}%)</td>
                                </tr>                                <tr>
                                    <th>Sakit</th>
                                    <td>{{ $summary->sakit ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->sakit / $summary->total) * 100, 2) : 0 }}%)</td>
                                </tr>
                                <tr>
                                    <th>Alpha</th>
                                    <td>{{ $summary->alpa ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->alpa / $summary->total) * 100, 2) : 0 }}%)</td>
                                </tr>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted mb-0">Ringkasan akan muncul setelah filter diterapkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Kehadiran</h6>
                </div>
                <div class="card-body">
                    @if(isset($summary) && $summary->total > 0)
                        <canvas id="attendanceChart" width="400" height="300"></canvas>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted mb-0">Grafik akan muncul setelah filter diterapkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
        
        @if(isset($summary) && $summary->total > 0)
        // Chart
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{
                    data: [
                        {{ $summary->hadir ?? 0 }},
                        {{ $summary->izin ?? 0 }},
                        {{ $summary->sakit ?? 0 }},
                        {{ $summary->alpa ?? 0 }}
                    ],
                    backgroundColor: [
                        '#1cc88a',  // success
                        '#f6c23e',  // warning
                        '#36b9cc',  // info
                        '#e74a3b',  // danger
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
