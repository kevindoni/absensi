@extends('layouts.guru')

@section('title', 'Laporan Presensi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Presensi</h1>
        <a href="{{ route('guru.presensi.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    
    <!-- Filter Form -->
    <div class="card shadow mb-4 no-print">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="get" action="{{ route('guru.presensi.report') }}">
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="bulan">Bulan</label>
                        <select class="form-control" id="bulan" name="bulan">
                            @foreach($namaBulan as $no => $nama)
                                <option value="{{ $no }}" {{ $bulan == $no ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tahun">Tahun</label>
                        <select class="form-control" id="tahun" name="tahun">
                            @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Laporan Presensi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center no-print">
            <h6 class="m-0 font-weight-bold text-primary">Laporan Presensi - {{ $namaBulan[$bulan] }} {{ $tahun }}</h6>
            <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print fa-sm"></i> Cetak
            </button>
        </div>
        <div class="card-body">
            @if($presensi->count() > 0)
                <!-- Add print-only header -->
                <div class="d-none d-print-block text-center mb-4">
                    <h4>Rekap Presensi Mengajar</h4>
                    <p>Periode: {{ $namaBulan[$bulan] }} {{ $tahun }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presensi as $tanggal => $presensiList)
                                @foreach($presensiList as $index => $p)
                                    <tr>
                                        @if($index == 0)
                                            <td rowspan="{{ $presensiList->count() }}">{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</td>
                                            <td rowspan="{{ $presensiList->count() }}">{{ str_replace(
                                                ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                                                ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                                                \Carbon\Carbon::parse($tanggal)->format('l')
                                            ) }}</td>
                                        @endif
                                        <td>{{ $p->jadwal->kelas->nama_kelas ?? '-' }}</td>
                                        <td>{{ $p->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                                        <td>{{ $p->waktu_masuk ? $p->waktu_masuk->format('H:i') : '-' }}</td>
                                        <td>{{ $p->waktu_keluar ? $p->waktu_keluar->format('H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $p->status == 'hadir' ? 'success' : ($p->status == 'terlambat' ? 'danger' : 'danger') }} no-print">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                            <span class="d-none d-print-inline" style="{{ $p->status == 'hadir' ? 'color: #008000;' : 'color: #ff0000;' }}">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $p->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Statistik -->
                <div class="row mt-4 no-print">
                    <div class="col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Ringkasan Presensi</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            Total: {{ $presensi->flatten()->count() }} kali mengajar
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Keterlambatan</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $presensi->flatten()->where('status', 'terlambat')->count() }} kali
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

                <!-- Print-only stats -->
                <div class="d-none d-print-block mt-4">
                    <table class="table table-borderless">
                        <tr>
                            <td style="padding-left:0">
                                <strong>RINGKASAN PRESENSI</strong><br>
                                Total: {{ $presensi->flatten()->count() }} kali mengajar
                            </td>
                            <td style="padding-right:0">
                                <strong>KETERLAMBATAN</strong><br>
                                {{ $presensi->flatten()->where('status', 'terlambat')->count() }} kali
                            </td>
                        </tr>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> Tidak ada data presensi untuk bulan {{ $namaBulan[$bulan] }} {{ $tahun }}.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print, 
        .sidebar, 
        .navbar, 
        footer, 
        .btn, 
        .form-group {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            display: none !important;
        }
        
        .container-fluid {
            padding: 0 !important;
        }
        
        body {
            background-color: #fff !important;
        }
        
        .d-print-block {
            display: block !important;
        }
        
        @page {
            margin: 1cm;
        }
        
        .table {
            font-size: 12px;
        }
        
        .card-body {
            padding: 0 !important;
        }
        
        .print-stats {
            margin-top: 2cm;
            border-top: 1px solid #ddd;
            padding-top: 0.5cm;
        }
        
        .stats-table {
            width: 100%;
            margin-bottom: 0;
        }
        
        .stats-label {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .stats-value {
            font-size: 16px;
            margin: 0;
        }
        
        .fa, .fas {
            display: none !important;
        }
        
        .card-counter {
            border: none !important;
            box-shadow: none !important;
        }

        .no-print {
            display: none !important;
        }
        
        .d-print-inline {
            display: inline !important;
        }
    }
</style>
@endsection
