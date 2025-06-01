@extends('layouts.admin')

@section('title', 'Pengaturan Notifikasi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Notifikasi</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Form untuk pengaturan notifikasi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Notifikasi</h6>
            <div>
                <a href="{{ route('admin.notifications.test') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-bell"></i> Kirim Notifikasi Test
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-info ml-2">
                    <i class="fas fa-list"></i> Lihat Semua Notifikasi
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.notifications.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-4">Pengaturan Umum</h5>                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="email_notifications" name="email_notifications" 
                                       value="1" {{ $settings['email_notifications'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="email_notifications">
                                    Aktifkan Notifikasi Email
                                </label>
                            </div>
                            <small class="form-text text-muted mt-1">
                                Kirim notifikasi melalui email ke orang tua/wali siswa.
                            </small>
                        </div>                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="notify_parent_on_absence" name="notify_parent_on_absence" 
                                       value="1" {{ $settings['notify_parent_on_absence'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="notify_parent_on_absence">
                                    Kirim Notifikasi ke Orang Tua untuk Ketidakhadiran
                                </label>
                            </div>
                            <small class="form-text text-muted mt-1">
                                Kirim notifikasi ke orang tua/wali siswa ketika siswa tidak hadir (alpha, izin, atau sakit).
                            </small>
                        </div>

                        <h5 class="text-success mb-4 mt-4">Pengaturan WhatsApp</h5>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enable_whatsapp_attendance_notifications" name="enable_whatsapp_attendance_notifications" 
                                       value="1" {{ $settings['enable_whatsapp_attendance_notifications'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="enable_whatsapp_attendance_notifications">
                                    Aktifkan Notifikasi WhatsApp untuk Absensi
                                </label>
                            </div>
                            <small class="form-text text-muted mt-1">
                                Kirim notifikasi WhatsApp otomatis ke orang tua/wali siswa setiap kali absensi dicatat (hadir, terlambat, tidak hadir, dll).
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <h5 class="text-primary mb-4">Template Pesan</h5>
                        <div class="form-group">
                            <label for="notification_email_template">Template Email</label>
                            <textarea class="form-control" id="notification_email_template" name="notification_email_template" 
                                rows="4">{{ $settings['notification_email_template'] }}</textarea>
                            <small class="form-text text-muted mt-1">
                                Gunakan variabel berikut: {nama_ortu}, {nama_siswa}, {tanggal}, {status}, {kelas}, {waktu}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.notifications.test') }}" class="btn btn-info">
                            <i class="fas fa-bell"></i> Uji Pengiriman Notifikasi
                        </a>
                    </div>
                </div>
                <div class="mt-4 card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Cara Penggunaan</h5>
                        <p>Anda dapat mengirimkan notifikasi ke orang tua/wali siswa ketika siswa tidak hadir di sekolah. Berikut adalah beberapa pilihan yang tersedia:</p>                        <ul>
                            <li><strong>Email:</strong> Notifikasi akan dikirim ke alamat email orang tua/wali yang terdaftar di sistem.</li>
                            <li><strong>Template Pesan:</strong> Anda dapat menyesuaikan pesan yang dikirim dengan menggunakan variabel yang disediakan.</li>
                        </ul>
                        <p>Untuk menguji sistem notifikasi, klik tombol "Uji Pengiriman Notifikasi".</p>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> <strong>Penting:</strong> Pastikan setiap siswa memiliki data orang tua yang terkait dan data orang tua memiliki alamat email dan/atau nomor telepon yang valid untuk menerima notifikasi.
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Notification settings scripts can be added here if needed
</script>
@endpush
