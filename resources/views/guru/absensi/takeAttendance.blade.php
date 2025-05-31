@extends('layouts.guru')

@section('title', 'Absensi Kelas')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
    #qr-reader {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }
    
    #qr-reader__scan_region {
        position: relative;
    }
    
    #qr-reader__scan_region::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px solid #4e73df;
        z-index: 1;
        pointer-events: none;
    }
    
    #qr-reader__dashboard_section_swaplink {
        display: none !important;
    }
    
    .attendance-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    #camera-permissions-help {
        display: none;
        margin-top: 15px;
        padding: 15px;
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        border-radius: 5px;
        color: #856404;
    }
    
    .permissions-steps {
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi {{ $jadwal->pelajaran->nama_pelajaran ?? 'Kelas' }} - {{ $jadwal->kelas->nama_kelas ?? '' }}</h1>
        <a href="{{ route('guru.absensi.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Scan QR Code Siswa</h6>
                </div>
                <div class="card-body">
                    <div id="qr-reader"></div>
                    
                    <div class="text-center mt-3">
                        <button class="btn btn-primary" id="startButton">
                            <i class="fas fa-camera"></i> Mulai Scan
                        </button>
                        <button class="btn btn-danger d-none" id="stopButton">
                            <i class="fas fa-stop"></i> Stop Scan
                        </button>
                    </div>
                    
                    <div id="camera-permissions-help">
                        <h5><i class="fas fa-exclamation-triangle"></i> Izin Kamera Ditolak</h5>
                        <p>Browser Anda menolak akses ke kamera. Untuk menggunakan scanner QR Code, Anda perlu memberikan izin kamera:</p>
                        
                        <div class="permissions-steps">
                            <strong>Di Desktop:</strong>
                            <ol>
                                <li>Klik ikon gembok/info pada address bar browser</li>
                                <li>Pilih "Izinkan" untuk akses kamera</li>
                                <li>Muat ulang halaman ini</li>
                            </ol>
                            
                            <strong>Di Smartphone:</strong>
                            <ol>
                                <li>Buka pengaturan aplikasi pada perangkat Anda</li>
                                <li>Cari pengaturan browser yang Anda gunakan</li>
                                <li>Aktifkan izin kamera</li>
                                <li>Kembali ke aplikasi dan muat ulang halaman</li>
                            </ol>
                            
                            <p>Atau gunakan fitur input manual NISN di bawah.</p>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong><i class="fas fa-info-circle"></i> Petunjuk:</strong>
                        <ol class="mb-0">
                            <li>Klik tombol "Mulai Scan" untuk memulai scanner.</li>
                            <li>Arahkan kamera ke QR Code siswa.</li>
                            <li>Hasil scan akan otomatis tercatat pada daftar kehadiran.</li>
                        </ol>
                    </div>

                    <div class="mt-3">
                        <form id="manualForm" action="{{ route('guru.absensi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                            <div class="form-group">
                                <label for="siswa_id">Input Manual (NISN)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nisn" name="nisn" placeholder="Masukkan NISN siswa...">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Gunakan ini jika QR Code siswa tidak bisa dipindai.</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Kehadiran</h6>
                </div>                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Info:</strong> Total siswa {{ $stats['total'] }} orang, hadir {{ $stats['hadir'] }} orang ({{ $stats['total'] > 0 ? round(($stats['hadir']/$stats['total']) * 100) : 0 }}%)
                    </div>
                    
                    <div class="attendance-list">
                        <table class="table table-bordered" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Kode Mapel</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($absensiDetail as $detail)
                                <tr class="{{ $detail->status == 'hadir' ? 'table-success' : ($detail->status == 'alpha' ? 'table-danger' : ($detail->status == 'terlambat' ? 'table-warning' : 'table-info')) }}">
                                    <td>{{ $detail->siswa->nisn ?? '-' }}</td>
                                    <td>{{ $detail->siswa->nama_lengkap ?? 'Unknown' }}</td>                                    <td>
                                        @if($detail->status == 'hadir' && (!$detail->minutes_late || $detail->minutes_late <= 0))
                                            <span class="badge badge-success">Hadir</span>                                        @elseif($detail->status == 'terlambat' || ($detail->status == 'hadir' && $detail->minutes_late > 0))
                                            @php
                                                $minutes = abs($detail->minutes_late ?? 0);
                                                $hours = floor($minutes / 60);
                                                $remainingMinutes = $minutes % 60;
                                                
                                                if ($hours > 0 && $remainingMinutes > 0) {
                                                    $timeText = $hours . ' jam ' . $remainingMinutes . ' menit';
                                                } elseif ($hours > 0) {
                                                    $timeText = $hours . ' jam';
                                                } else {
                                                    $timeText = $remainingMinutes . ' menit';
                                                }
                                            @endphp
                                            <span class="badge badge-warning">Terlambat ({{ $timeText }})</span>
                                        @elseif($detail->status == 'izin')
                                            <span class="badge badge-info">Izin</span>
                                        @elseif($detail->status == 'sakit')
                                            <span class="badge badge-warning">Sakit</span>
                                        @else
                                            <span class="badge badge-danger">Alpha</span>
                                        @endif
                                        @if($detail->keterangan && !in_array($detail->status, ['hadir', 'terlambat']))
                                            <small class="d-block text-muted mt-1">{{ $detail->keterangan }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $detail->jadwal->pelajaran->kode_pelajaran ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $detail->created_at->format('H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <!-- Replace the existing form with this new form that includes the journal -->
                        @if($absensiDetail->count() > 0)
                            <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#jurnalModal">
                                <i class="fas fa-check"></i> Selesai & Simpan Absensi
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-block disabled" disabled>
                                <i class="fas fa-check"></i> Selesai & Simpan Absensi (Belum Ada Data)
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Journal Modal -->
<div class="modal fade" id="jurnalModal" tabindex="-1" role="dialog" aria-labelledby="jurnalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ $absensiDetail->count() > 0 ? route('guru.absensi.complete', $absensiDetail->first()->id) : '#' }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="jurnalModalLabel">Jurnal Mengajar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Silahkan isi jurnal mengajar sebelum mengakhiri sesi absensi.
                    </div>
                    
                    <div class="form-group">
                        <label for="materi"><strong>Materi yang Diajarkan</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="materi" name="materi" required 
                               placeholder="Misalnya: Bab 3 - Trigonometri" maxlength="255">
                    </div>
                    
                    <div class="form-group">
                        <label for="kegiatan"><strong>Kegiatan Pembelajaran</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="kegiatan" name="kegiatan" rows="3" required 
                                  placeholder="Jelaskan kegiatan pembelajaran yang telah dilaksanakan..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="catatan"><strong>Catatan Tambahan</strong> <small>(opsional)</small></label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="2" 
                                  placeholder="Catatan lain seperti kendala, siswa yang perlu perhatian khusus, dll..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Configure toastr options
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000"
    };
      let html5QrCode;
    let cameraPermissionDenied = false;
    let scanning = false;

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
        // Setup AJAX with CSRF token for all requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize HTML5 QR Scanner but don't start scanning yet
        html5QrCode = new Html5Qrcode("qr-reader");
        
        // Start button handler
        $("#startButton").on("click", function() {
            if (scanning) return;
            
            scanning = true;
            $(this).addClass('d-none');
            $("#stopButton").removeClass('d-none');
            
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const config = { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    };
                    
                    html5QrCode.start(
                        { facingMode: "environment" }, // Prefer rear camera
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).catch(err => {
                        scanning = false;
                        $("#startButton").removeClass('d-none');
                        $("#stopButton").addClass('d-none');
                        
                        console.error("Error starting camera:", err);
                        
                        if (err.toString().includes('permission')) {
                            showCameraPermissionHelp();
                        } else {
                            toastr.error("Error starting camera: " + err);
                        }
                    });
                } else {
                    scanning = false;
                    $("#startButton").removeClass('d-none');
                    $("#stopButton").addClass('d-none');
                    toastr.error("No camera found on this device");
                }
            }).catch(err => {
                scanning = false;
                $("#startButton").removeClass('d-none');
                $("#stopButton").addClass('d-none');
                console.error("Error getting cameras:", err);
                toastr.error("Could not access the camera: " + err);
            });
        });
        
        // Stop button handler
        $("#stopButton").on("click", function() {
            if (!scanning) return;
            
            if (html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    scanning = false;
                    $("#stopButton").addClass('d-none');
                    $("#startButton").removeClass('d-none');
                }).catch(err => {
                    console.error("Error stopping camera:", err);
                });
            }
        });
          // Manual form submission
        let isManualSubmitting = false;
        $("#manualForm").on("submit", function(e) {
            e.preventDefault();
            
            // Prevent multiple submissions
            if (isManualSubmitting) {
                toastr.warning("Permintaan sedang diproses, silakan tunggu...");
                return;
            }
            
            const nisn = $("#nisn").val().trim();
            if (!nisn) {
                toastr.error("NISN tidak boleh kosong");
                return;
            }
            
            // Set submission state
            isManualSubmitting = true;
            const submitBtn = $("#manualForm button[type='submit']");
            const originalBtnText = submitBtn.html();
            
            // Disable form and show processing state
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
            $("#nisn").prop('disabled', true);
            
            // Send to server for processing
            $.ajax({
                url: "{{ route('guru.absensi.manualAttendance') }}",
                type: "POST",
                data: {
                    nisn: nisn,
                    jadwal_id: {{ $jadwal->id }}
                },                success: function(response) {
                    if (response.success) {
                        // Show success toast
                        toastr.success(response.message);
                        $("#nisn").val('');
                        
                        // Refresh the attendance list via AJAX
                        refreshAttendanceData();
                    } else {
                        // Show error toast
                        toastr.error(response.message);
                    }
                },                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan, coba lagi nanti.';
                    
                    // Try to get the actual error message from server response
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // Keep default message if parsing fails
                    }
                    
                    toastr.error(errorMessage);
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Reset form state regardless of success or failure
                    isManualSubmitting = false;
                    submitBtn.prop('disabled', false).html(originalBtnText);
                    $("#nisn").prop('disabled', false).focus();
                }
            });
        });
          let isQrProcessing = false;
        let lastQrData = '';
        let lastQrTime = 0;
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
                toastr.warning("QR code sedang diproses, silakan tunggu...");
                return;
            }
            
            isQrProcessing = true;
            lastQrData = qrCodeMessage;
            lastQrTime = now;
            
            // Send the QR code data to the server
            $.ajax({
                url: "{{ route('guru.absensi.processQr') }}",
                type: "POST",
                data: {
                    qr_data: qrCodeMessage,
                    jadwal_id: {{ $jadwal->id }}
                },
                dataType: 'json',                success: function(response) {
                    console.log("Response:", response);
                    
                    if (response.success) {
                        toastr.success(response.message);
                        
                        // Refresh the attendance data via AJAX instead of page reload
                        refreshAttendanceData();
                    } else {
                        toastr.error(response.message);
                    }
                },                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    
                    let errorMessage = "Terjadi kesalahan saat memproses QR code";
                    
                    // Try to get the actual error message from server response
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // If parsing fails, use status-specific messages
                        if (xhr.status === 419) {
                            errorMessage = "Sesi telah berakhir. Halaman akan dimuat ulang...";
                        } else if (xhr.status === 429) {
                            errorMessage = "QR code sedang diproses. Silakan tunggu sebentar.";
                        } else if (xhr.status === 409) {
                            errorMessage = "Siswa sudah diabsen sebelumnya";
                        }
                    }
                    
                    toastr.error(errorMessage);
                    
                    // If it's a CSRF token issue (419), reload the page
                    if (xhr.status === 419) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    }
                },
                complete: function() {
                    // Reset processing state
                    isQrProcessing = false;
                }
            });
        }
        
        function onScanFailure(error) {
            // We can ignore errors - they happen constantly while scanning
            // console.warn(`QR scan error: ${error}`);
        }
          function showCameraPermissionHelp() {
            $("#camera-permissions-help").show();
            $("#startButton").text("Coba Lagi").removeClass("btn-primary").addClass("btn-warning");
        }
        
        function refreshAttendanceData() {
            $.ajax({
                url: "{{ route('guru.absensi.getAttendanceData', $jadwal->id) }}",
                type: "GET",
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update statistics
                        const statsText = `<strong>Info:</strong> Total siswa ${response.stats.total} orang, hadir ${response.stats.hadir} orang (${response.percentage}%)`;
                        $('.alert-warning').html(statsText);
                        
                        // Update attendance table
                        const tbody = $('#attendanceTable tbody');
                        tbody.empty();
                        
                        if (response.attendance.length > 0) {
                            response.attendance.forEach(function(item) {
                                const row = `
                                    <tr class="${item.row_class}">
                                        <td>${item.nisn}</td>
                                        <td>${item.nama}</td>
                                        <td>${item.status}</td>
                                        <td><span class="badge badge-primary">${item.kode_pelajaran}</span></td>
                                        <td>${item.waktu}</td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                            
                            // Enable the complete button if there's data
                            $('.btn-success').removeClass('disabled').prop('disabled', false)
                                           .html('<i class="fas fa-check"></i> Selesai & Simpan Absensi');
                        } else {
                            // Disable the complete button if no data
                            $('.btn-success').addClass('disabled').prop('disabled', true)
                                           .html('<i class="fas fa-check"></i> Selesai & Simpan Absensi (Belum Ada Data)');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error refreshing attendance data:", error);
                    // Don't show error toast for this background refresh
                }
            });
        }
    });
</script>
@endsection
