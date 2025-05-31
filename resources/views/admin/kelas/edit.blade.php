@extends('layouts.admin')

@section('title', 'Edit Kelas')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kelas</h1>
        <a href="{{ route('admin.kelas.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
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

    <!-- Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Kelas</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.kelas.update', $kelas->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_kelas">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required>
                            @error('nama_kelas')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tingkat">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-control @error('tingkat') is-invalid @enderror" id="tingkat" name="tingkat" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <optgroup label="SD (Sekolah Dasar)">
                                    <option value="1" {{ old('tingkat', $kelas->tingkat) == '1' ? 'selected' : '' }}>Kelas 1</option>
                                    <option value="2" {{ old('tingkat', $kelas->tingkat) == '2' ? 'selected' : '' }}>Kelas 2</option>
                                    <option value="3" {{ old('tingkat', $kelas->tingkat) == '3' ? 'selected' : '' }}>Kelas 3</option>
                                    <option value="4" {{ old('tingkat', $kelas->tingkat) == '4' ? 'selected' : '' }}>Kelas 4</option>
                                    <option value="5" {{ old('tingkat', $kelas->tingkat) == '5' ? 'selected' : '' }}>Kelas 5</option>
                                    <option value="6" {{ old('tingkat', $kelas->tingkat) == '6' ? 'selected' : '' }}>Kelas 6</option>
                                </optgroup>
                                <optgroup label="SMP (Sekolah Menengah Pertama)">
                                    <option value="7" {{ old('tingkat', $kelas->tingkat) == '7' ? 'selected' : '' }}>Kelas 7</option>
                                    <option value="8" {{ old('tingkat', $kelas->tingkat) == '8' ? 'selected' : '' }}>Kelas 8</option>
                                    <option value="9" {{ old('tingkat', $kelas->tingkat) == '9' ? 'selected' : '' }}>Kelas 9</option>
                                </optgroup>
                                <optgroup label="SMA (Sekolah Menengah Atas)">
                                    <option value="10" {{ old('tingkat', $kelas->tingkat) == '10' ? 'selected' : '' }}>Kelas 10</option>
                                    <option value="11" {{ old('tingkat', $kelas->tingkat) == '11' ? 'selected' : '' }}>Kelas 11</option>
                                    <option value="12" {{ old('tingkat', $kelas->tingkat) == '12' ? 'selected' : '' }}>Kelas 12</option>
                                </optgroup>
                            </select>
                            @error('tingkat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="wali_kelas_id">Wali Kelas</label>
                    <select class="form-control @error('wali_kelas_id') is-invalid @enderror" id="wali_kelas_id" name="wali_kelas_id">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}" {{ old('wali_kelas_id', $kelas->wali_kelas_id) == $g->id ? 'selected' : '' }}>{{ $g->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Opsional</small>
                    @error('wali_kelas_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
