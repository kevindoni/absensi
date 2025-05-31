@extends('layouts.admin')

@section('title', 'Edit Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pelajaran</h1>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Pelajaran</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.pelajaran.update', $pelajaran->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nama_pelajaran">Nama Pelajaran <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_pelajaran" name="nama_pelajaran" 
                        value="{{ old('nama_pelajaran', $pelajaran->nama_pelajaran) }}" required>
                </div>

                <div class="form-group">
                    <label for="kode_pelajaran">Kode Pelajaran</label>
                    <input type="text" class="form-control" id="kode_pelajaran" name="kode_pelajaran" 
                        value="{{ old('kode_pelajaran', $pelajaran->kode_pelajaran) }}">
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $pelajaran->deskripsi) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.pelajaran.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
