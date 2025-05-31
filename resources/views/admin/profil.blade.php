@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
    </div>

    <div class="row">
        <!-- Left Column - Profile Picture -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Profil</h6>
                </div>
                <div class="card-body text-center">
                    <img class="img-profile rounded-circle mb-3" width="150px" height="150px"
                         src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                    <div class="small font-italic text-muted mb-2">JPG atau PNG tidak lebih dari 2 MB</div>
                    <button class="btn btn-primary btn-sm" type="button">
                        <i class="fas fa-upload fa-sm"></i> Ganti Foto
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column - Profile Details -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Profil</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-1"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update') }}" method="POST" class="user">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-user @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', auth()->user()->name) }}"
                                   placeholder="Masukkan nama lengkap">
                            @error('name')
                                <div class="invalid-feedback ml-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Email</label>
                            <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', auth()->user()->email) }}"
                                   placeholder="Masukkan alamat email">
                            @error('email')
                                <div class="invalid-feedback ml-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="text-gray-800 mb-3">Ganti Password</h5>

                        <div class="form-group">
                            <label class="font-weight-bold">Password Baru</label>
                            <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror" 
                                   name="password" placeholder="Masukkan password baru">
                            <small class="form-text text-muted ml-3">Kosongkan jika tidak ingin mengubah password</small>
                            @error('password')
                                <div class="invalid-feedback ml-3">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Konfirmasi Password</label>
                            <input type="password" class="form-control form-control-user" 
                                   name="password_confirmation" placeholder="Konfirmasi password baru">
                        </div>

                        <button type="submit" class="btn btn-primary btn-user px-5">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
