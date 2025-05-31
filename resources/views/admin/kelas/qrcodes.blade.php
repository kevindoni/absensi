@extends('layouts.admin')

@section('title', 'QR Codes Kelas')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">QR Codes Kelas {{ $kelas->nama_kelas }}</h1>        
        <div>
            <a href="{{ route('admin.kelas.qrcodes.print', $kelas->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2" target="_blank">
                <i class="fas fa-id-card fa-sm text-white-50"></i> Cetak Kartu ID
            </a>
            <a href="{{ route('admin.kelas.show', $kelas->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        @foreach($siswa as $s)
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center p-0">
                    <div class="qr-container">
                        <div class="qr-header">
                            @php
                                $logoPath = DB::table('settings')->where('key', 'logo_path')->value('value');
                                $schoolName = DB::table('settings')->where('key', 'school_name')->value('value');
                            @endphp
                            <img src="{{ asset($logoPath ?: 'sbadmin2/img/logo.png') }}" alt="Logo Sekolah" width="40" class="mb-2">
                            <h5>{{ $schoolName ?: config('app.name') }}</h5>
                        </div>                        <div class="qr-code">
                            @if($s->qr_token)
                                {!! App\Http\Controllers\QrController::generateStandardQrCode($s) !!}
                            @else
                                <div class="alert alert-warning">QR Code belum dibuat</div>
                            @endif
                        </div>
                        
                        <div class="qr-info">
                            <h5 class="mb-0 mt-2">{{ $s->nama_lengkap }}</h5>
                            <p class="text-muted">NISN: {{ $s->nisn }}</p>
                            <p class="text-muted">Kelas: {{ $kelas->nama_kelas }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
    .qr-container {
        padding: 1rem;
    }
    .qr-header {
        margin-bottom: 1rem;
    }    .qr-code {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 1rem 0;
        min-height: 150px;
    }
    .qr-code svg {
        width: 150px !important;
        height: 150px !important;
        border: 1px solid #e0e0e0;
        background: white;
        padding: 8px;
    }
    .qr-info {
        padding: 1rem;
        border-top: 1px solid #eee;
    }
    @media print {
        .btn {
            display: none !important;
        }
        .card {
            break-inside: avoid;
        }
    }
</style>
@endpush
