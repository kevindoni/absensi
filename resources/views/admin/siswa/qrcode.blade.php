@extends('layouts.admin')

@section('title', 'QR Code Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">QR Code Siswa</h1>
        <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">QR Code {{ $siswa->nama_lengkap }}</h6>
                </div>
                <div class="card-body text-center">
                    <div class="qr-container border p-4">
                        <div class="qr-header">
                            @php
                                $logoPath = DB::table('settings')->where('key', 'logo_path')->value('value');
                                $schoolName = DB::table('settings')->where('key', 'school_name')->value('value');
                            @endphp
                            <img src="{{ asset($logoPath ?: 'sbadmin2/img/logo.png') }}" alt="Logo Sekolah" width="60" class="mb-3">
                            <h5>{{ $schoolName ?: config('app.name') }}</h5>
                        </div>
                          <div class="qr-code mb-3">
                            {!! App\Http\Controllers\QrController::generateStandardQrCode($siswa) !!}
                        </div>
                        
                        <div class="qr-info">
                            <h5 class="mb-0">{{ $siswa->nama_lengkap }}</h5>
                            <p class="text-muted">NISN: {{ $siswa->nisn }}</p>
                            <p class="text-muted">Kelas: {{ $siswa->kelas->nama_kelas ?? 'Tidak ada kelas' }}</p>
                        </div>
                        
                        <div class="qr-footer">
                            <p class="small text-muted mb-0">Kartu ini bersifat pribadi dan hanya untuk digunakan oleh pemiliknya.</p>
                            <p class="small text-muted">Dibuat pada: {{ now()->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
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
/* QR Code display styles */
.qr-container {
    max-width: 400px;
    margin: 0 auto;
}

.qr-code svg {
    width: 150px !important;
    height: 150px !important;
    border: 1px solid #e0e0e0;
    background: white;
    padding: 8px;
}

@media print {
    .sidebar, .navbar, .card-header, .btn, footer {
        display: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .qr-container {
        padding: 20px !important;
        border: 1px solid #ddd !important;
        max-width: 350px;
        margin: auto;
    }
    
    .qr-code svg {
        width: 120px !important;
        height: 120px !important;
    }
    
    .qr-header {
        margin-bottom: 15px;
    }
    
    .qr-info {
        margin: 15px 0;
    }
    
    .qr-footer {
        margin-top: 15px;
        font-size: 0.8em;
    }
    
    @page {
        size: 88mm 125mm;
        margin: 0;
    }
}
</style>
@endsection