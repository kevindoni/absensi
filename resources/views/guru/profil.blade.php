@extends('layouts.guru')

@section('title', 'Profil Guru')

@section('styles')
<style>
    .profile-header {
        background-color: #f8f9fc;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .profile-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .profile-stats {
        margin: 20px 0;
    }
    .profile-stat-item {
        padding: 15px;
        border-radius: 5px;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .form-section {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Profil Guru</h1>

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
        <div class="col-xl-4">
            <!-- Profile picture card-->
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Profil</h6>
                </div>
                <div class="card-body text-center">
                    <img class="img-profile rounded-circle mb-3" src="{{ asset('sbadmin2/img/undraw_profile.svg') }}" width="160" height="160">
                    <h5 class="mb-0">{{ $guru->nama_lengkap }}</h5>
                    <div class="text-muted mb-2">{{ $guru->nip ?? 'NIP tidak tersedia' }}</div>
                    <div class="small font-italic text-muted mb-4">Foto profil JPG or PNG, maksimal 5 MB</div>
                    <form action="{{ route('guru.profil.updatePhoto') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="foto_profil" name="foto_profil" required>
                            <label class="custom-file-label" for="foto_profil">Pilih file...</label>
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-upload"></i> Upload Foto Baru
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Profile statistics -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Mengajar</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="card bg-primary text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs">KELAS DIAJAR</div>
                                    <div class="font-weight-bold">{{ $stats['kelas'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs">MATA PELAJARAN</div>
                                    <div class="font-weight-bold">{{ $stats['mapel'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-info text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs">TOTAL ABSENSI</div>
                                    <div class="font-weight-bold">{{ $stats['absensi'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-warning text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs">JADWAL PER MINGGU</div>
                                    <div class="font-weight-bold">{{ $stats['jadwal'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <!-- Account details card-->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Akun</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.profil.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}">
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nip">NIP</label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" value="{{ old('nip', $guru->nip) }}">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $guru->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="telepon">No. Telepon</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $guru->telepon) }}">
                                @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $guru->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
            
            <!-- Password card-->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.profil.updatePassword') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="password">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <button class="btn btn-primary" type="submit">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // File input customization
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endsection
