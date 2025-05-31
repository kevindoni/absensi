@extends('layouts.guru')

@section('title', 'Rekam Absensi Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rekam Absensi Baru</h1>
        <a href="{{ route('guru.absensi.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Jadwal Hari Ini -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Hari Ini</h6>
                </div>
                <div class="card-body">
                    @if($todayJadwal->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Jam</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayJadwal as $jadwal)
                                        <tr>
                                            <td>{{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</td>
                                            <td>{{ $jadwal->kelas->nama_kelas }}</td>
                                            <td>{{ $jadwal->pelajaran->nama_pelajaran }}</td>
                                            <td>
                                                <a href="{{ route('guru.absensi.takeAttendance', $jadwal->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-clipboard-check fa-sm"></i> Ambil Absensi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">Tidak ada jadwal mengajar untuk hari ini.</div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Semua Jadwal -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Semua Jadwal Mengajar</h6>
                </div>
                <div class="card-body">
                    @if($jadwal->count() > 0)
                        <div class="accordion" id="accordionJadwal">
                            @php
                                $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                $jadwalByDay = $jadwal->groupBy('hari');
                            @endphp
                            
                            @foreach($jadwalByDay as $hari => $itemJadwal)
                                <div class="card">
                                    <div class="card-header" id="heading{{ $hari }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left {{ $hari != Carbon\Carbon::now()->dayOfWeekIso ? 'collapsed' : '' }}" 
                                                    type="button" data-toggle="collapse" 
                                                    data-target="#collapse{{ $hari }}" 
                                                    aria-expanded="{{ $hari == Carbon\Carbon::now()->dayOfWeekIso ? 'true' : 'false' }}" 
                                                    aria-controls="collapse{{ $hari }}">
                                                <strong>{{ $days[$hari] }}</strong>
                                            </button>
                                        </h2>
                                    </div>
                                    
                                    <div id="collapse{{ $hari }}" class="collapse {{ $hari == Carbon\Carbon::now()->dayOfWeekIso ? 'show' : '' }}" aria-labelledby="heading{{ $hari }}" data-parent="#accordionJadwal">
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Jam</th>
                                                            <th>Kelas</th>
                                                            <th>Mata Pelajaran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($itemJadwal->sortBy('jam_mulai') as $j)
                                                            <tr>
                                                                <td>{{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}</td>
                                                                <td>{{ $j->kelas->nama_kelas }}</td>
                                                                <td>{{ $j->pelajaran->nama_pelajaran }}</td>
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
                        <div class="alert alert-info">Belum ada jadwal mengajar yang terdaftar.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .accordion .card-header {
        padding: 0;
    }
    
    .accordion .btn-link {
        color: #4e73df;
        text-decoration: none;
    }
    
    .accordion .btn-link:hover {
        color: #2e59d9;
        text-decoration: none;
    }
    
    .accordion .btn-link.collapsed {
        color: #858796;
    }
</style>
@endsection
