@extends('layouts.admin')

@section('title', 'Pengaturan WhatsApp')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fab fa-whatsapp text-success"></i> Pengaturan WhatsApp
        </h1>
        <div>
            <button class="btn btn-info btn-sm" onclick="refreshStatus()">
                <i class="fas fa-sync-alt"></i> Refresh Status
            </button>
            <button class="btn btn-success btn-sm" onclick="sendTestNotification()">
                <i class="fas fa-paper-plane"></i> Test Notifikasi
            </button>
        </div>
    </div>

    <!-- Connection Status Card -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-wifi"></i> Status Koneksi WhatsApp
                    </h6>
                    <div id="connection-badge">
                        @if($isConnected)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Terhubung
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fas fa-times-circle"></i> Tidak Terhubung
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="qr-code-section" style="{{ $isConnected ? 'display: none;' : '' }}">
                                <h6 class="font-weight-bold">Scan QR Code untuk Menghubungkan WhatsApp:</h6>
                                <div class="text-center mb-3">
                                    <div id="qr-code-container">
                                        <button class="btn btn-primary" onclick="generateQRCode()">
                                            <i class="fas fa-qrcode"></i> Generate QR Code
                                        </button>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Cara Menghubungkan:</strong>
                                    <ol class="mb-0 mt-2">
                                        <li>Buka WhatsApp di ponsel Anda</li>
                                        <li>Tap Menu (3 titik) → Linked Devices</li>
                                        <li>Tap "Link a Device"</li>
                                        <li>Scan QR Code di atas</li>
                                    </ol>
                                </div>
                            </div>
                            <div id="connected-section" style="{{ !$isConnected ? 'display: none;' : '' }}">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>WhatsApp Terhubung!</strong>
                                    <p class="mb-0">Sistem siap mengirim notifikasi melalui WhatsApp.</p>
                                </div>
                                <button class="btn btn-danger" onclick="disconnectWhatsApp()">
                                    <i class="fas fa-unlink"></i> Disconnect WhatsApp
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Informasi Gateway:</h6>
                            <div class="form-group">
                                <label for="gateway-url">Gateway URL:</label>
                                <div class="input-group">
                                    <input type="url" class="form-control" id="gateway-url" value="{{ $gatewayUrl }}" placeholder="http://localhost:3000">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" onclick="updateGateway()">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    <div class="row">
        <!-- Admin Numbers Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i> Nomor Admin WhatsApp
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="admin-numbers">Nomor WhatsApp Admin (pisahkan dengan koma):</label>
                        <textarea class="form-control" id="admin-numbers" rows="3" placeholder="628123456789, 628987654321">{{ old('admin_numbers', implode(', ', $adminNumbers)) }}</textarea>
                        <small class="form-text text-muted">
                            Format: 628xxxxxxxxx (gunakan kode negara 62 untuk Indonesia)
                        </small>
                    </div>
                    <button class="btn btn-primary" onclick="updateAdminNumbers()">
                        <i class="fas fa-save"></i> Simpan Nomor Admin
                    </button>
                </div>
            </div>
        </div>        <!-- Parent Numbers Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-family"></i> Nomor Orang Tua
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Info:</strong> Nomor orang tua diambil otomatis dari database. 
                        Total: <span id="parent-count">{{ count($parentNumbers) }}</span> nomor aktif.
                    </div>
                    <div class="form-group">
                        <label>Daftar Nomor Orang Tua Aktif:</label>
                        <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                            @if(count($parentNumbers) > 0)
                                @foreach($parentNumbers as $number)
                                    <span class="badge badge-success mr-1 mb-1">{{ $number }}</span>
                                @endforeach
                            @else
                                <small class="text-muted">Tidak ada nomor orang tua yang terdaftar</small>
                            @endif
                        </div>
                        <small class="form-text text-muted">
                            Nomor ini akan secara otomatis menerima notifikasi WhatsApp
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Test Message Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-paper-plane"></i> Test Pesan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="test-phone">Nomor Tujuan:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="test-phone" placeholder="628123456789">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                    Pilih Nomor
                                </button>
                                <div class="dropdown-menu">
                                    <h6 class="dropdown-header">Admin Numbers</h6>
                                    @foreach($adminNumbers as $number)
                                        <a class="dropdown-item" href="#" onclick="document.getElementById('test-phone').value='{{ $number }}'">{{ $number }}</a>
                                    @endforeach
                                    @if(count($parentNumbers) > 0)
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Parent Numbers</h6>
                                        @foreach($parentNumbers as $number)
                                            <a class="dropdown-item" href="#" onclick="document.getElementById('test-phone').value='{{ $number }}'">{{ $number }}</a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="test-message">Pesan:</label>
                        <textarea class="form-control" id="test-message" rows="3" placeholder="Ini adalah pesan test dari sistem absensi">Halo, ini adalah pesan test dari sistem absensi. Sistem WhatsApp berfungsi dengan baik! 🚀</textarea>
                    </div>
                    <button class="btn btn-success" onclick="sendTestMessage()">
                        <i class="fas fa-paper-plane"></i> Kirim Test Pesan
                    </button>
                </div>
            </div>
        </div>

        <!-- Test Notification Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-bell"></i> Test Notifikasi Massal
                    </h6>
                </div>
                <div class="card-body">                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Notifikasi akan dikirim ke semua nomor admin dan orang tua.
                        Total penerima: <span id="total-recipients">{{ count($allNumbers) }}</span> nomor.
                    </div>
                    <div class="form-group">
                        <label for="test-notification-message">Pesan Notifikasi:</label>
                        <textarea class="form-control" id="test-notification-message" rows="3">📢 Test Notifikasi Sistem

Ini adalah test notifikasi dari sistem absensi sekolah. Semua fitur WhatsApp berfungsi dengan baik!

Terima kasih. 🎓</textarea>
                    </div>
                    <button class="btn btn-warning" onclick="sendTestNotification()">
                        <i class="fas fa-bell"></i> Kirim Test Notifikasi Massal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Templates Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit"></i> Template Pesan Notifikasi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="clock-in-template">Template Absen Masuk:</label>
                            <textarea class="form-control" id="clock-in-template" rows="3">{{ $messageTemplates['clock_in'] ?? '🟢 *{name}* telah absen masuk pada {time}\n📍 Lokasi: {location}' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="clock-out-template">Template Absen Keluar:</label>
                            <textarea class="form-control" id="clock-out-template" rows="3">{{ $messageTemplates['clock_out'] ?? '🔴 *{name}* telah absen keluar pada {time}\n📍 Lokasi: {location}' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="late-template">Template Terlambat:</label>
                            <textarea class="form-control" id="late-template" rows="3">{{ $messageTemplates['late'] ?? '⚠️ *{name}* terlambat masuk pada {time}\n⏰ Keterlambatan: {late_duration}' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="absent-template">Template Tidak Hadir:</label>
                            <textarea class="form-control" id="absent-template" rows="3">{{ $messageTemplates['absent'] ?? '❌ *{name}* tidak hadir hari ini\n📅 Tanggal: {date}' }}</textarea>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="updateTemplates()">
                        <i class="fas fa-save"></i> Simpan Template
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Test alert to verify scripts are loading
console.log('WhatsApp admin scripts loaded successfully');

$(document).ready(function() {
    // Auto refresh status every 30 seconds
    setInterval(refreshStatus, 30000);
});

function showLoading() {
    $('#loadingModal').modal('show');
    // Auto-close loading modal after 15 seconds as a failsafe
    if (window.loadingModalTimeout) clearTimeout(window.loadingModalTimeout);
    window.loadingModalTimeout = setTimeout(() => {
        $('#loadingModal').modal('hide');
        window.loadingModalTimeout = null;
    }, 15000);
}

function hideLoading() {
    $('#loadingModal').modal('hide');
    if (window.loadingModalTimeout) {
        clearTimeout(window.loadingModalTimeout);
        window.loadingModalTimeout = null;
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    $('.container-fluid').prepend(alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

function refreshStatus() {
    $.get('/admin/whatsapp/status')
        .done(function(response) {
            if (response.success) {
                updateConnectionStatus(response.connected);
            }
        })
        .fail(function() {
            console.error('Failed to refresh status');
        });
}

function updateConnectionStatus(isConnected) {
    const badge = $('#connection-badge');
    const qrSection = $('#qr-code-section');
    const connectedSection = $('#connected-section');
    
    if (isConnected) {
        badge.html('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Terhubung</span>');
        qrSection.hide();
        connectedSection.show();
    } else {
        badge.html('<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Tidak Terhubung</span>');
        qrSection.show();
        connectedSection.hide();
    }
}

function generateQRCode() {
    showLoading();
    
    $.get('/admin/whatsapp/qr-code')
        .done(function(response) {
            hideLoading();
            if (response.success) {
                // Handle both string and object responses for QR code
                let qrCodeSrc = '';
                if (typeof response.qr_code === 'string') {
                    qrCodeSrc = response.qr_code;
                } else if (response.qr_code && response.qr_code.qrCode) {
                    qrCodeSrc = response.qr_code.qrCode;
                }
                
                if (qrCodeSrc) {
                    $('#qr-code-container').html(`
                        <div class="text-center">
                            <img src="${qrCodeSrc}" alt="QR Code" class="img-fluid" style="max-width: 300px; border: 1px solid #ddd; padding: 10px; background: white;">
                            <p class="mt-2 text-muted">Scan QR Code ini dengan WhatsApp Anda</p>
                            <small class="text-info">QR Code akan expire dalam 2 menit</small>
                        </div>
                    `);
                    
                    // Start polling for connection status
                    const pollInterval = setInterval(() => {
                        $.get('/admin/whatsapp/status')
                            .done(function(statusResponse) {
                                if (statusResponse.success && statusResponse.connected) {
                                    clearInterval(pollInterval);
                                    updateConnectionStatus(true);
                                    showAlert('success', 'WhatsApp berhasil terhubung!');
                                }
                            })
                            .fail(function() {
                                // Silently handle polling errors
                                console.log('Status polling failed');
                            });
                    }, 2000);
                    
                    // Stop polling after 2 minutes
                    setTimeout(() => {
                        clearInterval(pollInterval);
                        console.log('QR code polling stopped after 2 minutes');
                    }, 120000);
                } else {
                    showAlert('danger', 'QR Code tidak ditemukan dalam response');
                }
            } else {
                showAlert('danger', response.message || 'Gagal mengambil QR Code');
            }
        })
        .fail(function(xhr, status, error) {
            hideLoading();
            console.error('QR Code request failed:', xhr.responseText);
            showAlert('danger', 'Gagal mengambil QR Code: ' + (xhr.responseJSON?.message || error));
        });
}

function disconnectWhatsApp() {
    if (confirm('Apakah Anda yakin ingin memutuskan koneksi WhatsApp?')) {
        showLoading();
        
        $.post('/admin/whatsapp/disconnect')
            .done(function(response) {
                hideLoading();
                if (response.success) {
                    updateConnectionStatus(false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message);
                }
            })
            .fail(function() {
                hideLoading();
                showAlert('danger', 'Gagal memutuskan koneksi');
            });
    }
}

function updateGateway() {
    const gatewayUrl = $('#gateway-url').val();
    if (!gatewayUrl) {
        showAlert('warning', 'Gateway URL harus diisi');
        return;
    }
    
    showLoading();
    
    $.post('/admin/whatsapp/gateway', {
        gateway_url: gatewayUrl,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            showAlert('success', response.message);
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function() {
        hideLoading();
        showAlert('danger', 'Gagal mengupdate gateway URL');
    });
}

function updateAdminNumbers() {
    const adminNumbers = $('#admin-numbers').val();
    if (!adminNumbers.trim()) {
        showAlert('warning', 'Nomor admin harus diisi');
        return;
    }
    
    showLoading();
    
    $.post('/admin/whatsapp/admin-numbers', {
        admin_numbers: adminNumbers,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            showAlert('success', response.message);
            if (response.valid_numbers) {
                $('#admin-numbers').val(response.valid_numbers.join(', '));
            }
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function() {
        hideLoading();
        showAlert('danger', 'Gagal mengupdate nomor admin');
    });
}

function updateTemplates() {
    const templates = {
        clock_in_template: $('#clock-in-template').val(),
        clock_out_template: $('#clock-out-template').val(),
        late_template: $('#late-template').val(),
        absent_template: $('#absent-template').val(),
        _token: '{{ csrf_token() }}'
    };
    
    showLoading();
    
    $.post('/admin/whatsapp/templates', templates)
        .done(function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        })
        .fail(function() {
            hideLoading();
            showAlert('danger', 'Gagal mengupdate template pesan');
        });
}

function sendTestMessage() {
    const phoneNumber = $('#test-phone').val();
    const message = $('#test-message').val();
    
    if (!phoneNumber.trim() || !message.trim()) {
        showAlert('warning', 'Nomor tujuan dan pesan harus diisi');
        return;
    }
    
    showLoading();
    
    $.post('/admin/whatsapp/test-message', {
        phone_number: phoneNumber,
        message: message,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            showAlert('success', response.message);
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function() {
        hideLoading();
        showAlert('danger', 'Gagal mengirim pesan test');
    });
}

function sendTestNotification() {
    showLoading();
    
    $.post('/admin/whatsapp/test-notification', {
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            showAlert('success', response.message);
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function() {
        hideLoading();
        showAlert('danger', 'Gagal mengirim test notifikasi');
    });
}
</script>
@endsection
