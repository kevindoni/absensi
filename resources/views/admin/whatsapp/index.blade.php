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
    </div>    <!-- Test Functions Row -->
    <div class="row mb-4">
        <!-- Test Message Card -->
        <div class="col-lg-6">
            <div class="card shadow border-left-success">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-paper-plane"></i> Test Pesan Individual
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="test-phone">Nomor Tujuan:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">+62</span>
                            </div>
                            <input type="text" class="form-control" id="test-phone" placeholder="8123456789">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fas fa-users"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <h6 class="dropdown-header">Admin</h6>
                                    @foreach($adminNumbers as $number)
                                        <a class="dropdown-item" href="#" onclick="setPhoneNumber('{{ $number }}')">{{ $number }}</a>
                                    @endforeach
                                    @if(count($parentNumbers) > 0)
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Orang Tua</h6>
                                        @foreach(array_slice($parentNumbers, 0, 5) as $number)
                                            <a class="dropdown-item" href="#" onclick="setPhoneNumber('{{ $number }}')">{{ $number }}</a>
                                        @endforeach
                                        @if(count($parentNumbers) > 5)
                                            <div class="dropdown-divider"></div>
                                            <small class="dropdown-item-text text-muted">... dan {{ count($parentNumbers) - 5 }} nomor lainnya</small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">Masukkan nomor tanpa kode negara (+62)</small>
                    </div>                    
                    <div class="form-group">
                        <label for="test-message">Pesan Test:</label>
                        <textarea class="form-control" id="test-message" rows="3" placeholder="Ketik pesan test...">✅ Test koneksi WhatsApp dari {{ config('app.name', 'Sistem Absensi') }}

Sistem WhatsApp berfungsi dengan baik! 🚀

Waktu: {{ date('d/m/Y H:i') }}</textarea>
                    </div>
                    <button class="btn btn-success btn-block" onclick="sendTestMessage()">
                        <i class="fas fa-paper-plane"></i> Kirim Test Pesan
                    </button>
                </div>
            </div>
        </div>

        <!-- Connection Test Card -->
        <div class="col-lg-6">
            <div class="card shadow border-left-info">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-heartbeat"></i> Test Koneksi System
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Status System:</strong> Test koneksi dan ketersediaan service WhatsApp
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Total Nomor Terdaftar:</small>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="h5 mb-0 text-success">{{ count($adminNumbers) }}</div>
                                    <small>Admin</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="h5 mb-0 text-primary">{{ count($parentNumbers) }}</div>
                                    <small>Orang Tua</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-info btn-block" onclick="testSystemHealth()">
                        <i class="fas fa-heartbeat"></i> Test Kesehatan System
                    </button>
                    <button class="btn btn-outline-warning btn-block mt-2" onclick="sendTestNotification()">
                        <i class="fas fa-broadcast-tower"></i> Test Broadcast ke Admin
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Attendance Notification Row -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow border-left-warning">
                <div class="card-header py-3 bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-graduate"></i> Test Notifikasi Kehadiran ke Orang Tua
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Fitur ini akan mengirim notifikasi WhatsApp sesungguhnya ke nomor orang tua yang dipilih. 
                        Pastikan template sudah sesuai sebelum melakukan test.
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="test-siswa">Pilih Siswa untuk Test:</label>
                                <select class="form-control" id="test-siswa">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach(\App\Models\Siswa::with('orangtua', 'kelas')->whereHas('orangtua', function($q) { $q->whereNotNull('no_telp')->where('no_telp', '!=', ''); })->limit(20)->get() as $siswa)
                                        <option value="{{ $siswa->id }}" 
                                                data-parent="{{ $siswa->orangtua->no_telp ?? '' }}" 
                                                data-kelas="{{ $siswa->kelas->nama_kelas ?? '' }}"
                                                data-nama="{{ $siswa->nama_lengkap }}">
                                            {{ $siswa->nama_lengkap }} - {{ $siswa->kelas->nama_kelas ?? 'No Class' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted" id="parent-phone-info"></small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="test-template">Template Notifikasi:</label>
                                <select class="form-control" id="test-template">
                                    <option value="check_in">🟢 Hadir</option>
                                    <option value="late">⚠️ Terlambat</option>
                                    <option value="absent">❌ Tidak Hadir</option>
                                    <option value="sick">🏥 Sakit</option>
                                    <option value="permission">📄 Izin</option>
                                    <option value="check_out">🔴 Pulang</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="test-keterangan">Keterangan Test:</label>
                                <input type="text" class="form-control" id="test-keterangan" value="Test notifikasi dari sistem">
                                <small class="text-muted">Keterangan ini akan muncul di pesan</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div id="preview-notification" class="border rounded p-3 bg-light" style="display: none;">
                                <h6><i class="fas fa-eye"></i> Preview Pesan:</h6>
                                <div id="preview-content" class="text-monospace small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <button class="btn btn-outline-info btn-lg" onclick="previewAttendanceNotification()">
                                    <i class="fas fa-eye"></i> Preview Pesan
                                </button>
                                <button class="btn btn-warning btn-lg ml-2" onclick="sendTestAttendanceNotification()">
                                    <i class="fas fa-bell"></i> Kirim Test ke Orang Tua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Pesan Attendance Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit"></i> Template Notifikasi Kehadiran Siswa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Penting:</strong> Template ini digunakan untuk notifikasi otomatis kepada orang tua saat siswa absen.
                        Gunakan variabel seperti <code>{nama_siswa}</code>, <code>{kelas}</code>, <code>{tanggal}</code>, <code>{status}</code>, dll.
                    </div>

                    <form id="attendance-templates-form">
                        <div class="row">
                            <!-- Template Hadir -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <strong>✅ Template Hadir</strong>
                                    </div>
                                    <div class="card-body">                                        <textarea class="form-control" name="check_in" rows="6" placeholder="Template untuk siswa hadir...">{{ \App\Models\Setting::getSetting('whatsapp_template_check_in', '🟢 Notifikasi Kehadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
✅ *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas perhatiannya.') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Terlambat -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <strong>⚠️ Template Terlambat</strong>
                                    </div>
                                    <div class="card-body">                                        <textarea class="form-control" name="late" rows="6" placeholder="Template untuk siswa terlambat...">{{ \App\Models\Setting::getSetting('whatsapp_template_late', '⚠️ Notifikasi Keterlambatan dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
⏰ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon perhatian untuk kedisiplinan anak.') }}</textarea>
📝 *Keterangan*: {keterangan}

Mohon perhatian untuk kedisiplinan anak.') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Tidak Hadir -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <strong>❌ Template Tidak Hadir (Alpha)</strong>
                                    </div>
                                    <div class="card-body">                                        <textarea class="form-control" name="absent" rows="6" placeholder="Template untuk siswa tidak hadir...">{{ \App\Models\Setting::getSetting('whatsapp_template_absent', '❌ Notifikasi Ketidakhadiran dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
❌ *Status*: {status}
📝 *Keterangan*: {keterangan}

Mohon konfirmasi mengenai ketidakhadiran anak.') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Sakit -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <strong>🏥 Template Sakit</strong>
                                    </div>
                                    <div class="card-body">                                        <textarea class="form-control" name="sick" rows="6" placeholder="Template untuk siswa sakit...">{{ \App\Models\Setting::getSetting('whatsapp_template_sick', '🏥 Notifikasi Sakit dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
🏥 *Status*: {status}
📝 *Keterangan*: {keterangan}

Semoga lekas sembuh.') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Izin -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>📄 Template Izin</strong>
                                    </div>
                                    <div class="card-body">                                        <textarea class="form-control" name="permission" rows="6" placeholder="Template untuk siswa izin...">{{ \App\Models\Setting::getSetting('whatsapp_template_permission', '📄 Notifikasi Izin dari {school_name}

👤 *Nama*: {nama_siswa}
🏫 *Kelas*: {kelas}
📅 *Tanggal*: {tanggal}
📄 *Status*: {status}
📝 *Keterangan*: {keterangan}

Terima kasih atas pemberitahuannya.') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Check Out -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-dark">
                                    <div class="card-header bg-dark text-white">
                                        <strong>🔴 Template Pulang</strong>
                                    </div>
                                    <div class="card-body">                                        <textarea class="form-control" name="check_out" rows="6" placeholder="Template untuk siswa pulang...">{{ \App\Models\Setting::getSetting('whatsapp_template_check_out', '🔴 Notifikasi Pulang dari {school_name}

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
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-lg" onclick="updateAttendanceTemplates()">
                                <i class="fas fa-save"></i> Simpan Template Kehadiran
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="resetTemplatesToDefault()">
                                <i class="fas fa-undo"></i> Reset ke Default
                            </button>
                        </div>
                    </form>

                    <!-- Variabel yang tersedia -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <strong><i class="fas fa-info-circle"></i> Variabel Yang Tersedia</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><code>{school_name}</code> - Nama sekolah</li>
                                                <li><code>{nama_siswa}</code> - Nama lengkap siswa</li>
                                                <li><code>{kelas}</code> - Kelas siswa</li>
                                                <li><code>{tanggal}</code> - Tanggal absensi</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><code>{waktu}</code> - Waktu absensi</li>
                                                <li><code>{status}</code> - Status kehadiran</li>
                                                <li><code>{keterangan}</code> - Keterangan tambahan</li>
                                                <li><code>{mata_pelajaran}</code> - Mata pelajaran</li>
                                            </ul>
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
// Test alert to verify scripts are loading
console.log('WhatsApp admin scripts loaded successfully');

$(document).ready(function() {
    // Auto refresh status every 30 seconds
    setInterval(refreshStatus, 30000);
    
    // Handle siswa selection change
    $('#test-siswa').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const parentPhone = selectedOption.data('parent');
        
        if (parentPhone) {
            $('#parent-phone-info').text(`Nomor Orang Tua: ${parentPhone}`).removeClass('text-muted').addClass('text-success');
        } else {
            $('#parent-phone-info').text('').removeClass('text-success').addClass('text-muted');
        }
    });
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

// Set phone number from dropdown
function setPhoneNumber(phoneNumber) {
    // Remove the +62 prefix if present since we have +62 in the input group
    const cleanNumber = phoneNumber.replace(/^\+?62/, '');
    $('#test-phone').val(cleanNumber);
}

// Test system health and connectivity
function testSystemHealth() {
    showLoading();
    
    $.get('/admin/whatsapp/system-health')
        .done(function(response) {
            hideLoading();
            if (response.success) {
                let statusHtml = `
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> Hasil Test Kesehatan System</h6>
                        <ul class="mb-0">
                            <li>Koneksi WhatsApp: ${response.data.whatsapp_connected ? '✅ Terhubung' : '❌ Tidak Terhubung'}</li>
                            <li>Gateway Status: ${response.data.gateway_status ? '✅ Online' : '❌ Offline'}</li>
                            <li>Database: ${response.data.database_connected ? '✅ Terhubung' : '❌ Error'}</li>
                            <li>Total Admin: ${response.data.admin_count} nomor</li>
                            <li>Total Orang Tua: ${response.data.parent_count} nomor</li>
                        </ul>
                    </div>
                `;
                
                // Show the result in a modal or alert
                showAlert('info', statusHtml);
            } else {
                showAlert('danger', response.message || 'Test kesehatan system gagal');
            }
        })
        .fail(function(xhr) {
            hideLoading();
            showAlert('danger', 'Gagal melakukan test kesehatan system: ' + (xhr.responseJSON?.message || 'Network error'));
        });
}

// Update attendance templates
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
    
    showLoading();
    
    $.post('/admin/whatsapp/attendance-templates', templates)
        .done(function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Template notifikasi kehadiran berhasil disimpan!');
            } else {
                showAlert('danger', response.message || 'Gagal menyimpan template');
            }
        })
        .fail(function(xhr) {
            hideLoading();
            showAlert('danger', 'Gagal menyimpan template: ' + (xhr.responseJSON?.message || 'Network error'));
        });
}

// Reset templates to default
function resetTemplatesToDefault() {
    if (confirm('Apakah Anda yakin ingin mereset semua template ke pengaturan default? Perubahan yang belum disimpan akan hilang.')) {
        showLoading();
        
        $.post('/admin/whatsapp/reset-templates', {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            hideLoading();
            if (response.success) {
                // Reload the page to show default templates
                showAlert('success', 'Template berhasil direset ke default. Halaman akan dimuat ulang...');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showAlert('danger', response.message || 'Gagal mereset template');
            }
        })
        .fail(function(xhr) {
            hideLoading();
            showAlert('danger', 'Gagal mereset template: ' + (xhr.responseJSON?.message || 'Network error'));
        });
    }
}

// Enhanced sendTestMessage with better phone formatting
function sendTestMessage() {
    let phoneNumber = $('#test-phone').val().trim();
    const message = $('#test-message').val().trim();
    
    if (!phoneNumber || !message) {
        showAlert('warning', 'Nomor tujuan dan pesan harus diisi');
        return;
    }
    
    // Format phone number properly
    phoneNumber = phoneNumber.replace(/[^\d]/g, ''); // Remove non-digits
    
    // Add +62 prefix if not present
    if (!phoneNumber.startsWith('62')) {
        if (phoneNumber.startsWith('0')) {
            phoneNumber = '62' + phoneNumber.substring(1);
        } else if (phoneNumber.startsWith('8')) {
            phoneNumber = '62' + phoneNumber;
        }
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
            showAlert('success', `Pesan berhasil dikirim ke +${phoneNumber}!`);
        } else {
            showAlert('danger', response.message || 'Gagal mengirim pesan');
        }
    })
    .fail(function(xhr) {
        hideLoading();
        showAlert('danger', 'Gagal mengirim pesan test: ' + (xhr.responseJSON?.message || 'Network error'));
    });
}

// Send test notification to admins
function sendTestNotification() {
    if (!confirm('Kirim test notifikasi ke semua admin WhatsApp?')) {
        return;
    }
    
    showLoading();
    
    $.post('/admin/whatsapp/test-notification', {
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            showAlert('success', response.message || 'Test notifikasi berhasil dikirim ke admin!');
        } else {
            showAlert('danger', response.message || 'Gagal mengirim test notifikasi');
        }
    })
    .fail(function(xhr) {
        hideLoading();
        showAlert('danger', 'Gagal mengirim test notifikasi: ' + (xhr.responseJSON?.message || 'Network error'));
    });
}

// Preview attendance notification
function previewAttendanceNotification() {
    const siswaId = $('#test-siswa').val();
    const templateType = $('#test-template').val();
    const keterangan = $('#test-keterangan').val().trim();
    
    if (!siswaId || !templateType) {
        showAlert('warning', 'Pilih siswa dan template notifikasi terlebih dahulu');
        return;
    }
    
    const siswa = $('#test-siswa option:selected');
    const namaSiswa = siswa.data('nama');
    const kelasSiswa = siswa.data('kelas');
    const parentPhone = siswa.data('parent');
    
    let message = '';
    switch (templateType) {
        case 'check_in':
            message = `🟢 Notifikasi Kehadiran dari {school_name}

👤 *Nama*: ${namaSiswa}
🏫 *Kelas*: ${kelasSiswa}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
📚 *Mata Pelajaran*: {mata_pelajaran}
🕐 *Jam Ke*: {jam_ke} ({jam_mulai}-{jam_selesai})
✅ *Status*: Hadir
📝 *Keterangan*: ${keterangan}

Terima kasih atas perhatiannya.`;
            break;
        case 'late':
            message = `⚠️ Notifikasi Keterlambatan dari {school_name}

👤 *Nama*: ${namaSiswa}
🏫 *Kelas*: ${kelasSiswa}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
📚 *Mata Pelajaran*: {mata_pelajaran}
🕐 *Jam Ke*: {jam_ke} ({jam_mulai}-{jam_selesai})
⏰ *Status*: Terlambat
📝 *Keterangan*: ${keterangan}

Mohon perhatian untuk kedisiplinan anak.`;
            break;
        case 'absent':
            message = `❌ Notifikasi Ketidakhadiran dari {school_name}

👤 *Nama*: ${namaSiswa}
🏫 *Kelas*: ${kelasSiswa}
📅 *Tanggal*: {tanggal}
📚 *Mata Pelajaran*: {mata_pelajaran}
🕐 *Jam Ke*: {jam_ke} ({jam_mulai}-{jam_selesai})
❌ *Status*: Tidak Hadir
📝 *Keterangan*: ${keterangan}

Mohon konfirmasi mengenai ketidakhadiran anak.`;
            break;
        case 'sick':
            message = `🏥 Notifikasi Sakit dari {school_name}

👤 *Nama*: ${namaSiswa}
🏫 *Kelas*: ${kelasSiswa}
📅 *Tanggal*: {tanggal}
🏥 *Status*: Sakit
📝 *Keterangan*: ${keterangan}

Semoga lekas sembuh.`;
            break;
        case 'permission':
            message = `📄 Notifikasi Izin dari {school_name}

👤 *Nama*: ${namaSiswa}
🏫 *Kelas*: ${kelasSiswa}
📅 *Tanggal*: {tanggal}
📄 *Status*: Izin
📝 *Keterangan*: ${keterangan}

Terima kasih atas pemberitahuannya.`;
            break;
        case 'check_out':
            message = `🔴 Notifikasi Pulang dari {school_name}

👤 *Nama*: ${namaSiswa}
🏫 *Kelas*: ${kelasSiswa}
📅 *Tanggal*: {tanggal}
🕐 *Waktu*: {waktu}
🔴 *Status*: Pulang
📝 *Keterangan*: ${keterangan}

Anak telah pulang dengan selamat.`;
            break;
        default:
            message = 'Template not recognized';
    }
      // Replace placeholders with actual values
    const currentDate = new Date().toLocaleDateString('id-ID');
    const currentTime = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
    const schoolName = '{{ config("app.name", "Sistem Absensi") }}';
    
    message = message
        .replace('{school_name}', schoolName)
        .replace('{tanggal}', currentDate)
        .replace('{waktu}', currentTime);
    
    // Show preview with additional info
    const previewHtml = `
        <div class="mb-2">
            <strong>Kepada:</strong> ${parentPhone}<br>
            <strong>Siswa:</strong> ${namaSiswa} (${kelasSiswa})<br>
            <strong>Template:</strong> ${templateType.toUpperCase()}
        </div>
        <div class="border-top pt-2">
            <pre style="white-space: pre-wrap; font-family: inherit;">${message}</pre>
        </div>
    `;
    
    $('#preview-content').html(previewHtml);
    $('#preview-notification').show();
}

// Send test attendance notification
function sendTestAttendanceNotification() {
    const siswaId = $('#test-siswa').val();
    const templateType = $('#test-template').val();
    const keterangan = $('#test-keterangan').val().trim();
    
    if (!siswaId || !templateType) {
        showAlert('warning', 'Pilih siswa dan template notifikasi terlebih dahulu');
        return;
    }
    
    const siswa = $('#test-siswa option:selected');
    const parentPhone = siswa.data('parent');
    const siswaName = siswa.data('nama');
    
    if (!parentPhone) {
        showAlert('warning', 'Nomor orang tua tidak ditemukan untuk siswa ini');
        return;
    }
    
    if (confirm(`Anda akan mengirim notifikasi ${templateType} ke nomor ${parentPhone} untuk siswa ${siswaName}. Lanjutkan?`)) {
        showLoading();
        
        $.post('/admin/whatsapp/test-attendance-notification', {
            siswa_id: siswaId,
            template_type: templateType,
            keterangan: keterangan,
            time: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}),
            date: new Date().toLocaleDateString('id-ID'),
            location: 'Sekolah',
            late_duration: '15 menit',
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', `✅ ${response.message} ke nomor ${parentPhone}!`);
            } else {
                showAlert('danger', response.message || 'Gagal mengirim notifikasi kehadiran');
            }
        })
        .fail(function(xhr) {
            hideLoading();
            let message = 'Gagal mengirim notifikasi kehadiran';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message += ': ' + xhr.responseJSON.message;
            } else if (xhr.status === 0) {
                message += ': Tidak dapat terhubung ke server';
            }
            showAlert('danger', message);
        });
    }
}
</script>
@endsection
