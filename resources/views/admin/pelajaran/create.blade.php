@extends('layouts.admin')

@section('title', 'Tambah Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Pelajaran</h1>
        <a href="{{ route('admin.pelajaran.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Import Excel Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Import Data Pelajaran dari Excel</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pelajaran.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>File Excel</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx, .xls">
                        <label class="custom-file-label" for="file">Pilih file...</label>
                    </div>
                    <small class="form-text text-muted">
                        Download <a href="{{ route('admin.pelajaran.template') }}">template Excel</a> untuk format yang benar.
                    </small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-import"></i> Import Data
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Pelajaran</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.pelajaran.store') }}">
                @csrf

                <div class="form-group">
                    <label for="nama_pelajaran">Nama Pelajaran <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_pelajaran" name="nama_pelajaran" value="{{ old('nama_pelajaran') }}" required>
                </div>

                <div class="form-group">
                    <label for="kode_pelajaran">Kode Pelajaran</label>
                    <input type="text" class="form-control" id="kode_pelajaran" name="kode_pelajaran" value="{{ old('kode_pelajaran') }}">
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.pelajaran.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show filename in custom file input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
