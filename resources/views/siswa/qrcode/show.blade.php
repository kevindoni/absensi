@extends('layouts.siswa')

@section('title', 'QR Code Presensi')

@section('styles')
<style>
    .qr-card {
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }
    
    .qr-card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 1.5rem;
        border-bottom: none;
    }
      .qr-container {
        padding: 25px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        display: inline-block;
        margin: 20px 0;
        border: 1px solid #e3e6f0;
    }
    
    .qr-container svg {
        width: 250px !important;
        height: 250px !important;
        transition: transform 0.3s ease;
    }
    
    .qr-container svg:hover {
        transform: scale(1.03);
    }
    
    .student-info {
        background: #f8f9fc;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.3rem;
    }
    
    .info-value {
        font-size: 1.1rem;
        color: #2e2f37;
    }
    
    .download-btn {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        padding: 10px 25px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(37, 108, 225, 0.3);
    }
    
    .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 108, 225, 0.4);
    }
    
    .watermark {
        position: absolute;
        opacity: 0.05;
        font-size: 8rem;
        font-weight: 900;
        color: #4e73df;
        z-index: 0;
        pointer-events: none;
    }
      @media (max-width: 576px) {
        .qr-container svg {
            width: 200px !important;
            height: 200px !important;
        }
        
        .watermark {
            font-size: 5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card qr-card">
                <div class="card-header qr-card-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-qrcode mr-2"></i> Kode Presensi Digital
                    </h3>
                </div>
                
                <div class="card-body text-center position-relative">
                    <div class="watermark">PRESENSI</div>
                      <div class="qr-container">
                        @if($siswa->qr_token)
                            {!! App\Http\Controllers\QrController::generateStandardQrCode($siswa, 250) !!}
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                QR Code belum dibuat. Silakan hubungi admin sekolah.
                            </div>
                        @endif
                    </div>
                    
                    <div class="student-info text-left">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value">{{ $siswa->nama_lengkap }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">NISN</div>
                                <div class="info-value">{{ $siswa->nisn }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Kelas</div>
                                <div class="info-value">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Jurusan</div>
                                <div class="info-value">{{ $siswa->kelas->jurusan->nama_jurusan ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('siswa.qrcode.download') }}" class="btn download-btn">
                            <i class="fas fa-download mr-2"></i> Unduh QR Code
                        </a>
                        
                        <div class="mt-3 text-muted small">
                            <i class="fas fa-info-circle mr-1"></i> Scan QR code ini untuk melakukan presensi
                        </div>
                    </div>
                </div>
                
                <div class="card-footer text-muted text-center small">
                    QR Code berlaku selama menjadi siswa di sekolah ini
                </div>
            </div>
            
            <div class="alert alert-info mt-4">
                <h5 class="alert-heading"><i class="fas fa-lightbulb mr-2"></i>Petunjuk Penggunaan</h5>
                <ol class="mb-0 pl-3">
                    <li>Tunjukkan QR code ini saat masuk kelas</li>
                    <li>Guru akan memindai kode untuk mencatat kehadiran</li>
                    <li>Simpan QR code dengan aman dan jangan bagikan ke orang lain</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection