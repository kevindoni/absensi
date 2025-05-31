@extends('layouts.admin')

@section('title')
{{ $settings['school_name'] ?? config('app.name') }} - Tahun Ajaran
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tahun Ajaran</h1>
        <a href="{{ route('admin.academic-year.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Tahun Ajaran
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

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Tahun Ajaran</h6>
            <div>
                <a href="{{ route('admin.academic-year.migrate-form') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-exchange-alt"></i> Migrasi Siswa
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Tahun Ajaran</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th width="10%">Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academicYears as $index => $year)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $year->nama }}</td>
                            <td>{{ $year->tanggal_mulai->format('d-m-Y') }}</td>
                            <td>{{ $year->tanggal_selesai->format('d-m-Y') }}</td>
                            <td>
                                @if($year->is_active)
                                <span class="badge badge-success">Aktif</span>
                                @else
                                <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.academic-year.edit', $year->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if(!$year->is_active)
                                    <form action="{{ route('admin.academic-year.set-active', $year->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Aktifkan
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.academic-year.destroy', $year->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();

        // Delete confirmation
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')) {
                this.submit();
            }
        });
    });
</script>
@endsection
