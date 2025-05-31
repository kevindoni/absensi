@extends('layouts.guru')

@section('title', 'Scan QR Code')

@section('styles')
<style>
    #qr-reader {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }
    
    #qr-reader__dashboard_section_swaplink {
        display: none;
    }
    
    .scan-result {
        min-height: 60px;
    }
    
    .student-list {
        max-height: 350px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Scan QR Code Absensi</h1>
        <a href="{{ route('guru.absensi.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <!-- QR Reader -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Scan QR Code</h6>
                    <button id="switchCamera" class="btn btn-sm btn-primary">
                        <i class="fas fa-sync fa-sm"></i> Ganti Kamera
                    </button>
                </div>
                <div class="card-body">
                    <div id="qr-reader"></div>
                    <div id="scanResult" class="scan-result mt-3"></div>
                </div>
            </div>

            <!-- Manual Input -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Input Manual</h6>
                </div>
                <div class="card-body">
                    <form id="manualAttendanceForm">
                        <div class="form-group">
                            <label for="nisn">NISN</label>
                            <input type="text" class="form-control" id="nisn" placeholder="Masukkan NISN">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    <div id="manualScanResult" class="scan-result mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <!-- Class Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Kelas</h6>
                </div>
                <div class="card-body">
                    <h5>{{ $jadwal->pelajaran->nama_pelajaran }}</h5>
                    <p class="mb-1"><strong>Kelas:</strong> {{ $jadwal->kelas->nama_kelas }}</p>
                    <p class="mb-1"><strong>Hari:</strong> {{ config('constants.hari')[$jadwal->hari] }}</p>
                    <p class="mb-1"><strong>Jam:</strong> {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</p>
                    <p class="mb-1"><strong>Total Siswa:</strong> {{ $totalSiswa }}</p>
                    <p><strong>Hadir:</strong> <span id="hadir-count">{{ $hadir }}</span> siswa</p>
                </div>
            </div>

            <!-- Attendance List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Siswa Hadir</h6>
                </div>
                <div class="card-body">
                    <div class="student-list">
                        <table class="table table-sm" id="hadirTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>NISN</th>
                                    <th>Kelas</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($absensiDetail as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->siswa->nama_lengkap }}</td>
                                    <td>{{ $detail->siswa->nisn }}</td>
                                    <td>{{ $detail->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                    <td>{{ $detail->created_at->format('H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Complete Button -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <a href="#" class="btn btn-success btn-block" data-toggle="modal" data-target="#completeModal">
                        <i class="fas fa-check fa-sm"></i> Selesaikan Absensi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complete Attendance Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('guru.absensi.complete', $absensi->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="completeModalLabel">Selesaikan Absensi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="materi">Materi</label>
                        <input type="text" class="form-control" id="materi" name="materi" required placeholder="Masukkan materi pembelajaran">
                    </div>
                    <div class="form-group">
                        <label for="kegiatan">Kegiatan</label>
                        <textarea class="form-control" id="kegiatan" name="kegiatan" rows="3" required placeholder="Jelaskan kegiatan pembelajaran"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan (opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Tambahkan catatan jika ada"></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-info-circle"></i> Setelah menyelesaikan absensi, siswa yang belum hadir akan otomatis ditandai alpha.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.2.1/html5-qrcode.min.js"></script>
<script>
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
        // Setup CSRF protection for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Define variables we'll need
        let html5QrCode;
        let currentCamera = null;
        let cameras = [];
        let currentCameraIndex = 0;
        
        // Function to start scanner with a specific camera
        function startScanner(cameraId) {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    startWithCamera(cameraId);
                });
            } else {
                startWithCamera(cameraId);
            }
        }
        
        // Function to actually start the camera
        function startWithCamera(cameraId) {
            const config = { 
                fps: 10,
                qrbox: { width: 250, height: 250 },
                formatsToSupport: [ 
                    Html5QrcodeSupportedFormats.QR_CODE
                ],
                aspectRatio: 1.0
            };
            
            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                cameraId, 
                config, 
                onScanSuccess,
                onScanFailure
            ).catch(function(err) {
                console.error("Error starting scanner:", err);
                $('#scanResult').html(
                    '<div class="alert alert-danger">Error starting scanner: ' + err + '. Try switching cameras.</div>'
                );
            });
        }
        
        // Function to detect cameras and start scanner
        function initializeScanner() {
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameras = devices;
                    startScanner(devices[0].id);
                    currentCameraIndex = 0;
                    currentCamera = devices[0].id;
                } else {
                    $('#scanResult').html('<div class="alert alert-danger">No cameras found</div>');
                }
            }).catch(err => {
                console.error("Error getting cameras:", err);
                $('#scanResult').html('<div class="alert alert-danger">Error getting cameras: ' + err + '</div>');
            });
        }
        
        // Camera switch button
        $('#switchCamera').on('click', function() {
            if (cameras.length <= 1) {
                $('#scanResult').html('<div class="alert alert-info">Only one camera available</div>');
                return;
            }
            
            currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
            startScanner(cameras[currentCameraIndex].id);
        });
          let isQrProcessing = false;
        let lastQrData = '';
        let lastQrTime = 0;
          // Function that's called when QR code is successfully scanned
        function onScanSuccess(qrCodeMessage) {
            console.log("QR Code detected:", qrCodeMessage);
            
            // Play beep sound when QR code is successfully scanned
            playBeepSound();
            
            // Prevent processing the same QR code within 2 seconds
            const now = Date.now();
            if (qrCodeMessage === lastQrData && (now - lastQrTime) < 2000) {
                console.log("Duplicate QR code ignored");
                return;
            }
            
            // Prevent multiple simultaneous requests
            if (isQrProcessing) {
                $('#scanResult').html('<div class="alert alert-warning">QR code sedang diproses, silakan tunggu...</div>');
                return;
            }
            
            isQrProcessing = true;
            lastQrData = qrCodeMessage;
            lastQrTime = now;
            
            // Show loading indicator
            $('#scanResult').html('<div class="alert alert-info">Processing...</div>');
            
            // Send to server
            $.ajax({
                url: "{{ route('guru.absensi.processQr') }}",
                type: "POST",
                data: {
                    qr_data: qrCodeMessage,
                    jadwal_id: {{ $jadwal->id }}
                },
                dataType: 'json',
                success: function(response) {
                    console.log("Server response:", response);
                    
                    if (response.success) {
                        $('#scanResult').html('<div class="alert alert-success">' + response.message + '</div>');
                        
                        // Add student to table
                        const siswaHTML = `
                            <tr>
                                <td>${$('#hadirTable tbody tr').length + 1}</td>
                                <td>${response.siswa.nama}</td>
                                <td>${response.siswa.nisn}</td>
                                <td>${response.siswa.kelas}</td>
                                <td>${new Date().toLocaleTimeString()}</td>
                            </tr>
                        `;
                        $('#hadirTable tbody').append(siswaHTML);
                        
                        // Update counter
                        $('#hadir-count').text(parseInt($('#hadir-count').text()) + 1);
                    } else {
                        $('#scanResult').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                    
                    // Clear message after 5 seconds
                    setTimeout(function() {
                        $('#scanResult').html('');
                    }, 5000);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr);
                    
                    let errorMsg = "Server error. Please try again.";
                    if (xhr.status === 419) {
                        errorMsg = "Session expired. Please refresh the page.";
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else if (xhr.status === 429) {
                        errorMsg = "QR code sedang diproses. Silakan tunggu sebentar.";
                    }
                    
                    $('#scanResult').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                },
                complete: function() {
                    // Reset processing state
                    isQrProcessing = false;
                }
            });
            });
        }
        
        function onScanFailure(error) {
            // We can ignore errors - they happen constantly while scanning
            // console.warn(`QR scan error: ${error}`);
        }
          // Handle manual form submission
        let isManualSubmitting = false;
        $('#manualAttendanceForm').on('submit', function(e) {
            e.preventDefault();
            
            // Prevent multiple submissions
            if (isManualSubmitting) {
                $('#manualScanResult').html('<div class="alert alert-warning">Permintaan sedang diproses, silakan tunggu...</div>');
                return;
            }
            
            const nisn = $('#nisn').val().trim();
            if (!nisn) {
                $('#manualScanResult').html('<div class="alert alert-danger">NISN cannot be empty</div>');
                return;
            }
            
            // Set submission state
            isManualSubmitting = true;
            const submitBtn = $('#manualAttendanceForm button[type="submit"]');
            const originalBtnText = submitBtn.html();
            
            // Disable form and show processing state
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            $('#nisn').prop('disabled', true);
            $('#manualScanResult').html('<div class="alert alert-info">Processing...</div>');
            
            $.ajax({
                url: "{{ route('guru.absensi.manualAttendance') }}",
                type: "POST",
                data: {
                    nisn: nisn,
                    jadwal_id: {{ $jadwal->id }}
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#manualScanResult').html('<div class="alert alert-success">' + response.message + '</div>');
                        
                        // Add student to table
                        const siswaHTML = `
                            <tr>
                                <td>${$('#hadirTable tbody tr').length + 1}</td>
                                <td>${response.siswa.nama}</td>
                                <td>${response.siswa.nisn}</td>
                                <td>${response.siswa.kelas}</td>
                                <td>${new Date().toLocaleTimeString()}</td>
                            </tr>
                        `;
                        $('#hadirTable tbody').append(siswaHTML);
                        
                        // Update counter
                        $('#hadir-count').text(parseInt($('#hadir-count').text()) + 1);
                        
                        // Clear input
                        $('#nisn').val('');
                    } else {
                        $('#manualScanResult').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                    
                    // Clear message after 5 seconds
                    setTimeout(function() {
                        $('#manualScanResult').html('');
                    }, 5000);
                },
                error: function(xhr, status, error) {
                    let errorMsg = "Server error. Please try again.";
                    if (xhr.status === 419) {
                        errorMsg = "Session expired. Please refresh the page.";
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else if (xhr.status === 429) {
                        errorMsg = "Request sedang diproses. Silakan tunggu sebentar.";
                    }
                    
                    $('#manualScanResult').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                },
                complete: function() {
                    // Reset form state regardless of success or failure
                    isManualSubmitting = false;
                    submitBtn.prop('disabled', false).html(originalBtnText);
                    $('#nisn').prop('disabled', false).focus();
                }
            });
        });
            });
        });
        
        // Initialize scanner when page loads
        initializeScanner();
        
        // Focus on NISN input
        $('#nisn').focus();
    });
</script>
@endsection
