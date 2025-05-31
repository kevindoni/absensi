@extends('layouts.admin')

@section('title', 'Uji Pengiriman Notifikasi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Uji Pengiriman Notifikasi</h1>
        <a href="{{ route('admin.notifications.settings') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Pengaturan
        </a>
    </div>
    
    <!-- Status Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow h-100 py-2 {{ $exitCode === 0 ? 'border-left-success' : 'border-left-danger' }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 {{ $exitCode === 0 ? 'text-success' : 'text-danger' }}">
                                Status Pengujian
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $exitCode === 0 ? 'Berhasil' : 'Gagal' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $exitCode === 0 ? 'fa-check' : 'fa-times' }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <!-- System Notification Alert -->
    <div class="alert alert-success mb-4">
        <h5><i class="fas fa-bell"></i> Notifikasi Sistem</h5>
        <p>Notifikasi test berhasil dikirim ke dashboard admin. Silahkan periksa ikon notifikasi di bagian atas halaman.</p>
        <a href="#" class="btn btn-sm btn-outline-success" onclick="$('#alertsDropdown').trigger('click'); return false;">
            <i class="fas fa-external-link-alt"></i> Lihat Notifikasi
        </a>
    </div>
      <!-- Settings Status -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="card shadow h-100 py-2 border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Email
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $settings['email_notifications'] ? 'Aktif' : 'Nonaktif' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $settings['email_notifications'] ? 'fa-check-circle' : 'fa-times-circle' }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-md-6">
            <div class="card shadow h-100 py-2 border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Notifikasi Ketidakhadiran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $settings['notify_parent_on_absence'] ? 'Aktif' : 'Nonaktif' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $settings['notify_parent_on_absence'] ? 'fa-check-circle' : 'fa-times-circle' }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Command Output -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Output Pengujian</h6>
        </div>        <div class="card-body">
            <div class="bg-dark text-light p-3 rounded" style="font-family: monospace; white-space: pre-wrap;">{{ $output }}</div>
            
            @if(!$settings['email_notifications'])
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> Notifikasi Email sedang dinonaktifkan. Aktifkan untuk dapat mengirim notifikasi.
                </div>
            @endif
            
            @if(!$settings['notify_parent_on_absence'])
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> Notifikasi ketidakhadiran sedang dinonaktifkan. Aktifkan untuk dapat mengirim notifikasi ketika siswa tidak hadir.
                </div>
            @endif
        </div>
    </div>
    
    <!-- Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Tindakan</h5>
                    <p>Untuk mengubah pengaturan notifikasi, silahkan kembali ke halaman pengaturan notifikasi.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.notifications.settings') }}" class="btn btn-primary">
                                <i class="fas fa-cog"></i> Kembali ke Pengaturan Notifikasi
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.notifications.test') }}" class="btn btn-info">
                                <i class="fas fa-sync"></i> Uji Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
