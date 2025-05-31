@extends('layouts.guru')

@section('title', 'Detail Jurnal Mengajar')

@section('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 no-print">
        <h1 class="h3 mb-0 text-gray-800">Detail Jurnal Mengajar</h1>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-print fa-sm text-white-50"></i> Cetak
            </button>
            <a href="{{ route('guru.laporan.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Informasi Kelas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">JURNAL PEMBELAJARAN</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Hari/Tanggal</th>
                            <td>: {{ $jurnal->tanggal->format('l, d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>: {{ $jurnal->jadwal->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <td>: {{ $jurnal->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Jam Pelajaran</th>
                            <td>: {{ date('H:i', strtotime($jurnal->jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jurnal->jadwal->jam_selesai)) }}</td>
                        </tr>
                        <tr>
                            <th>Guru</th>
                            <td>: {{ $jurnal->guru->nama_lengkap ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pertemuan Ke</th>
                            <td>: {{ $pertemuanKe }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jurnal Mengajar Detail -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Materi dan Kegiatan Pembelajaran</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 20%">Materi</th>
                    <td>{{ $jurnal->materi }}</td>
                </tr>
                <tr>
                    <th>Kegiatan Pembelajaran</th>
                    <td style="white-space: pre-line">{{ $jurnal->kegiatan }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td style="white-space: pre-line">{{ $jurnal->catatan ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <!-- Data Kehadiran Siswa -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rekapitulasi Kehadiran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-3">
                    <div class="border rounded py-3 bg-light">
                        <h4 class="mb-0">{{ $totalSiswa }}</h4>
                        <p class="mb-0">Jumlah Siswa</p>
                    </div>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="border rounded py-3 bg-success text-white">
                        <h4 class="mb-0">{{ $totalHadir }}</h4>
                        <p class="mb-0">Hadir</p>
                    </div>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="border rounded py-3 bg-warning text-white">
                        <h4 class="mb-0">{{ $totalIzinSakit }}</h4>
                        <p class="mb-0">Izin/Sakit</p>
                    </div>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="border rounded py-3 bg-danger text-white">
                        <h4 class="mb-0">{{ $totalAlpha }}</h4>
                        <p class="mb-0">Alpha</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tanda Tangan -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center">
                    <p>Mengetahui,</p>
                    <p>Kepala Sekolah</p>
                    <br><br><br>
                    <p><b><u>{{ $sekolah->kepala_sekolah ?? '...........................' }}</u></b></p>
                    <p>NIP: {{ $sekolah->nip_kepala_sekolah ?? '........................' }}</p>
                </div>
                <div class="col-md-6 text-center">
                    <p>{{ $sekolah->kota ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}</p>
                    <p>Guru Mata Pelajaran</p>
                    <br><br><br>
                    <p><b><u>{{ $jurnal->guru->nama_lengkap ?? '-' }}</u></b></p>
                    <p>NIP: {{ $jurnal->guru->nip ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
