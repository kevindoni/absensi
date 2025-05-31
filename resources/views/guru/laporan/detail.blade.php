@extends('layouts.guru')

@section('title', 'Detail Laporan Absensi')

@section('styles')
<style>
    .info-card {
        margin-bottom: 20px;
    }
    .summary-box {
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        color: #fff;
    }
    .box-green {
        background-color: #1cc88a;
    }
    .box-blue {
        background-color: #36b9cc;
    }
    .box-yellow {
        background-color: #f6c23e;
    }
    .box-red {
        background-color: #e74a3b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Laporan Absensi</h1>
        <div>
            <a href="{{ route('guru.laporan.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
            <a href="{{ route('guru.laporan.show', ['laporan' => 'export', 'tanggal_mulai' => $date->format('Y-m-d'), 'tanggal_akhir' => $date->format('Y-m-d'), 'type' => 'pdf']) }}" 
               class="btn btn-sm btn-danger shadow-sm" target="_blank">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Date Info -->
    <div class="card shadow mb-4 info-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tanggal:</strong> {{ $date->translatedFormat('l, d F Y') }}</p>
                    <p><strong>Guru:</strong> {{ Auth::guard('guru')->user()->nama_lengkap }}</p>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="summary-box box-green text-center">                                <h4>{{ $absensiData->whereIn('status', ['hadir', 'terlambat'])->count() }}</h4>
                                <small>Hadir</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-box box-yellow text-center">
                                <h4>{{ $absensiData->where('status', 'terlambat')->count() }}</h4>
                                <small>Terlambat</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-box box-blue text-center">
                                <h4>{{ $absensiData->where('status', 'izin')->count() }}</h4>
                                <small>Izin</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-box box-yellow text-center">
                                <h4>{{ $absensiData->where('status', 'sakit')->count() }}</h4>
                                <small>Sakit</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-box box-red text-center">
                                <h4>{{ $absensiData->where('status', 'alpha')->count() }}</h4>
                                <small>Alpha</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Entries -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jurnal Mengajar</h6>
        </div>
        <div class="card-body">
            @if($jurnalData->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Materi</th>
                                <th>Kegiatan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jurnalData as $jurnal)
                            <tr>
                                <td>{{ $jurnal->jadwal->kelas->nama_kelas ?? '-' }}</td>
                                <td>{{ $jurnal->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                                <td>{{ $jurnal->materi }}</td>
                                <td>{{ $jurnal->kegiatan }}</td>
                                <td>{{ $jurnal->catatan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Tidak ada jurnal mengajar untuk tanggal ini.
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Absensi Siswa</h6>
        </div>
        <div class="card-body">
            @if($absensiData->isNotEmpty())
                @php
                    $jadwalGroups = $absensiData->groupBy('jadwal_id');
                @endphp
                
                @foreach($jadwalGroups as $jadwal_id => $siswaList)
                    @php
                        $jadwalInfo = $siswaList->first()->jadwal;
                    @endphp
                    
                    <h5 class="mb-3">
                        {{ $jadwalInfo->pelajaran->nama_pelajaran ?? 'Unknown' }} - 
                        {{ $jadwalInfo->kelas->nama_kelas ?? 'Unknown' }}
                        <span class="badge badge-primary">{{ $jadwalInfo->jam_mulai ?? '' }} - {{ $jadwalInfo->jam_selesai ?? '' }}</span>
                    </h5>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaList as $absensi)
                                <tr class="{{ $absensi->status == 'hadir' ? 'table-success' : ($absensi->status == 'alpha' ? 'table-danger' : ($absensi->status == 'sakit' ? 'table-warning' : ($absensi->status == 'terlambat' ? 'table-warning' : 'table-info'))) }}">
                                    <td>{{ $absensi->siswa->nisn ?? '-' }}</td>
                                    <td>{{ $absensi->siswa->nama_lengkap ?? 'Unknown' }}</td>
                                    <td>
                                        @if($absensi->status == 'hadir')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif($absensi->status == 'terlambat')
                                            <span class="badge badge-warning">Terlambat</span>
                                        @elseif($absensi->status == 'izin')
                                            <span class="badge badge-info">Izin</span>
                                        @elseif($absensi->status == 'sakit')
                                            <span class="badge badge-warning">Sakit</span>
                                        @else
                                            <span class="badge badge-danger">Alpha</span>
                                        @endif
                                    </td>
                                    <td>{{ $absensi->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    Tidak ada data absensi untuk tanggal ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
