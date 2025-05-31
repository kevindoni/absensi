@extends('layouts.admin')

@section('title', 'Detail Guru')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Guru</h1>
        <a href="{{ route('admin.guru.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Guru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Nama Lengkap</th>
                        <td>{{ $guru->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td>{{ $guru->username }}</td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td>{{ $guru->nip ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $guru->email }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td>{{ $guru->no_telp ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $guru->alamat ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $guru->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <th>Terdaftar Sejak</th>
                        <td>{{ $guru->created_at->format('d F Y') }}</td>
                    </tr>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.guru.edit', $guru->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus guru ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
