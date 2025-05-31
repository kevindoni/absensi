@extends('layouts.guru')

@section('title', 'Laporan Absensi')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d3e2;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .form-control:disabled, .form-control[readonly] {
        background-color: #f8f9fc;
        opacity: 1;
    }
    .export-btn {
        margin-left: 5px;
    }
    .table-responsive {
        min-height: 400px;
    }
    .filter-card {
        margin-bottom: 20px;
    }    .report-summary {
        margin-bottom: 20px;
    }
    
    /* Custom DataTable styling */
    .dataTables_length, .dataTables_filter {
        margin-bottom: 15px;
    }
    .dataTables_info, .dataTables_paginate {
        margin-top: 15px;
    }
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .page-link {
        color: #4e73df;
    }
    .page-link:hover {
        color: #2e59d9;
    }
    div.dataTables_wrapper div.dataTables_length select {
        width: 60px;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Kehadiran</h1>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4 filter-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.laporan.index') }}" method="GET" id="reportFilterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kelas">Kelas</label>
                            <select class="form-control select2" name="kelas_id" id="kelas">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pelajaran">Mata Pelajaran</label>
                            <select class="form-control select2" name="pelajaran_id" id="pelajaran">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach($pelajaranList as $pelajaran)
                                    <option value="{{ $pelajaran->id }}" {{ request('pelajaran_id') == $pelajaran->id ? 'selected' : '' }}>
                                        {{ $pelajaran->nama_pelajaran }} ({{ $pelajaran->kode_pelajaran }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="daterange">Rentang Tanggal</label>
                            <input type="text" class="form-control" id="daterange" name="daterange" 
                                   value="{{ request('daterange', date('Y-m-d', strtotime('-30 days')).' - '.date('Y-m-d')) }}" 
                                   readonly>
                            <input type="hidden" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai', date('Y-m-d', strtotime('-30 days'))) }}">
                            <input type="hidden" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status Kehadiran</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8 text-right" style="align-self: flex-end;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search fa-sm"></i> Filter
                        </button>
                        <button type="reset" class="btn btn-secondary" id="resetFilter">
                            <i class="fas fa-undo fa-sm"></i> Reset
                        </button>
                        <div class="btn-group export-btn">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export fa-sm"></i> Export
                            </button>
                            <div class="dropdown-menu">
                                <a href="{{ route('guru.laporan.export', ['type' => 'excel'] + request()->all()) }}" class="dropdown-item">Excel</a>
                                <a href="{{ route('guru.laporan.export', ['type' => 'pdf'] + request()->all()) }}" class="dropdown-item">PDF</a>
                                <a href="{{ route('guru.laporan.export', ['type' => 'print'] + request()->all()) }}" class="dropdown-item" target="_blank">Print</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Summary -->
    @if(count($attendanceData) > 0)
    <div class="row report-summary">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Hadir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['hadir'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Izin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['izin'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Sakit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['sakit'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-procedures fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Alpha</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['alpha'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- DataTables -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran</h6>
            <div class="text-right">
                <span class="badge badge-info">Periode: {{ date('d M Y', strtotime(request('tanggal_mulai', date('Y-m-d', strtotime('-30 days'))))) }} - {{ date('d M Y', strtotime(request('tanggal_akhir', date('Y-m-d')))) }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>                        @forelse($attendanceData as $item)                        <tr class="{{ strtolower($item->status) == 'hadir' || strtolower($item->status) == 'terlambat' ? 'table-success' : (strtolower($item->status) == 'alpha' ? 'table-danger' : (strtolower($item->status) == 'sakit' ? 'table-warning' : 'table-info')) }}">
                            <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                            <td>{{ $item->siswa->nisn ?? '-' }}</td>
                            <td>{{ $item->siswa->nama_lengkap ?? 'Unknown' }}</td>
                            <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $item->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                            <td><span class="badge badge-primary">{{ $item->jadwal->pelajaran->kode_pelajaran ?? '-' }}</span></td>
                            <td>
                                @if(strtolower($item->status) == 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif(strtolower($item->status) == 'terlambat')
                                    <span class="badge badge-success">Hadir (Terlambat)</span>
                                @elseif(strtolower($item->status) == 'izin')
                                    <span class="badge badge-info">Izin</span>
                                @elseif(strtolower($item->status) == 'sakit')
                                    <span class="badge badge-warning">Sakit</span>
                                @else
                                    <span class="badge badge-danger">Alpha</span>
                                @endif
                            </td>
                            <td>
                                @if(strtolower($item->status) == 'terlambat')
                                    {{ $item->formatted_minutes_late ? 'Terlambat '.$item->formatted_minutes_late : $item->keterangan }}
                                @else
                                    {{ $item->keterangan ?? '-' }}
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data kehadiran yang ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>            <!-- Server-side pagination is disabled in favor of DataTable pagination -->
            {{-- We're using DataTable's built-in pagination instead --}}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Pilih...",
            allowClear: true
        });
        
        // Initialize DateRangePicker
        $('#daterange').daterangepicker({
            startDate: moment($('#tanggal_mulai').val()),
            endDate: moment($('#tanggal_akhir').val()),
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: "Pilih",
                cancelLabel: "Batal",
                customRangeLabel: "Rentang Kustom",
                daysOfWeek: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            },
            ranges: {
               'Hari Ini': [moment(), moment()],
               'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
               '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
               'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
               'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function(start, end) {
            $('#tanggal_mulai').val(start.format('YYYY-MM-DD'));
            $('#tanggal_akhir').val(end.format('YYYY-MM-DD'));
        });
          // Reset button
        $('#resetFilter').on('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('guru.laporan.index') }}";
        });
        
        // Initialize DataTables
        $('#dataTable').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            ordering: true,
            info: true,
            searching: true,
            responsive: true,
            columnDefs: [
                {orderable: false, targets: [7]} // Disable sorting on keterangan column
            ],            language: {
                emptyTable: "Tidak ada data yang tersedia",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                search: "Cari:",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>
@endsection
