@extends('layouts.guru')

@section('title', 'Detail Izin Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Izin Siswa</h1>
        <a href="{{ route('guru.izin.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Izin -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Izin</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Tanggal</th>
                            <td>{{ $izin->tanggal->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Nama Siswa</th>
                            <td>{{ $izin->siswa->nama_lengkap ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>NISN</th>
                            <td>{{ $izin->siswa->nisn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>{{ $izin->siswa->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($izin->status == 'izin')
                                    <span class="badge badge-info">Izin</span>
                                @elseif($izin->status == 'sakit')
                                    <span class="badge badge-warning">Sakit</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $izin->keterangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Diinput Oleh</th>
                            <td>{{ $izin->guru->nama_lengkap ?? 'Admin' }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Input</th>
                            <td>{{ $izin->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Surat</h6>
                </div>
                <div class="card-body">
                    @if($izin->bukti_surat)
                        @php
                            $extension = pathinfo(storage_path('app/public/surat_izin/' . $izin->bukti_surat), PATHINFO_EXTENSION);
                        @endphp
                        
                        @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset('storage/surat_izin/' . $izin->bukti_surat) }}" class="img-fluid" alt="Bukti Surat">
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                                <p>Dokumen PDF</p>
                                <a href="{{ asset('storage/surat_izin/' . $izin->bukti_surat) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-eye"></i> Lihat Dokumen
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Tidak ada bukti surat yang dilampirkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
