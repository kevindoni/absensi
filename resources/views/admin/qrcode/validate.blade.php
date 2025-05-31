@extends('layouts.admin')

@section('title', 'Validasi QR Code')

@section('styles')
<style>
    .scanner-container {
        max-width: 600px;
        margin: 0 auto;
    }
    .qr-reader {
        border: 2px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
    }
    .scan-result {
        margin-top: 20px;
        padding: 15px;
        border-radius: 8px;
        display: none;
    }
    .scan-result.success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    .scan-result.error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    .scan-result.info {
        background-color: #cce5ff;
        border: 1px solid #99d6ff;
        color: #004085;
    }
    .scan-controls {
        margin: 20px 0;
        text-align: center;
    }
    .scan-controls button {
        margin: 5px;
    }
    .student-info {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    .student-info h5 {
        color: #4e73df;
        margin-bottom: 10px;
    }
    .info-row {
        margin-bottom: 8px;
    }
    .info-label {
        font-weight: bold;
        color: #5a5c69;
        display: inline-block;
        width: 120px;
    }
    .manual-input {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fc;
        border-radius: 8px;
    }
</style>
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode"></script>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-qrcode mr-2"></i>Validasi QR Code
        </h1>
        <a href="{{ route('admin.qrcode.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-camera mr-2"></i>Scanner QR Code
                    </h6>
                </div>
                <div class="card-body">
                    <div class="scanner-container">
                        <!-- QR Scanner -->
                        <div id="reader" class="qr-reader"></div>
                        
                        <!-- Scan Controls -->
                        <div class="scan-controls">
                            <button id="start-scan" class="btn btn-primary">
                                <i class="fas fa-play mr-1"></i>Mulai Scan
                            </button>
                            <button id="stop-scan" class="btn btn-secondary" style="display: none;">
                                <i class="fas fa-stop mr-1"></i>Berhenti Scan
                            </button>
                        </div>

                        <!-- Scan Result -->
                        <div id="scan-result" class="scan-result">
                            <div id="result-content"></div>
                        </div>                        <!-- Manual Input -->
                        <div class="manual-input">
                            <h6><i class="fas fa-keyboard mr-2"></i>Input Manual</h6>
                            <div class="input-group">
                                <input type="text" id="manual-qr-input" class="form-control" placeholder="Masukkan QR Code atau NISN siswa...">
                                <div class="input-group-append">
                                    <button id="manual-validate" class="btn btn-primary">Validasi</button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Anda dapat memasukkan QR Code lengkap atau NISN siswa (nomor induk siswa)
                            </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let html5QrCodeScanner = null;
let isScanning = false;

// Function to play beep sound
function playBeepSound() {
    try {
        const audio = new Audio('/sounds/beep.mp3');
        audio.volume = 0.5; // Set volume to 50%
        audio.play().catch(err => {
            console.log('Could not play beep sound:', err);
        });
    } catch (error) {
        console.log('Error creating audio:', error);
    }
}

$(document).ready(function() {
    // Start scan button
    $('#start-scan').click(function() {
        startQrScanner();
    });

    // Stop scan button
    $('#stop-scan').click(function() {
        stopQrScanner();
    });

    // Manual validate button
    $('#manual-validate').click(function() {
        const qrCode = $('#manual-qr-input').val().trim();
        if (qrCode) {
            validateQrCode(qrCode);
        } else {
            showResult('error', 'Silakan masukkan kode QR terlebih dahulu.');
        }
    });

    // Enter key support for manual input
    $('#manual-qr-input').keypress(function(e) {
        if (e.which === 13) {
            $('#manual-validate').click();
        }
    });
});

function startQrScanner() {
    if (html5QrCodeScanner) {
        stopQrScanner();
    }

    html5QrCodeScanner = new Html5Qrcode("reader");
    
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const cameraId = devices[0].id;
            
            html5QrCodeScanner.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanError
            ).then(() => {
                isScanning = true;
                $('#start-scan').hide();
                $('#stop-scan').show();
                showResult('info', 'Scanner aktif. Arahkan kamera ke QR code.');
            }).catch(err => {
                console.error('Error starting scanner:', err);
                showResult('error', 'Gagal memulai scanner. Pastikan kamera tersedia dan memberikan izin akses.');
            });
        } else {
            showResult('error', 'Tidak ada kamera yang terdeteksi.');
        }
    }).catch(err => {
        console.error('Error getting cameras:', err);
        showResult('error', 'Gagal mengakses kamera. Pastikan browser mendukung kamera dan memberikan izin akses.');
    });
}

function stopQrScanner() {
    if (html5QrCodeScanner && isScanning) {
        html5QrCodeScanner.stop().then(() => {
            html5QrCodeScanner = null;
            isScanning = false;
            $('#start-scan').show();
            $('#stop-scan').hide();
            showResult('info', 'Scanner dihentikan.');
        }).catch(err => {
            console.error('Error stopping scanner:', err);
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    console.log('QR Code detected:', decodedText);
    
    // Play beep sound when QR code is successfully scanned
    playBeepSound();
    
    // Stop scanner temporarily
    if (html5QrCodeScanner && isScanning) {
        html5QrCodeScanner.pause(true);
    }
    
    // Validate the QR code
    validateQrCode(decodedText);
}

function onScanError(errorMessage) {
    // Ignore common scanning errors that are part of normal operation
    if (!errorMessage.includes('No QR code found') && 
        !errorMessage.includes('QR code parse error')) {
        console.warn('QR Scan error:', errorMessage);
    }
}

async function validateQrCode(qrText) {
    try {
        showResult('info', '<i class="fas fa-spinner fa-spin mr-2"></i>Memvalidasi QR Code...');
        
        const response = await fetch(`/admin/qrcode/validate/${encodeURIComponent(qrText)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        
        if (data.valid) {
            let resultHtml = '<div class="student-info">';
            resultHtml += '<h5><i class="fas fa-check-circle text-success mr-2"></i>QR Code Valid!</h5>';
            
            if (data.siswa) {
                resultHtml += `
                    <div class="info-row">
                        <span class="info-label">Nama:</span>
                        <span>${data.siswa.nama_lengkap || data.siswa.nama || 'N/A'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NISN:</span>
                        <span>${data.siswa.nisn || 'N/A'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kelas:</span>
                        <span>${data.siswa.kelas || 'N/A'}</span>
                    </div>
                `;
            }
            
            if (data.jadwal) {
                resultHtml += `
                    <div class="info-row">
                        <span class="info-label">Mata Pelajaran:</span>
                        <span>${data.jadwal.pelajaran ? data.jadwal.pelajaran.nama_pelajaran : 'N/A'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jam:</span>
                        <span>${data.jadwal.jam_mulai} - ${data.jadwal.jam_selesai}</span>
                    </div>
                `;
            }
            
            if (data.status) {
                resultHtml += `
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="badge badge-${data.status === 'hadir' ? 'success' : (data.status === 'terlambat' ? 'warning' : 'secondary')}">${data.status.toUpperCase()}</span>
                    </div>
                `;
            }
            
            if (data.message) {
                resultHtml += `
                    <div class="info-row">
                        <span class="info-label">Keterangan:</span>
                        <span>${data.message}</span>
                    </div>
                `;
            }
            
            resultHtml += '</div>';
            showResult('success', resultHtml);
        } else {
            showResult('error', `<i class="fas fa-times-circle mr-2"></i><strong>QR Code Tidak Valid!</strong><br>${data.message || 'Kode QR tidak dapat divalidasi.'}`);
        }

        // Clear manual input
        $('#manual-qr-input').val('');

        // Resume scanner after 3 seconds
        setTimeout(() => {
            if (html5QrCodeScanner && isScanning) {
                html5QrCodeScanner.resume();
                showResult('info', 'Scanner aktif kembali. Arahkan kamera ke QR code.');
            }
        }, 3000);

    } catch (error) {
        console.error('Validation error:', error);
        showResult('error', '<i class="fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan saat memvalidasi QR Code. Silakan coba lagi.');
        
        // Resume scanner after error
        setTimeout(() => {
            if (html5QrCodeScanner && isScanning) {
                html5QrCodeScanner.resume();
                showResult('info', 'Scanner aktif kembali. Arahkan kamera ke QR code.');
            }
        }, 2000);
    }
}

function showResult(type, message) {
    const resultDiv = $('#scan-result');
    const contentDiv = $('#result-content');
    
    resultDiv.removeClass('success error info').addClass(type);
    contentDiv.html(message);
    resultDiv.show();
}
</script>
@endsection
