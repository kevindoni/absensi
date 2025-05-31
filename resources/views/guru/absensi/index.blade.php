@extends('layouts.guru')

@section('title', 'Absensi Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi Siswa</h1>
        <a href="{{ route('guru.laporan.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
            <i class="fas fa-file-alt fa-sm text-white-50"></i> Laporan Lengkap
        </a>
    </div>

    <!-- Jadwal Mengajar Hari Ini -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 text-primary d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Jadwal Mengajar Hari Ini - {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</h6>
        </div>
        <div class="card-body">
            @if($jadwalHariIni->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="jadwalTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="15%">Jam Pelajaran</th>
                                <th>Mata Pelajaran</th>
                                <th width="15%">Kelas</th>
                                <th width="15%">Status</th>
                                <th width="20%">Aksi</th>
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
                                        $status = 'Belum Dimulai';
                                        $statusClass = 'secondary';
                                        
                                        if(isset($absensiHariIni[$jadwal->id])) {
                                            $status = $absensiHariIni[$jadwal->id]->is_completed ? 'Selesai' : 'Berlangsung';
                                            $statusClass = $absensiHariIni[$jadwal->id]->is_completed ? 'success' : 'primary';
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ $status }}</span>
                                </td>
                                <td class="text-center">
                                    @if(!isset($absensiHariIni[$jadwal->id]))
                                        <a href="{{ route('guru.absensi.takeAttendance', $jadwal->id) }}" class="btn btn-sm btn-primary btn-block">
                                            <i class="fas fa-qrcode mr-1"></i> Mulai
                                        </a>
                                    @elseif(!$absensiHariIni[$jadwal->id]->is_completed)
                                        <div class="btn-group btn-block" role="group">
                                            <a href="{{ route('guru.absensi.takeAttendance', $jadwal->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-qrcode mr-1"></i> Lanjutkan
                                            </a>
                                            <a href="{{ route('guru.absensi.show', $absensiHariIni[$jadwal->id]->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('guru.absensi.show', $absensiHariIni[$jadwal->id]->id) }}" class="btn btn-sm btn-success btn-block">
                                            <i class="fas fa-eye mr-1"></i> Lihat Hasil
                                        </a>
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

    <!-- Riwayat Absensi Terakhir -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 text-primary d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Riwayat Absensi 7 Hari Terakhir</h6>
            <a href="{{ route('guru.absensi.riwayat') }}" class="btn btn-sm btn-light">
                <i class="fas fa-history mr-1"></i> Lihat Semua
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="riwayatTable" width="100%" cellspacing="0">                    <thead class="thead-light">
                        <tr>
                            <th width="15%">Tanggal</th>
                            <th>Mata Pelajaran</th>
                            <th width="15%">Kelas</th>
                            <th width="12%">Hadir</th>
                            <th width="12%">Tidak Hadir</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatAbsensi as $absensi)
                        @php
                            $jadwalId = $absensi->jadwal_id;
                            $tanggal = $absensi->tanggal;
                            
                            $stats = DB::table('absensis')
                                ->where('jadwal_id', $jadwalId)
                                ->whereDate('tanggal', $tanggal)
                                ->select(
                                    DB::raw('SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                                    DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as terlambat'),
                                    DB::raw('SUM(CASE WHEN status IN ("izin", "sakit", "alpha") THEN 1 ELSE 0 END) as tidak_hadir')
                                )
                                ->first();
                        @endphp
                        <tr>
                            <td>{{ $absensi->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $absensi->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                            <td>{{ $absensi->jadwal->kelas->nama_kelas ?? '-' }}</td>                            <td class="text-center"><span class="badge badge-success">{{ ($stats->hadir ?? 0) + ($stats->terlambat ?? 0) }}</span></td>
                            <td class="text-center"><span class="badge badge-danger">{{ $stats->tidak_hadir ?? 0 }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('guru.absensi.detail', ['jadwal' => $jadwalId, 'tanggal' => $tanggal]) }}" 
                                   class="btn btn-sm btn-info"
                                   title="Detail Absensi">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('guru.absensi.edit', $tanggal) }}" 
                                   class="btn btn-sm btn-warning"
                                   title="Edit Absensi">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>                       
                         @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada riwayat absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#jadwalTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "ordering": true,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [4] }
            ]
        });
        
        $('#riwayatTable').DataTable({
            "pageLength": 5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            "order": [[0, 'desc']],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            }
        });
    });
</script>
@endsection

@section('styles')
<style>
    .card-header.bg-primary {
        background-color: #4e73df !important;
    }
    .table thead.thead-light th {
        background-color: #f8f9fc;
    }
    .btn-block {
        display: block;
        width: 100%;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
</style>
@endsection