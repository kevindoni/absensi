@extends('layouts.admin')

@section('title', 'Pengaturan QR Code')

@section('styles')
<style>
    .card-title-icon {
        margin-right: 10px;
    }
    .setting-group {
        padding: 15px;
        border-left: 4px solid #4e73df;
        background-color: #f8f9fc;
        margin-bottom: 20px;
    }
    .setting-group h5 {
        color: #4e73df;
    }
    .help-icon {
        cursor: pointer;
        color: #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan QR Code</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.qrcode.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Analytics
            </a>
            <a href="{{ route('admin.qrcode.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-cogs card-title-icon"></i> Konfigurasi QR Code
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.qrcode.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="setting-group">
                    <h5><i class="fas fa-clock"></i> Masa Berlaku QR Code</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qr_validity_period_type">Jenis Masa Berlaku</label>
                                <select class="form-control" id="qr_validity_period_type" name="qr_validity_period_type">
                                    <option value="days" {{ ($settings->qr_validity_period_type ?? '') == 'days' ? 'selected' : '' }}>Berdasarkan Hari</option>
                                    <option value="permanent" {{ ($settings->qr_validity_period_type ?? '') == 'permanent' ? 'selected' : '' }}>Permanen</option>
                                    <option value="daily" {{ ($settings->qr_validity_period_type ?? '') == 'daily' ? 'selected' : '' }}>Harian (Reset setiap hari)</option>
                                </select>
                                <small class="form-text text-muted">Tentukan jenis masa berlaku QR Code</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="validity_days_group">
                                <label for="qr_validity_period">Masa Berlaku QR Code (hari)</label>
                                <input type="number" class="form-control" id="qr_validity_period" name="qr_validity_period" 
                                    min="0" value="{{ $settings->qr_validity_period ?? 30 }}">
                                <small class="form-text text-muted" id="qr_validity_hint">Setelah periode ini, QR Code akan otomatis tidak valid. Nilai 0 berarti berlaku selamanya.</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="setting-group">
                    <h5><i class="fas fa-sync"></i> Reset dan Pembaruan</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qr_auto_reset">Reset QR Code Otomatis</label>
                                <select class="form-control" id="qr_auto_reset" name="qr_auto_reset">
                                    <option value="1" {{ ($settings->qr_auto_reset ?? false) ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !($settings->qr_auto_reset ?? false) ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                <small class="form-text text-muted">Jika diaktifkan, sistem akan membuat QR Code baru secara otomatis saat masa berlaku habis. Admin perlu mencetak ulang QR Code setelah di-reset.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="require_active_academic_year">Memerlukan Tahun Ajaran Aktif</label>
                                <select class="form-control" id="require_active_academic_year" name="require_active_academic_year">
                                    <option value="1" {{ ($settings->require_active_academic_year ?? true) ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ !($settings->require_active_academic_year ?? true) ? 'selected' : '' }}>Tidak</option>
                                </select>
                                <small class="form-text text-muted">QR Code hanya valid pada tahun ajaran aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="setting-group">
                    <h5><i class="fas fa-shield-alt"></i> Validasi QR Code</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="allow_multiple_scans">Izinkan Scan Berulang</label>
                                <select class="form-control" id="allow_multiple_scans" name="allow_multiple_scans">
                                    <option value="1" {{ ($settings->allow_multiple_scans ?? true) ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ !($settings->allow_multiple_scans ?? true) ? 'selected' : '' }}>Tidak</option>
                                </select>
                                <small class="form-text text-muted">QR Code dapat dipindai beberapa kali dalam satu hari</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="validate_by_schedule">Validasi Berdasarkan Jadwal</label>
                                <select class="form-control" id="validate_by_schedule" name="validate_by_schedule">
                                    <option value="1" {{ ($settings->validate_by_schedule ?? true) ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ !($settings->validate_by_schedule ?? true) ? 'selected' : '' }}>Tidak</option>
                                </select>
                                <small class="form-text text-muted">QR Code hanya valid sesuai dengan jadwal mengajar</small>
                            </div>
                        </div>
                    </div>
                </div>                  <div class="setting-group">
                    <h5><i class="fas fa-exclamation-triangle"></i> Pengaturan Keterlambatan</h5>
                    
                    <!-- Status Sistem Toleransi -->
                    <div class="alert {{ $mainSettings['enable_late_tolerance_system'] ? 'alert-success' : 'alert-warning' }}">
                        <i class="fas fa-{{ $mainSettings['enable_late_tolerance_system'] ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                        <strong>Status Sistem Toleransi:</strong> 
                        {{ $mainSettings['enable_late_tolerance_system'] ? 'AKTIF' : 'NONAKTIF' }}
                        <br>
                        @if($mainSettings['enable_late_tolerance_system'])
                            <small>Sistem toleransi keterlambatan sedang aktif dan akan diterapkan pada absensi QR dan manual.</small>                        @else
                            <small>Sistem toleransi keterlambatan dinonaktifkan. Semua keterlambatan akan dicatat dengan status "hadir" namun tetap mencatat waktu keterlambatan.</small>
                        @endif
                    </div>
                    
                    @if($mainSettings['enable_late_tolerance_system'])
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Informasi:</strong> Pengaturan keterlambatan dikelola secara terpusat di halaman <strong>Pengaturan Sistem</strong>.
                            <br>
                            <ul class="mb-0 mt-2">
                                <li><strong>Toleransi Guru:</strong> Keterlambatan guru dalam batas ini tetap dihitung sebagai "hadir"</li>
                                <li><strong>Batas Maksimum Siswa:</strong> Siswa tidak diizinkan masuk jika terlambat melebihi batas ini</li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Peringatan:</strong> Sistem toleransi keterlambatan sedang <strong>DINONAKTIFKAN</strong>.
                            <br>
                            <small>Untuk mengaktifkan kembali, silakan ubah pengaturan di halaman <strong>Pengaturan Sistem</strong>.</small>
                        </div>
                    @endif
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-chalkboard-teacher mr-1"></i> Toleransi Keterlambatan Guru (menit)</label>
                                <input type="text" class="form-control" value="{{ $mainSettings['late_tolerance_minutes'] }}" readonly>
                                <small class="form-text text-muted">Keterlambatan guru dalam batas toleransi tetap dihitung hadir</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user-graduate mr-1"></i> Batas Maksimum Keterlambatan Siswa (menit)</label>
                                <input type="text" class="form-control" value="{{ $mainSettings['max_late_minutes'] }}" readonly>
                                <small class="form-text text-muted">Keterlambatan siswa melebihi batas ini tidak diizinkan masuk</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-cogs mr-1"></i> Ubah Pengaturan Keterlambatan
                        </a>
                    </div>
                </div>
                
                <div class="text-right">
                    <a href="{{ route('admin.qrcode.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Display info about active academic year if needed -->
    @if($academicYear)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Tahun Ajaran Aktif Saat Ini: <strong>{{ $academicYear->nama }}</strong> ({{ $academicYear->tahun_mulai }}/{{ $academicYear->tahun_selesai }})
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-circle"></i> Tidak ada Tahun Ajaran aktif saat ini. QR Code tidak akan berfungsi jika pengaturan "Memerlukan Tahun Ajaran Aktif" diaktifkan.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {        // Toggle validity period days input based on type selection
        function toggleValidityDaysGroup() {
            const type = $('#qr_validity_period_type').val();
            
            // Always show the field first to avoid display issues
            $('#validity_days_group').show();
            
            if (type === 'days') {
                $('#qr_validity_period').val('30');
                $('#qr_validity_period').prop('readonly', false);
                $('#qr_validity_hint').text('Setelah periode ini, QR Code akan otomatis tidak valid.');
            } else if (type === 'permanent') {
                $('#qr_validity_period').val('0');
                $('#qr_validity_period').prop('readonly', true);
                $('#qr_validity_hint').text('Nilai 0 berarti QR Code berlaku seumur sekolah (selama siswa terdaftar).');
            } else { // daily
                $('#validity_days_group').hide();
                $('#qr_validity_period').prop('readonly', false);
            }
        }
        
        // Initial toggle - call this after a short delay to ensure DOM is fully processed
        setTimeout(function() {
            toggleValidityDaysGroup();
        }, 100);
        
        // Event listeners
        $('#qr_validity_period_type').on('change', toggleValidityDaysGroup);
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Before form submit
        $('form').on('submit', function() {
            // Enable all fields before submitting to make sure they're included
            $('#qr_validity_period').prop('readonly', false);
        });
    });
</script>
@endsection
