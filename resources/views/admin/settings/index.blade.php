@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Sistem</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Informasi Sekolah -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-school mr-1"></i> Informasi Sekolah
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold required">Logo Sekolah</label>
                            <div class="mb-3">
                                @if($settings['logo_path'])
                                    <img src="{{ asset($settings['logo_path']) }}" alt="Logo" class="img-thumbnail mb-2" style="height: 100px">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" 
                                       id="logo" name="logo" accept="image/*">
                                <label class="custom-file-label" for="logo">Pilih file...</label>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Format: JPG, PNG. Maksimal 2MB</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Nama Sekolah</label>
                            <input type="text" class="form-control @error('school_name') is-invalid @enderror"
                                   name="school_name" value="{{ old('school_name', $settings['school_name']) }}">
                            @error('school_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Alamat Sekolah</label>
                            <textarea class="form-control @error('school_address') is-invalid @enderror" 
                                    name="school_address" rows="3">{{ old('school_address', $settings['school_address']) }}</textarea>
                            @error('school_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('school_phone') is-invalid @enderror"
                                           name="school_phone" value="{{ old('school_phone', $settings['school_phone']) }}">
                                    @error('school_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Email Sekolah</label>
                                    <input type="email" class="form-control @error('school_email') is-invalid @enderror"
                                           name="school_email" value="{{ old('school_email', $settings['school_email']) }}">
                                    @error('school_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                          <div class="form-group">
                            <label class="font-weight-bold">Website Sekolah</label>
                            <input type="url" class="form-control @error('school_website') is-invalid @enderror"
                                   name="school_website" value="{{ old('school_website', $settings['school_website']) }}">
                            @error('school_website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Kepala Sekolah</label>
                            <input type="text" class="form-control @error('kepala_sekolah') is-invalid @enderror"
                                   name="kepala_sekolah" value="{{ old('kepala_sekolah', $settings['kepala_sekolah'] ?? '') }}">
                            @error('kepala_sekolah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">NIP Kepala Sekolah</label>
                            <input type="text" class="form-control @error('nip_kepala_sekolah') is-invalid @enderror"
                                   name="nip_kepala_sekolah" value="{{ old('nip_kepala_sekolah', $settings['nip_kepala_sekolah'] ?? '') }}">
                            @error('nip_kepala_sekolah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengaturan Sistem -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs mr-1"></i> Pengaturan Sistem
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Zona Waktu</label>
                            <select class="form-control @error('timezone') is-invalid @enderror" name="timezone">
                                @foreach($timezones as $tz)
                                    <option value="{{ $tz }}" {{ $settings['timezone'] == $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tahun Ajaran</label>
                                    <input type="number" class="form-control @error('academic_year') is-invalid @enderror"
                                           name="academic_year" value="{{ old('academic_year', $settings['academic_year']) }}">
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Semester</label>
                                    <select class="form-control @error('semester') is-invalid @enderror" name="semester">
                                        <option value="1" {{ $settings['semester'] == '1' ? 'selected' : '' }}>Ganjil</option>
                                        <option value="2" {{ $settings['semester'] == '2' ? 'selected' : '' }}>Genap</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>                        </div>                        
                        
                        <!-- Pengaturan Sistem Keterlambatan -->
                        <div class="card border-left-warning shadow h-100 py-2 mb-3">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            <i class="fas fa-clock mr-1"></i> Sistem Toleransi Keterlambatan
                                        </div>
                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="enable_late_tolerance_system" 
                                                   name="enable_late_tolerance_system" {{ $settings['enable_late_tolerance_system'] ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold" for="enable_late_tolerance_system">
                                                Aktifkan Sistem Toleransi Keterlambatan
                                            </label>
                                        </div>                                        
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Jika diaktifkan, sistem akan menggunakan pengaturan toleransi untuk guru dan batas maksimum untuk siswa.
                                            Jika dinonaktifkan, semua keterlambatan akan dicatat dengan status "hadir" namun tetap mencatat waktu keterlambatan.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="late-tolerance-settings" style="{{ $settings['enable_late_tolerance_system'] ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i> Batas Toleransi Keterlambatan Guru (Menit)
                                </label>
                                <input type="number" class="form-control @error('late_tolerance_minutes') is-invalid @enderror"
                                       name="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', $settings['late_tolerance_minutes']) }}"
                                       min="0" max="120">
                                <small class="form-text text-muted">Keterlambatan guru dalam batas toleransi tetap dihitung hadir</small>
                                @error('late_tolerance_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-user-graduate mr-1"></i> Batas Maksimum Keterlambatan Siswa (Menit)
                                </label>
                                <input type="number" class="form-control @error('max_late_minutes') is-invalid @enderror"
                                       name="max_late_minutes" value="{{ old('max_late_minutes', $settings['max_late_minutes']) }}"
                                       min="0" max="180">
                                <small class="form-text text-muted">Keterlambatan siswa melebihi batas maksimum tidak diizinkan masuk</small>
                                @error('max_late_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="enable_fingerprint" 
                                   name="enable_fingerprint" {{ $settings['enable_fingerprint'] ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_fingerprint">Aktifkan Absensi Fingerprint</label>
                        </div>                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="enable_face_recognition" 
                                   name="enable_face_recognition" {{ $settings['enable_face_recognition'] ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_face_recognition">Aktifkan Pengenalan Wajah</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mb-4">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save mr-1"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Custom file input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Toggle late tolerance settings visibility
    function toggleLateToleranceSettings() {
        const isEnabled = $('#enable_late_tolerance_system').is(':checked');
        const settingsContainer = $('#late-tolerance-settings');
        
        if (isEnabled) {
            settingsContainer.slideDown();
        } else {
            settingsContainer.slideUp();
        }
    }
    
    // Initialize toggle on page load
    $(document).ready(function() {
        toggleLateToleranceSettings();
        
        // Listen for changes
        $('#enable_late_tolerance_system').on('change', function() {
            toggleLateToleranceSettings();
        });
    });
</script>
@endpush
