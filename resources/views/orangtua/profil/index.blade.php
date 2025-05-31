@extends('layouts.orangtua')

@section('title', 'Profil Orangtua')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profil Saya</h6>
                </div>
                <div class="card-body">                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Nama Lengkap</th>
                                <td>{{ $orangtua->nama_lengkap }}</td>
                            </tr>
                            <tr>
                                <th>Hubungan</th>
                                <td>{{ $orangtua->hubungan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>No. Telepon</th>
                                <td>{{ $orangtua->no_telp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $orangtua->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <h6 class="mt-4 mb-3 font-weight-bold text-primary">Data Anak</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                </tr>
                            </thead>                            
                            <tbody>
                                @if($orangtua->siswa)
                                    <tr>
                                        <td>{{ $orangtua->siswa->nisn }}</td>
                                        <td>{{ $orangtua->siswa->nama_lengkap }}</td>
                                        <td>{{ $orangtua->siswa->kelas?->nama_kelas ?? '-' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data anak</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
