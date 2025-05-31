@extends('layouts.admin')

@section('title', 'Detail Orang Tua')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Orang Tua</h1>
        <a href="{{ route('admin.orangtua.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Orang Tua</h6>
            <div>
                <a href="{{ route('admin.orangtua.edit', $orangtua->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit fa-sm"></i> Edit
                </a>
                <form action="{{ route('admin.orangtua.destroy', $orangtua->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data orang tua ini?')">
                        <i class="fas fa-trash fa-sm"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Nama Lengkap</th>
                        <td>{{ $orangtua->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th>Siswa</th>
                        <td>
                            @if($orangtua->siswa)
                                <a href="{{ route('admin.siswa.show', $orangtua->siswa->id) }}">
                                    {{ $orangtua->siswa->nisn }} - {{ $orangtua->siswa->nama_lengkap }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Hubungan</th>
                        <td>{{ $orangtua->hubungan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td>{{ $orangtua->no_telp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $orangtua->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Terdaftar Sejak</th>
                        <td>{{ date('d F Y', strtotime($orangtua->created_at)) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
