@extends('layouts.admin')

@section('title')
{{ $settings['school_name'] ?? config('app.name') }} - Detail Tahun Ajaran
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Tahun Ajaran</h1>
        <a href="{{ route('admin.academic-year.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Tahun Ajaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nama Tahun Ajaran</th>
                            <td width="70%">: {{ $academicYear->nama }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>: {{ $academicYear->tanggal_mulai->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>: {{ $academicYear->tanggal_selesai->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: 
                                @if($academicYear->is_active)
                                <span class="badge badge-success">Aktif</span>
                                @else
                                <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>: {{ $academicYear->created_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Diperbarui</th>
                            <td>: {{ $academicYear->updated_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.academic-year.edit', $academicYear->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if(!$academicYear->is_active)
                <form action="{{ route('admin.academic-year.set-active', $academicYear->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Aktifkan
                    </button>
                </form>
                <form action="{{ route('admin.academic-year.destroy', $academicYear->id) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
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
