@extends('layouts.admin')

@section('title')
{{ $settings['school_name'] ?? config('app.name') }} - Migrasi Siswa
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Migrasi Siswa Antar Tahun Ajaran</h1>
        <a href="{{ route('admin.academic-year.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Migrasi Siswa</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.academic-year.migrate-students') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Dari:</h6>
                                <div class="form-group">
                                    <label for="source_year_id">Tahun Ajaran Asal <span class="text-danger">*</span></label>
                                    <select class="form-control" id="source_year_id" name="source_year_id" required>
                                        <option value="">Pilih Tahun Ajaran</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('source_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->nama }} {{ $year->is_active ? '(Aktif)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="kelas_id">Kelas Asal (Opsional)</label>
                                    <select class="form-control" id="kelas_id" name="kelas_id">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Biarkan kosong untuk memilih semua siswa dari tahun ajaran yang dipilih.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Ke:</h6>
                                <div class="form-group">
                                    <label for="target_year_id">Tahun Ajaran Tujuan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="target_year_id" name="target_year_id" required>
                                        <option value="">Pilih Tahun Ajaran</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('target_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->nama }} {{ $year->is_active ? '(Aktif)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="target_kelas_id">Kelas Tujuan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="target_kelas_id" name="target_kelas_id" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('target_kelas_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Informasi:</strong> Proses migrasi akan memindahkan data siswa dari tahun ajaran asal ke tahun ajaran tujuan dan mengubah kelas mereka sesuai dengan kelas tujuan yang dipilih.
                        </div>
                        
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin memigrasikan siswa? Tindakan ini tidak dapat dibatalkan.')">
                            <i class="fas fa-exchange-alt"></i> Migrasi Siswa
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Panduan Migrasi</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Pilih tahun ajaran asal siswa yang akan dimigrasikan.</li>
                        <li>Jika ingin memigrasikan hanya siswa dari kelas tertentu, pilih kelas asal.</li>
                        <li>Pilih tahun ajaran tujuan (tahun ajaran baru).</li>
                        <li>Pilih kelas tujuan untuk siswa tersebut.</li>
                        <li>Klik tombol "Migrasi Siswa" untuk melakukan proses migrasi.</li>
                    </ol>
                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong> Pastikan untuk memeriksa dengan teliti sebelum melakukan migrasi, karena tindakan ini akan mengubah data siswa dan tidak dapat dibatalkan secara otomatis.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
