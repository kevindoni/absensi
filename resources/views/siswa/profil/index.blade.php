@extends('layouts.siswa')

@section('title', 'Profil Siswa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profil Saya</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <div class="avatar-wrapper mb-3">
                                <img src="{{ asset('img/default-avatar.png') }}" alt="Avatar" class="img-profile rounded-circle" style="width: 150px; height: 150px;">
                            </div>
                            <a href="{{ route('siswa.qrcode') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-qrcode fa-fw"></i> Lihat QR Code
                            </a>
                        </div>
                        <div class="col-md-9">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">NISN</th>
                                        <td>{{ auth()->user()->nisn }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <td>{{ auth()->user()->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kelas</th>
                                        <td>{{ auth()->user()->kelas->nama_kelas ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Kelamin</th>
                                        <td>{{ auth()->user()->jenis_kelamin }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Lahir</th>
                                        <td>{{ auth()->user()->tanggal_lahir ? \Carbon\Carbon::parse(auth()->user()->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ auth()->user()->alamat ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
