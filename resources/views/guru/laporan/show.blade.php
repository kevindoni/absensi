@extends('layouts.guru')

@section('title', 'Detail Laporan Absensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Laporan Absensi Siswa</h1>
        <a href="{{ route('guru.laporan.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Nama Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $siswa->nama_lengkap }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                NIS/NISN</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if (!empty($siswa->nis) && !empty($siswa->nisn))
                                    {{ $siswa->nis }} / {{ $siswa->nisn }}
                                @elseif (!empty($siswa->nis))
                                    {{ $siswa->nis }}
                                @elseif (!empty($siswa->nisn))
                                    {{ $siswa->nisn }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kelas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $siswa->kelas->nama_kelas ?? '-' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Hadir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['hadir'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
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
                                Terlambat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['terlambat'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sakit'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-thermometer fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['izin'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Alpha</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['alpha'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Persentase Kehadiran -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Persentase Kehadiran</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-1">
                                <div class="small">
                                    <span class="mr-2">
                                        <i class="fas fa-circle text-primary"></i> Hadir: {{ $stats['persentase'] }}%
                                    </span>
                                </div>
                            </div>
                            <div class="progress mb-4">
                                <div class="progress-bar" role="progressbar" style="width: {{ $stats['persentase'] }}%"
                                    aria-valuenow="{{ $stats['persentase'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Absensi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Absensi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $data->tanggal->format('d-m-Y') }}</td>
                            <td>
                                @switch($data->status)
                                    @case('hadir')
                                        <span class="badge badge-success">Hadir</span>
                                        @break
                                    @case('terlambat')
                                        <span class="badge badge-warning">Terlambat</span>
                                        @break
                                    @case('sakit')
                                        <span class="badge badge-warning">Sakit</span>
                                        @break
                                    @case('izin')
                                        <span class="badge badge-info">Izin</span>
                                        @break
                                    @case('alpha')
                                        <span class="badge badge-danger">Alpha</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $data->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ $data->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada riwayat absensi untuk siswa ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "ordering": false,
            "info": true,
            "searching": true,
            "lengthMenu": [10, 25, 50, 100],
            "language": {
                "zeroRecords": "Tidak ada data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(disaring dari _MAX_ data)",
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>
@endsection
