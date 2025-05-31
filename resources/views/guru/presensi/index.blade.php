@extends('layouts.guru')

@section('title', 'Absensi Mengajar')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi Mengajar</h1>
        <a href="{{ route('guru.presensi.report') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Laporan Absensi
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Jadwal Mengajar Hari Ini -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Jadwal Mengajar Hari Ini - {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</h6>
        </div>
        <div class="card-body">
            @if($jadwalHariIni->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalHariIni as $jadwal)
                            <tr>
                                <td>{{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</td>
                                <td>{{ $jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                                <td>{{ $jadwal->kelas->nama_kelas ?? '-' }}</td>
                                <td>
                                    @php
                                        $status = 'Belum Absen';
                                        $statusClass = 'secondary';
                                        
                                        if(isset($presensiHariIni[$jadwal->id])) {
                                            $presensi = $presensiHariIni[$jadwal->id];
                                            if($presensi->waktu_keluar) {
                                                $status = 'Selesai Mengajar';
                                                $statusClass = 'success';
                                            } else {
                                                $status = 'Sedang Mengajar';
                                                $statusClass = 'primary';
                                            }
                                            
                                            if($presensi->status == 'terlambat') {
                                                $status .= ' (Terlambat)';
                                                $statusClass = 'warning';
                                            }
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ $status }}</span>
                                </td>
                                <td>
                                    @if(!isset($presensiHariIni[$jadwal->id]))
                                        <form action="{{ route('guru.presensi.checkIn') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                            <button type="submit" class="btn btn-sm btn-primary">Check-in</button>
                                        </form>
                                    @elseif(!$presensiHariIni[$jadwal->id]->waktu_keluar)
                                        <form action="{{ route('guru.presensi.checkOut') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="presensi_id" value="{{ $presensiHariIni[$jadwal->id]->id }}">
                                            <button type="submit" class="btn btn-sm btn-success">Check-out</button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>Selesai</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Anda tidak memiliki jadwal mengajar untuk hari ini.
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Presensi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Presensi (7 Hari Terakhir)</h6>
        </div>
        <div class="card-body">
            @if($riwayatPresensi->count() > 0)
                <div class="accordion" id="accordionRiwayat">
                    @foreach($riwayatPresensi as $tanggal => $presensiList)
                        <div class="card">
                            <div class="card-header" id="heading{{ \Str::slug($tanggal) }}">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse{{ \Str::slug($tanggal) }}" aria-expanded="{{ $tanggal == $today ? 'true' : 'false' }}" aria-controls="collapse{{ \Str::slug($tanggal) }}">
                                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                                        <span class="badge badge-info ml-2">{{ $presensiList->count() }} kelas</span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapse{{ \Str::slug($tanggal) }}" class="collapse {{ $tanggal == $today ? 'show' : '' }}" aria-labelledby="heading{{ \Str::slug($tanggal) }}" data-parent="#accordionRiwayat">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Mata Pelajaran</th>
                                                    <th>Kelas</th>
                                                    <th>Jam Masuk</th>
                                                    <th>Jam Keluar</th>
                                                    <th>Status</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($presensiList as $presensi)
                                                <tr>
                                                    <td>{{ $presensi->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                                                    <td>{{ $presensi->jadwal->kelas->nama_kelas ?? '-' }}</td>
                                                    <td>{{ $presensi->waktu_masuk ? $presensi->waktu_masuk->format('H:i') : '-' }}</td>
                                                    <td>{{ $presensi->waktu_keluar ? $presensi->waktu_keluar->format('H:i') : '-' }}</td>
                                                    <td>
                                                        @if($presensi->status == 'hadir')
                                                            <span class="badge badge-success">Hadir</span>
                                                        @elseif($presensi->status == 'terlambat')
                                                            <span class="badge badge-warning">Terlambat</span>
                                                        @elseif($presensi->status == 'izin')
                                                            <span class="badge badge-info">Izin</span>
                                                        @elseif($presensi->status == 'sakit')
                                                            <span class="badge badge-warning">Sakit</span>
                                                        @else
                                                            <span class="badge badge-danger">Tidak Hadir</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $presensi->keterangan ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Tidak ada riwayat presensi dalam 7 hari terakhir.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
