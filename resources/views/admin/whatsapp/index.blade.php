@extends('layouts.admin')

@section('title', 'Pengaturan WhatsApp')

@section('content')
<div class="container-fluid">    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fab fa-whatsapp text-success"></i> Pengaturan WhatsApp
        </h1>
        <div class="btn-group d-flex">
            <button class="btn btn-info btn-sm flex-fill" onclick="refreshStatus()">
                <i class="fas fa-sync-alt"></i> <span class="d-none d-sm-inline">Refresh</span>
            </button>
            <button class="btn btn-success btn-sm flex-fill" onclick="sendTestNotification()">
                <i class="fas fa-paper-plane"></i> <span class="d-none d-sm-inline">Test Admin</span>
            </button>
        </div>
    </div>

    <!-- Main Settings Row -->
    <div class="row">
        <!-- Connection & Status Card -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fab fa-whatsapp mr-2"></i> Status Koneksi WhatsApp
                        </h6>
                        <div id="connection-indicator" class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm text-light mr-2" role="status" id="status-spinner" style="display: none;">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <small id="last-update" class="text-light opacity-75">
                                Last update: {{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Status Overview -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div id="connection-status-card" class="status-card">
                                @if($isConnected)
                                    <div class="status-card-connected">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="status-icon status-icon-success">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <h5 class="mb-1 font-weight-bold text-success">WhatsApp Terhubung</h5>
                                                    <p class="mb-0 text-muted small">Gateway aktif dan siap mengirim notifikasi</p>
                                                    <small class="text-success">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Online sejak: <span id="connection-time">{{ date('d/m/Y H:i') }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="status-actions">
                                                <button class="btn btn-outline-danger btn-sm" onclick="disconnectWhatsApp()">
                                                    <i class="fas fa-unlink mr-1"></i> Disconnect
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="status-card-disconnected">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="status-icon status-icon-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <h5 class="mb-1 font-weight-bold text-danger">Tidak Terhubung</h5>
                                                    <p class="mb-0 text-muted small">Silakan scan QR Code untuk menghubungkan WhatsApp</p>
                                                    <small class="text-danger">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        Service tidak aktif
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="status-actions">
                                                <button class="btn btn-primary btn-sm" onclick="generateQRCode()">
                                                    <i class="fas fa-qrcode mr-1"></i> Generate QR
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 30%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>                    <!-- Configuration Section -->
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="config-section">
                                <h6 class="font-weight-bold text-dark mb-3">
                                    <i class="fas fa-cog text-primary mr-2"></i> Gateway URL
                                </h6>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Gateway Endpoint:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-link text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="url" class="form-control" id="gateway-url" 
                                               value="{{ $gatewayUrl ?? 'http://localhost:3000' }}" 
                                               placeholder="http://localhost:3000">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary" onclick="updateGateway()">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="config-section">
                                <h6 class="font-weight-bold text-dark mb-3">
                                    <i class="fas fa-users text-success mr-2"></i> Nomor Admin
                                </h6>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Admin WhatsApp:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-phone text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="admin-numbers" 
                                               value="{{ implode(', ', $adminNumbers ?? []) }}" 
                                               placeholder="628123456789, 628987654321">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-success" onclick="updateAdminNumbers()">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- Statistics Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="quick-stats bg-light rounded p-3">
                                <div class="row text-center">
                                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                                        <div class="stat-item">
                                            <h5 class="mb-1 font-weight-bold text-primary" id="total-sent">0</h5>
                                            <small class="text-muted">Pesan Terkirim</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                                        <div class="stat-item">
                                            <h5 class="mb-1 font-weight-bold text-success" id="success-rate">0%</h5>
                                            <small class="text-muted">Success Rate</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                                        <div class="stat-item">
                                            <h5 class="mb-1 font-weight-bold text-info" id="admin-count">{{ count($adminNumbers ?? []) }}</h5>
                                            <small class="text-muted">Admin Aktif</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                                        <div class="stat-item">
                                            <h5 class="mb-1 font-weight-bold text-warning" id="uptime">0h 0m</h5>
                                            <small class="text-muted">Uptime</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Functions Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-success text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-vial mr-2"></i> Test Functions
                    </h6>
                </div>
                <div class="card-body p-4">
                    <!-- Quick Test Form -->
                    <div class="test-form-section mb-4">
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Nomor Tujuan:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="test-phone" 
                                       placeholder="628123456789">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Pesan Test:</label>
                            <textarea class="form-control" id="test-message" rows="3" 
                                      placeholder="Tulis pesan test...">Test WhatsApp dari {{ config('app.name') }}
Tanggal: {{ date('d/m/Y H:i:s') }}
Status: Normal</textarea>
                        </div>
                    </div>                    <!-- Test Buttons -->
                    <div class="test-actions mb-4">
                        <button class="btn btn-success btn-block mb-2" onclick="sendTestMessage()">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Test Pesan
                        </button>
                        <div class="row">
                            <div class="col-6 pr-1">
                                <button class="btn btn-info btn-sm btn-block" onclick="testSystemHealth()">
                                    <i class="fas fa-heartbeat mr-1"></i> <span class="d-none d-lg-inline">Health</span> Check
                                </button>
                            </div>
                            <div class="col-6 pl-1">
                                <button class="btn btn-warning btn-sm btn-block" onclick="generateQRCode()">
                                    <i class="fas fa-qrcode mr-1"></i> QR <span class="d-none d-lg-inline">Code</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Test Results -->
                    <div id="test-results" class="test-results" style="display: none;">
                        <div class="alert alert-info border-0">
                            <div class="d-flex align-items-center">
                                <div class="result-icon mr-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="result-content">
                                    <h6 class="mb-1">Test Result</h6>
                                    <p class="mb-0 small" id="test-result-message">Ready for testing...</p>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Test Statistics -->
                    <div class="test-stats">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-box p-2 border rounded">
                                    <h6 class="mb-1 font-weight-bold text-success" id="success-tests">0</h6>
                                    <small class="text-muted">Success</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box p-2 border rounded">
                                    <h6 class="mb-1 font-weight-bold text-danger" id="failed-tests">0</h6>
                                    <small class="text-muted">Failed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Section (Hidden by default) -->
    <div id="qr-code-section" class="row" style="{{ $isConnected ? 'display: none;' : '' }}">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-qrcode mr-2"></i> QR Code WhatsApp
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-info">
                        <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i> Cara Menghubungkan WhatsApp:</h6>
                        <ol class="text-left">
                            <li>Klik tombol "Generate QR Code"</li>
                            <li>Buka WhatsApp di ponsel Anda</li>
                            <li>Pilih menu "Linked Devices" → "Link a Device"</li>
                            <li>Scan QR Code yang muncul di bawah</li>
                        </ol>
                    </div>
                    <div id="qr-code-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Notification Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit mr-2"></i> Template Notifikasi Kehadiran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Info:</strong> Template untuk notifikasi otomatis ke orang tua. 
                        Gunakan variabel: <code>{nama_siswa}</code>, <code>{kelas}</code>, <code>{tanggal}</code>, <code>{status}</code>
                    </div>                    <!-- Template Tabs -->
                    <ul class="nav nav-tabs nav-fill" id="templateTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="hadir-tab" data-toggle="tab" href="#hadir" role="tab">
                                <span class="d-none d-md-inline">✅ </span>Hadir
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="terlambat-tab" data-toggle="tab" href="#terlambat" role="tab">
                                <span class="d-none d-md-inline">⚠️ </span>Terlambat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tidak-hadir-tab" data-toggle="tab" href="#tidak-hadir" role="tab">
                                <span class="d-none d-md-inline">❌ </span><span class="d-inline d-md-none">Tidak</span><span class="d-none d-md-inline">Tidak Hadir</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="sakit-tab" data-toggle="tab" href="#sakit" role="tab">
                                <span class="d-none d-md-inline">🏥 </span>Sakit
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="izin-tab" data-toggle="tab" href="#izin" role="tab">
                                <span class="d-none d-md-inline">📄 </span>Izin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pulang-tab" data-toggle="tab" href="#pulang" role="tab">
                                <span class="d-none d-md-inline">🔴 </span>Pulang
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="templateTabsContent">
                        <!-- Hadir Tab -->
                        <div class="tab-pane fade show active" id="hadir" role="tabpanel">
                            <div class="mt-3">
                                <textarea class="form-control" name="check_in" rows="4">{{ \App\Models\Setting::getSetting('whatsapp_template_check_in', '🟢 Notifikasi Kehadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
✅ *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas perhatiannya.') }}</textarea>
                            </div>
                        </div>

                        <!-- Terlambat Tab -->
                        <div class="tab-pane fade" id="terlambat" role="tabpanel">
                            <div class="mt-3">
                                <textarea class="form-control" name="late" rows="4">{{ \App\Models\Setting::getSetting('whatsapp_template_late', '⚠️ Notifikasi Keterlambatan dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
⏰ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon perhatian untuk kedisiplinan anak.') }}</textarea>
                            </div>
                        </div>

                        <!-- Tidak Hadir Tab -->
                        <div class="tab-pane fade" id="tidak-hadir" role="tabpanel">
                            <div class="mt-3">
                                <textarea class="form-control" name="absent" rows="4">{{ \App\Models\Setting::getSetting('whatsapp_template_absent', '❌ Notifikasi Ketidakhadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
❌ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon konfirmasi mengenai ketidakhadiran anak.') }}</textarea>
                            </div>
                        </div>

                        <!-- Sakit Tab -->
                        <div class="tab-pane fade" id="sakit" role="tabpanel">
                            <div class="mt-3">
                                <textarea class="form-control" name="sick" rows="4">{{ \App\Models\Setting::getSetting('whatsapp_template_sick', '🏥 Notifikasi Sakit dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🏥 *Status*: {status}
📝 *Keterangan*: {keterangan}

Semoga lekas sembuh.') }}</textarea>
                            </div>
                        </div>

                        <!-- Izin Tab -->
                        <div class="tab-pane fade" id="izin" role="tabpanel">
                            <div class="mt-3">
                                <textarea class="form-control" name="permission" rows="4">{{ \App\Models\Setting::getSetting('whatsapp_template_permission', '📄 Notifikasi Izin dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
📄 *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas pemberitahuannya.') }}</textarea>
                            </div>
                        </div>

                        <!-- Pulang Tab -->
                        <div class="tab-pane fade" id="pulang" role="tabpanel">
                            <div class="mt-3">
                                <textarea class="form-control" name="check_out" rows="4">{{ \App\Models\Setting::getSetting('whatsapp_template_check_out', '🔴 Notifikasi Pulang dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
🔴 *Status*: {status}
📝 *Keterangan*: {keterangan}

Anak telah pulang dengan selamat.') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" onclick="updateAttendanceTemplates()">
                            <i class="fas fa-save"></i> Simpan Template
                        </button>
                        <button type="button" class="btn btn-secondary ml-2" onclick="resetTemplatesToDefault()">
                            <i class="fas fa-undo"></i> Reset Default
                        </button>
                    </div>

                    <!-- Available Variables -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-2"><i class="fas fa-info-circle"></i> Variabel Tersedia</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small>
                                                <code>{school_name}</code> - Nama sekolah<br>
                                                <code>{nama_siswa}</code> - Nama siswa<br>
                                                <code>{kelas}</code> - Kelas siswa<br>
                                                <code>{tanggal}</code> - Tanggal absensi
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small>
                                                <code>{waktu}</code> - Waktu absensi<br>
                                                <code>{status}</code> - Status kehadiran<br>
                                                <code>{keterangan}</code> - Keterangan<br>
                                                <code>{mata_pelajaran}</code> - Mata pelajaran
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
    </div>
</div>
@endsection

@section('scripts')
<script>
// Setup AJAX untuk mengurangi console logging
$.ajaxSetup({
    silent: true // tidak menampilkan log
});

$(document).ready(function() {
    startStatusMonitoring();
    initializeTestTracking();
});

// Test tracking functionality
let testStats = {
    today: 0,
    success: 0,
    failed: 0
};

function initializeTestTracking() {
    const today = new Date().toDateString();
    const savedStats = localStorage.getItem('whatsapp_test_stats_' + today);
    
    if (savedStats) {
        testStats = JSON.parse(savedStats);
    } else {
        testStats = { today: 0, success: 0, failed: 0 };
    }
    
    updateTestStatsDisplay();
}

function updateTestStats(success) {
    testStats.today++;
    if (success) {
        testStats.success++;
    } else {
        testStats.failed++;
    }
    
    const today = new Date().toDateString();
    localStorage.setItem('whatsapp_test_stats_' + today, JSON.stringify(testStats));
    
    updateTestStatsDisplay();
}

function updateTestStatsDisplay() {
    $('#success-tests').text(testStats.success);
    $('#failed-tests').text(testStats.failed);
    
    const successRate = testStats.today > 0 ? Math.round((testStats.success / testStats.today) * 100) : 100;
    $('#success-rate').text(successRate + '%');
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
    
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

function refreshStatus() {
    const spinner = $('#status-spinner');
    spinner.show();
    
    $.get('/admin/whatsapp/status')
        .done(function(response) {
            if (response.success) {
                updateConnectionStatus(response.connected);
            }
        })
        .fail(function() {
            console.error('Failed to refresh status');
        })
        .always(function() {
            spinner.hide();
        });
}

function updateQuickStats() {
    $.get('/admin/whatsapp/stats')
        .done(function(response) {
            if (response.success) {
                $('#total-sent').text(response.data.total_sent || 0);
                $('#success-rate').text((response.data.success_rate || 0) + '%');
                $('#uptime').text(response.data.uptime || '0h 0m');
            }
        })
        .fail(function() {
            console.log('Stats update failed');
        });
}

function startStatusMonitoring() {
    // Update stats every 30 seconds
    setInterval(updateQuickStats, 30000);
    
    // Initial update
    updateQuickStats();
    
    // Check connection status every 10 seconds
    setInterval(function() {
        $.get('/admin/whatsapp/status')
            .done(function(response) {
                if (response.success) {
                    const currentStatus = response.connected;
                    
                    if (currentStatus !== window.lastKnownStatus) {
                        window.lastKnownStatus = currentStatus;
                        updateConnectionStatus(currentStatus);
                        
                        if (currentStatus) {
                            showAlert('success', 'WhatsApp connection restored!');
                        } else {
                            showAlert('warning', 'WhatsApp connection lost. Please check your connection.');
                        }
                    }
                }
            })
            .fail(function() {
                console.log('Status monitoring request failed');
            });
    }, 10000);
    
    // Initial status check
    $.get('/admin/whatsapp/status')
        .done(function(response) {
            if (response.success) {
                window.lastKnownStatus = response.connected;
                updateConnectionStatus(response.connected);
            }
        });
}

function updateConnectionStatus(isConnected) {
    const statusCard = $('#connection-status-card');
    const qrSection = $('#qr-code-section');
    const lastUpdate = $('#last-update');
    
    // Update last update time with Indonesian format
    const now = new Date();
    const jakartaTime = now.toLocaleString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    lastUpdate.text(`Last update: ${jakartaTime}`);
    
    if (isConnected) {
        statusCard.html(`
            <div class="status-card-connected">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="status-icon status-icon-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-1 font-weight-bold text-success">Terhubung</h5>
                            <p class="mb-0 text-muted small">WhatsApp Gateway aktif dan siap digunakan</p>
                            <small class="text-success">
                                <i class="fas fa-clock mr-1"></i>
                                Connected since: <span id="connection-time">${jakartaTime}</span>
                            </small>
                        </div>
                    </div>
                    <div class="status-actions">
                        <button class="btn btn-outline-danger btn-sm" onclick="disconnectWhatsApp()">
                            <i class="fas fa-unlink mr-1"></i> Disconnect
                        </button>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        `);
        qrSection.hide();
        updateQuickStats();
    } else {
        statusCard.html(`
            <div class="status-card-disconnected">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="status-icon status-icon-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-1 font-weight-bold text-danger">Tidak Terhubung</h5>
                            <p class="mb-0 text-muted small">Silakan scan QR Code untuk menghubungkan WhatsApp</p>
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Service tidak aktif
                            </small>
                        </div>
                    </div>
                    <div class="status-actions">
                        <button class="btn btn-primary btn-sm" onclick="generateQRCode()">
                            <i class="fas fa-qrcode mr-1"></i> Generate QR
                        </button>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 30%"></div>
                    </div>
                </div>
            </div>
        `);
        qrSection.show();
    }
}

function generateQRCode() {
    $.get('/admin/whatsapp/qr-code')
        .done(function(response) {
            if (response.success) {
                let qrCodeSrc = '';
                if (typeof response.qr_code === 'string') {
                    qrCodeSrc = response.qr_code;
                } else if (response.qr_code && response.qr_code.qrCode) {
                    qrCodeSrc = response.qr_code.qrCode;
                }
                
                if (qrCodeSrc) {
                    $('#qr-code-container').html(`
                        <div class="qr-code-display">
                            <div class="qr-code-wrapper">
                                <img src="${qrCodeSrc}" alt="QR Code" class="qr-code-image">
                            </div>
                            <div class="qr-code-info">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fab fa-whatsapp mr-2"></i>Scan dengan WhatsApp
                                </h6>
                                <p class="text-muted small mb-3">
                                    QR Code akan expire dalam 2 menit
                                </p>
                            </div>
                        </div>
                    `);
                    
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
                                console.log('Status polling failed');
                            });
                    }, 2000);
                    
                    setTimeout(() => {
                        clearInterval(pollInterval);
                    }, 120000);
                } else {
                    showAlert('danger', 'QR Code tidak ditemukan dalam response');
                }
            } else {
                showAlert('danger', response.message || 'Gagal mengambil QR Code');
            }
        })
        .fail(function(xhr, status, error) {
            showAlert('danger', 'Gagal mengambil QR Code: ' + (xhr.responseJSON?.message || error));
        });
}

function disconnectWhatsApp() {
    if (confirm('Apakah Anda yakin ingin memutuskan koneksi WhatsApp?')) {
        $.post('/admin/whatsapp/disconnect')
            .done(function(response) {
                if (response.success) {
                    updateConnectionStatus(false);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message);
                }
            })
            .fail(function() {
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
    
    $.post('/admin/whatsapp/gateway', {
        gateway_url: gatewayUrl,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function() {
        showAlert('danger', 'Gagal mengupdate gateway URL');
    });
}

function updateAdminNumbers() {
    const adminNumbers = $('#admin-numbers').val();
    if (!adminNumbers.trim()) {
        showAlert('warning', 'Nomor admin harus diisi');
        return;
    }
    
    $.post('/admin/whatsapp/admin-numbers', {
        admin_numbers: adminNumbers,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
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
        showAlert('danger', 'Gagal mengupdate nomor admin');
    });
}

function testSystemHealth() {
    const resultsDiv = $('#test-results');
    resultsDiv.show().find('.alert')
        .removeClass('alert-success alert-danger alert-warning')
        .addClass('alert-info')
        .find('#test-result-message')
        .html('Running system health check...');

    $.get('/admin/whatsapp/system-health')
        .done(function(response) {
            if (response.success) {
                const data = response.data;
                const healthStatus = {
                    all_good: data.db_connected && data.whatsapp_connected,
                    alert_type: data.db_connected && data.whatsapp_connected ? 'success' : 'warning',
                    message: `
                        <strong>System Health Report:</strong><br>
                        Database: ${data.db_connected ? '✅' : '❌'}<br>
                        WhatsApp: ${data.whatsapp_connected ? '✅' : '❌'}<br>
                        Success Rate: ${data.success_rate}%<br>
                        Active Admins: ${data.admin_count}<br>
                        Uptime: ${data.uptime}
                    `
                };

                resultsDiv.find('.alert')
                    .removeClass('alert-info alert-danger alert-warning')
                    .addClass(`alert-${healthStatus.alert_type}`)
                    .find('#test-result-message')
                    .html(healthStatus.message);

                resultsDiv.find('.result-icon i')
                    .removeClass('fa-info-circle fa-times-circle')
                    .addClass(healthStatus.all_good ? 'fa-check-circle' : 'fa-exclamation-triangle');
                
                updateTestStats(true);
            } else {
                throw new Error(response.message || 'Health check failed');
            }
        })
        .fail(function(xhr) {
            const error = xhr.responseJSON?.message || 'Failed to complete system health check';
            resultsDiv.find('.alert')
                .removeClass('alert-info alert-success alert-warning')
                .addClass('alert-danger')
                .find('#test-result-message')
                .text(error);

            resultsDiv.find('.result-icon i')
                .removeClass('fa-info-circle fa-check-circle')
                .addClass('fa-times-circle');
            
            updateTestStats(false);
        });
}

function sendTestMessage() {
    let phoneNumber = $('#test-phone').val().trim();
    const message = $('#test-message').val().trim();
    
    if (!phoneNumber || !message) {
        showAlert('warning', 'Nomor tujuan dan pesan harus diisi');
        return;
    }
    
    phoneNumber = phoneNumber.replace(/[^\d]/g, '');
    
    if (!phoneNumber.startsWith('62')) {
        if (phoneNumber.startsWith('0')) {
            phoneNumber = '62' + phoneNumber.substring(1);
        } else if (phoneNumber.startsWith('8')) {
            phoneNumber = '62' + phoneNumber;
        }
    }
    
    $.post('/admin/whatsapp/test-message', {
        phone_number: phoneNumber,
        message: message,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', `Pesan berhasil dikirim ke +${phoneNumber}!`);
            updateTestStats(true);
        } else {
            showAlert('danger', response.message || 'Gagal mengirim pesan');
            updateTestStats(false);
        }
    })
    .fail(function(xhr) {
        showAlert('danger', 'Gagal mengirim pesan test: ' + (xhr.responseJSON?.message || 'Network error'));
        updateTestStats(false);
    });
}

function sendTestNotification() {
    if (!confirm('Kirim test notifikasi ke semua admin WhatsApp?')) {
        return;
    }
    
    $.post('/admin/whatsapp/test-notification', {
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message || 'Test notifikasi berhasil dikirim ke admin!');
            updateTestStats(true);
        } else {
            showAlert('danger', response.message || 'Gagal mengirim test notifikasi');
            updateTestStats(false);
        }
    })
    .fail(function(xhr) {
        showAlert('danger', 'Gagal mengirim test notifikasi: ' + (xhr.responseJSON?.message || 'Network error'));
        updateTestStats(false);
    });
}

function updateAttendanceTemplates() {
    const templates = {
        check_in: $('textarea[name="check_in"]').val(),
        late: $('textarea[name="late"]').val(),
        absent: $('textarea[name="absent"]').val(),
        sick: $('textarea[name="sick"]').val(),
        permission: $('textarea[name="permission"]').val(),
        check_out: $('textarea[name="check_out"]').val(),
        _token: '{{ csrf_token() }}'
    };
    
    $.post('/admin/whatsapp/attendance-templates', templates)
        .done(function(response) {
            if (response.success) {
                showAlert('success', 'Template notifikasi kehadiran berhasil disimpan!');
            } else {
                showAlert('danger', response.message || 'Gagal menyimpan template');
            }
        })
        .fail(function(xhr) {
            showAlert('danger', 'Gagal menyimpan template: ' + (xhr.responseJSON?.message || 'Network error'));
        });
}

function resetTemplatesToDefault() {
    if (confirm('Apakah Anda yakin ingin mereset semua template ke pengaturan default?')) {
        $.post('/admin/whatsapp/reset-templates', {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', 'Template berhasil direset ke default. Halaman akan dimuat ulang...');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showAlert('danger', response.message || 'Gagal mereset template');
            }
        })
        .fail(function(xhr) {
            showAlert('danger', 'Gagal mereset template: ' + (xhr.responseJSON?.message || 'Network error'));
        });
    }
}
</script>

<style>
.status-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 20px;
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.status-card-connected {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 1px solid #c3e6cb;
}

.status-card-disconnected {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: 1px solid #f5c6cb;
}

.status-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: all 0.3s ease;
}

.status-icon-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    animation: pulse 2s infinite;
}

.status-icon-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.status-icon:hover {
    transform: scale(1.1);
}

.config-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.config-section:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.quick-stats {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.quick-stats:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.stat-item {
    position: relative;
    padding: 10px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: scale(1.05);
}

.stat-item:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 0;
    top: 20%;
    height: 60%;
    width: 1px;
    background: linear-gradient(to bottom, transparent, #e3e6f0, transparent);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
}

.card.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    border-radius: 15px;
    overflow: hidden;
}

.input-group .form-control {
    border-radius: 8px;
}

.input-group .input-group-text {
    border-radius: 8px;
}

.btn {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-group .btn {
    border-radius: 8px !important;
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.alert {
    border-radius: 10px;
    border: none;
}

/* Navigation tabs responsive */
.nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-radius: 8px 8px 0 0;
    padding: 8px 12px;
    margin-bottom: -2px;
    background: none;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background: #f8f9fa;
}

.nav-tabs .nav-link.active {
    background: white;
    border-color: #dee2e6 #dee2e6 #fff;
    font-weight: 600;
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.alert {
    border-radius: 10px;
    border: none;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* QR Code Styles */
.qr-code-display {
    max-width: 400px;
    margin: 0 auto;
    text-align: center;
}

.qr-code-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px;
}

.qr-code-image {
    max-width: 250px;
    width: 100%;
    height: auto;
    border-radius: 8px;
    background: white;
    padding: 10px;
    transition: all 0.3s ease;
}

.qr-code-image:hover {
    transform: scale(1.02);
}

.qr-code-info {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e3e6f0;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    /* Hide separators between stats on mobile */
    .stat-item:not(:last-child)::after {
        display: none;
    }
    
    /* Stack statistics vertically on small screens */
    .quick-stats .row {
        flex-direction: column;
    }
    
    .quick-stats .col-3 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 10px;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 10px;
    }
    
    .quick-stats .col-3:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    /* Make stat items more mobile-friendly */
    .stat-item {
        padding: 15px 10px;
        background: white;
        border-radius: 8px;
        margin-bottom: 5px;
        border: 1px solid #f0f0f0;
    }
    
    .stat-item h5 {
        font-size: 1.5rem;
    }
    
    /* Configuration section adjustments */
    .config-section {
        margin-bottom: 20px;
        padding: 15px;
    }
    
    /* Status card mobile optimization */
    .status-card {
        padding: 15px;
    }
    
    .status-card-connected,
    .status-card-disconnected {
        padding: 15px;
    }
    
    .status-card .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .status-actions {
        margin-top: 15px;
        width: 100%;
    }
    
    .status-actions .btn {
        width: 100%;
    }
    
    /* Status icon adjustments */
    .status-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 10px;
    }
    
    /* Card header mobile adjustments */
    .card-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .card-header #connection-indicator {
        margin-top: 10px;
        width: 100%;
        justify-content: space-between;
    }
    
    /* Page heading mobile */
    .d-sm-flex.align-items-center {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .btn-group {
        margin-top: 15px;
        width: 100%;
    }
    
    .btn-group .btn {
        flex: 1;
        margin: 0 2px;
    }
    
    /* Input groups on mobile */
    .input-group {
        flex-wrap: nowrap;
    }
    
    .input-group .form-control {
        font-size: 14px;
    }
    
    /* Test section mobile */
    .test-actions .row {
        margin: 0;
    }
    
    .test-actions .col-6 {
        padding: 0 2px;
    }
    
    /* Template tabs mobile */
    .nav-tabs {
        flex-wrap: wrap;
        border-bottom: none;
    }
    
    .nav-tabs .nav-item {
        margin-bottom: 5px;
        flex: 1 1 auto;
    }
    
    .nav-tabs .nav-link {
        font-size: 12px;
        padding: 8px 6px;
        text-align: center;
        border-radius: 5px;
        margin: 0 2px;
    }
    
    /* QR Code mobile adjustments */
    .qr-code-wrapper {
        padding: 10px;
    }
    
    .qr-code-image {
        max-width: 200px;
    }
    
    /* Alert mobile */
    .alert {
        font-size: 14px;
        padding: 10px;
    }
    
    /* Progress bars */
    .progress {
        height: 6px;
        margin-top: 10px;
    }
}

/* Extra small devices (phones, less than 576px) */
@media (max-width: 575.98px) {
    .container-fluid {
        padding: 10px;
    }
    
    .card {
        margin-bottom: 15px;
    }
    
    .card-body {
        padding: 15px !important;
    }
    
    .card-header {
        padding: 10px 15px !important;
    }
    
    /* Make columns full width on very small screens */
    .col-lg-8,
    .col-lg-4,
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Stack test buttons vertically */
    .test-actions .row .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 5px;
    }
    
    /* Smaller font sizes */
    h1.h3 {
        font-size: 1.5rem;
    }
    
    h5 {
        font-size: 1.1rem;
    }
    
    h6 {
        font-size: 1rem;
    }
    
    /* Compact spacing */
    .mb-4 {
        margin-bottom: 1rem !important;
    }
    
    .p-4 {
        padding: 1rem !important;
    }
    
    /* Tab content mobile */
    .tab-content textarea {
        font-size: 13px;
        min-height: 120px;
    }
    
    /* Action buttons mobile */
    .text-center .btn {
        display: block;
        width: 100%;
        margin: 5px 0;
    }
}
</style>
@endsection
