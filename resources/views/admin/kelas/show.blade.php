@extends('layouts.admin')

@section('title', 'Detail Kelas')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Kelas</h1>
        <a href="{{ route('admin.kelas.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Detail Kelas {{ $kelas->nama_kelas }}</h6>
            <div>
                <a href="{{ route('admin.kelas.edit', $kelas->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Nama Kelas</th>
                        <td>{{ $kelas->nama_kelas }}</td>
                    </tr>                    
                    <tr>
                        <th>Tingkat</th>
                        <td><a href="{{ route('admin.kelas.index', ['tingkat' => $kelas->tingkat]) }}" class="text-primary">Kelas {{ $kelas->tingkat }}</a></td>
                    </tr>
                    <tr>
                        <th>Wali Kelas</th>
                        <td>
                            @if($kelas->waliKelas)
                                {{ $kelas->waliKelas->nama_lengkap }}
                            @else
                                <span class="badge badge-danger blink">Belum ditentukan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jumlah Siswa</th>
                        <td>{{ $kelas->siswa->count() }} siswa</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Students Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa di Kelas Ini</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas->siswa as $key => $siswa)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $siswa->nisn }}</td>
                                <td>{{ $siswa->nama_lengkap }}</td>
                                <td>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada siswa di kelas ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>    </div>
</div>

<style>
@keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.blink {
    animation: blink 1.5s linear infinite;
    font-weight: bold;
}
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endsection
