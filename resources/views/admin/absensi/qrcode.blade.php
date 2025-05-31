@extends('layouts.admin')

@section('title', 'QR Code Absensi Kelas')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">QR Code Absensi</h1>
        <a href="{{ route('admin.absensi.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">QR Code Absensi Kelas {{ $kelas->nama_kelas }}</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <h4>Absensi Kelas {{ $kelas->nama_kelas }}</h4>
                        <strong>Tanggal:</strong> {{ date('d F Y', strtotime($tanggal)) }}
                    </div>
                    
                    <div class="qr-container border p-4 d-inline-block mb-3">
                        {!! $qrcode !!}
                    </div>
                    
                    <div>
                        <p class="text-muted mb-3">Scan QR Code ini untuk melakukan absensi</p>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print fa-sm"></i> Cetak QR Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .card-header, .btn, footer, .text-muted {
        display: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-body {
        padding: 0 !important;
    }
}
</style>
@endsection
