@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Kelas</h1>
        <a href="{{ route('admin.kelas.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kelas
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif    <!-- DataTables -->    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Kelas - Tahun Ajaran {{ $activeYear->nama }}
                @if(isset($filterTingkat))
                    <span class="ml-2 badge badge-info">Menampilkan Kelas Tingkat {{ $filterTingkat }}</span>
                    <a href="{{ route('admin.kelas.index') }}" class="ml-2 btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i> Hapus Filter
                    </a>
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th>Wali Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $key => $k)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $k->nama_kelas }}</td>
                                <td>{{ $k->tingkat }}</td>
                                <td>
                                    @if($k->waliKelas)
                                        {{ $k->waliKelas->nama_lengkap }}
                                    @else
                                        <span class="badge badge-danger blink">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td>{{ $k->siswa->count() }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.kelas.show', $k->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.kelas.edit', $k->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data kelas</td>
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
