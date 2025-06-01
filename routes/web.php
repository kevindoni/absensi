<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\OrangtuaController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\KelasController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// CSRF Token refresh route for AJAX
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

// Debug session route (only in local environment)
Route::get('/debug-session', function () {
    if (config('app.env') !== 'local') {
        abort(404);
    }
    return view('debug.session');
})->name('debug.session');

// Test login route (only in local environment)
Route::get('/test-login', function () {
    if (config('app.env') !== 'local') {
        abort(404);
    }
    return view('test-login');
})->name('test.login');

// Guest routes (for login)
Route::middleware('guest:admin,guru,siswa,orangtua')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    // Specific login routes for different user types
    Route::get('auth/guru/login', [LoginController::class, 'showGuruLoginForm'])->name('guru.login');
    // Add other login form routes here as needed
});

// Admin login routes
Route::get('/auth/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/auth/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');

// Logout route (no guest middleware)
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Authentication Routes - Multi Auth
Route::prefix('auth')->group(function () {
    // Admin Routes
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
    Route::post('/admin/logout', [LoginController::class, 'adminLogout'])->name('admin.logout');
    
    // Guru Routes
    Route::get('/guru/login', [LoginController::class, 'showGuruLoginForm'])->name('guru.login');
    Route::post('/guru/login', [LoginController::class, 'guruLogin'])->name('guru.login.submit');
    Route::post('/guru/logout', [LoginController::class, 'guruLogout'])->name('guru.logout');
    
    // Siswa Routes
    Route::get('/siswa/login', [LoginController::class, 'showSiswaLoginForm'])->name('siswa.login');
    Route::post('/siswa/login', [LoginController::class, 'siswaLogin'])->name('siswa.login.submit');
    Route::post('/siswa/logout', [LoginController::class, 'siswaLogout'])->name('siswa.logout');
    
    // Orangtua Routes
    Route::get('/orangtua/login', [LoginController::class, 'showOrangtuaLoginForm'])->name('orangtua.login');
    Route::post('/orangtua/login', [LoginController::class, 'orangtuaLogin'])->name('orangtua.login.submit');
    Route::post('/orangtua/logout', [LoginController::class, 'orangtuaLogout'])->name('orangtua.logout');
});

// Redirect from generic login to welcome page with login options
Route::get('/login', function () {
    return redirect()->route('welcome');
})->name('login');

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth:admin,guru,siswa,orangtua'])->name('dashboard');

// Admin Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    
    // Guru routes with import
    Route::resource('guru', GuruController::class);
    Route::post('guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::get('guru/template/download', [GuruController::class, 'downloadTemplate'])->name('guru.template');
    
    // Siswa routes with import
    Route::resource('siswa', App\Http\Controllers\Admin\SiswaController::class);
    Route::get('/siswa/search/data', [App\Http\Controllers\Admin\SiswaController::class, 'search'])->name('siswa.search');
    Route::post('/siswa/import', [App\Http\Controllers\Admin\SiswaController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/template/download', [App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])->name('siswa.template');
    Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('siswa/template/download', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
    
    Route::resource('orangtua', OrangtuaController::class);
    Route::get('/profil', [DashboardController::class, 'adminProfile'])->name('profil');
    Route::get('/laporan', [DashboardController::class, 'adminLaporan'])->name('laporan.index');
    
    // Kelas routes
    Route::resource('kelas', KelasController::class);
    Route::get('kelas/template/download', [KelasController::class, 'downloadTemplate'])->name('kelas.template.download');
    Route::post('kelas/import', [KelasController::class, 'import'])->name('kelas.import');
    
    // Laporan routes
    Route::get('/laporan', [App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');
    
        // QR Code Routes (consolidated)
    Route::get('/qrcode', [App\Http\Controllers\QrController::class, 'index'])->name('qrcode.index');
    Route::get('/siswa/{siswaId}/qrcode', [App\Http\Controllers\QrController::class, 'generateSiswaQr'])->name('siswa.qrcode');
    Route::get('/kelas/{kelasId}/qrcodes', [App\Http\Controllers\QrController::class, 'generateClassQr'])->name('kelas.qrcodes');
    Route::get('/kelas/{kelasId}/qrcodes/print', [App\Http\Controllers\QrController::class, 'printPreviewQrCodes'])->name('kelas.qrcodes.print');
    Route::patch('/siswa/{siswaId}/reset-qrcode', [App\Http\Controllers\QrController::class, 'resetQrCode'])->name('qrcode.reset');
    Route::post('/qrcode/bulk-reset', [App\Http\Controllers\QrController::class, 'bulkResetQrCode'])->name('qrcode.bulk-reset');
    Route::get('/qrcode/validate', function() {
        return view('admin.qrcode.validate');
    })->name('qrcode.validate');
    Route::get('/qrcode/validate/{qrToken}', [App\Http\Controllers\QrController::class, 'validateQrCode'])->name('qrcode.validate.process');
    Route::get('/qrcode/validate-enhanced', function() {
        return view('admin.qrcode.validate-enhanced');
    })->name('qrcode.validate.enhanced');
    Route::get('/qrcode/test', [App\Http\Controllers\QrController::class, 'generateTestQr'])->name('qrcode.test');
    Route::get('/qrcode/settings', [App\Http\Controllers\QrController::class, 'showQrSettings'])->name('qrcode.settings');
    Route::post('/qrcode/settings', [App\Http\Controllers\QrController::class, 'updateQrSettings'])->name('qrcode.settings.update');
    Route::put('/qrcode/settings', [App\Http\Controllers\QrController::class, 'updateQrSettings']);

    // Pelajaran routes
    Route::resource('pelajaran', App\Http\Controllers\Admin\PelajaranController::class);
    Route::post('pelajaran/import', [App\Http\Controllers\Admin\PelajaranController::class, 'import'])->name('pelajaran.import');
    Route::get('pelajaran/template/download', [App\Http\Controllers\Admin\PelajaranController::class, 'downloadTemplate'])->name('pelajaran.template');
    
    // Jadwal Mengajar routes
    Route::resource('jadwal', App\Http\Controllers\Admin\JadwalMengajarController::class);
    Route::get('guru/{id}/jadwal', [App\Http\Controllers\Admin\JadwalMengajarController::class, 'showByGuru'])->name('guru.jadwal');
    Route::get('kelas/{id}/jadwal', [App\Http\Controllers\Admin\JadwalMengajarController::class, 'showByKelas'])->name('kelas.jadwal');
    
    // Academic Year routes
    Route::resource('academic-year', App\Http\Controllers\Admin\AcademicYearController::class);
    Route::patch('academic-year/{academicYear}/set-active', [App\Http\Controllers\Admin\AcademicYearController::class, 'setActive'])->name('academic-year.set-active');
    Route::get('academic-year-migrate', [App\Http\Controllers\Admin\AcademicYearController::class, 'showMigrateForm'])->name('academic-year.migrate-form');
    Route::post('academic-year-migrate', [App\Http\Controllers\Admin\AcademicYearController::class, 'migrateStudents'])->name('academic-year.migrate-students');
    
    // Settings routes
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Notification settings routes
    Route::get('/notifications/settings', [App\Http\Controllers\Admin\NotificationsController::class, 'settings'])->name('notifications.settings');
    Route::post('/notifications/settings', [App\Http\Controllers\Admin\NotificationsController::class, 'updateSettings'])->name('notifications.update');
    Route::get('/notifications/test', [App\Http\Controllers\Admin\NotificationsController::class, 'testNotification'])->name('notifications.test');
    Route::post('/notifications/test-wa-connection', [App\Http\Controllers\Admin\NotificationsController::class, 'testWaConnection'])->name('notifications.test-wa-connection');
    
    // Admin Notifications Routes
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

// Guru Routes
Route::middleware(['auth:guru'])->name('guru.')->prefix('guru')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [App\Http\Controllers\Guru\DashboardController::class, 'profil'])->name('profil');
    Route::patch('/profil', [App\Http\Controllers\Guru\ProfilController::class, 'update'])->name('profil.update');
    Route::patch('/profil/password', [App\Http\Controllers\Guru\ProfilController::class, 'updatePassword'])->name('profil.updatePassword');
    Route::patch('/profil/photo', [App\Http\Controllers\Guru\ProfilController::class, 'updatePhoto'])->name('profil.updatePhoto');
    
    // Absensi Routes
    Route::resource('absensi', App\Http\Controllers\Guru\AbsensiController::class);
    Route::get('absensi/take/{jadwal}', [App\Http\Controllers\Guru\AbsensiController::class, 'takeAttendance'])->name('absensi.takeAttendance');
    Route::patch('absensi/detail/{detail}/update-status', [App\Http\Controllers\Guru\AbsensiController::class, 'updateStatus'])->name('absensi.updateStatus');
    Route::post('absensi/process-qr', [App\Http\Controllers\Guru\AbsensiController::class, 'processQr'])->name('absensi.processQr');
    Route::post('absensi/manual-attendance', [App\Http\Controllers\Guru\AbsensiController::class, 'manualAttendance'])->name('absensi.manualAttendance');
    Route::get('absensi/attendance-data/{jadwal}', [App\Http\Controllers\Guru\AbsensiController::class, 'getAttendanceData'])->name('absensi.getAttendanceData');
    Route::patch('absensi/{absensi}/complete', [App\Http\Controllers\Guru\AbsensiController::class, 'completeAttendance'])->name('absensi.complete');
    
    // Absensi Riwayat Route
    Route::get('/absensi/riwayat', [App\Http\Controllers\Guru\AbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    
    // Presensi Routes for Guru
    Route::get('presensi', [App\Http\Controllers\Guru\PresensiController::class, 'index'])->name('presensi.index');
    Route::post('presensi/check-in', [App\Http\Controllers\Guru\PresensiController::class, 'checkIn'])->name('presensi.checkIn');
    Route::post('presensi/check-out', [App\Http\Controllers\Guru\PresensiController::class, 'checkOut'])->name('presensi.checkOut');
    Route::get('presensi/report', [App\Http\Controllers\Guru\PresensiController::class, 'report'])->name('presensi.report');
    
    // Search Routes
    Route::get('/search/siswa', [App\Http\Controllers\Guru\IzinController::class, 'searchSiswa'])->name('search.siswa');
    Route::get('/search/students-by-class', [App\Http\Controllers\Guru\IzinController::class, 'getStudentsByClass'])->name('search.students-by-class');
    
    // Izin/Permission Routes for Guru
    Route::resource('izin', App\Http\Controllers\Guru\IzinController::class);
    
    // Report routes - update the route to point to the right methods
    Route::get('/laporan', [App\Http\Controllers\Guru\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [App\Http\Controllers\Guru\LaporanController::class, 'export'])->name('laporan.export');
    Route::get('/laporan/{laporan}', [App\Http\Controllers\Guru\LaporanController::class, 'show'])->name('laporan.show');
    
    // Jadwal Routes for Personal Schedule
    Route::get('/jadwal', [App\Http\Controllers\Guru\JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/data', [App\Http\Controllers\Guru\JadwalController::class, 'getScheduleData'])->name('jadwal.data');
    Route::get('/jadwal/weekly', [App\Http\Controllers\Guru\JadwalController::class, 'weekly'])->name('jadwal.weekly');
    Route::get('/jadwal/preview-pdf', [App\Http\Controllers\Guru\JadwalController::class, 'previewPdf'])->name('jadwal.preview-pdf');
    Route::get('/jadwal/preview-compact-pdf', [App\Http\Controllers\Guru\JadwalController::class, 'previewCompactPdf'])->name('jadwal.preview-compact-pdf');
    Route::get('/jadwal/preview-weekly-pdf', [App\Http\Controllers\Guru\JadwalController::class, 'previewWeeklyPdf'])->name('jadwal.preview-weekly-pdf');
    Route::get('/jadwal/export-pdf', [App\Http\Controllers\Guru\JadwalController::class, 'exportPdf'])->name('jadwal.export-pdf');
    Route::get('/jadwal/export-compact-pdf', [App\Http\Controllers\Guru\JadwalController::class, 'exportCompactPdf'])->name('jadwal.export-compact-pdf');
    Route::get('/jadwal/export-weekly-pdf', [App\Http\Controllers\Guru\JadwalController::class, 'exportWeeklyPdf'])->name('jadwal.export-weekly-pdf');
    Route::get('/jadwal/{id}', [App\Http\Controllers\Guru\JadwalController::class, 'show'])->name('jadwal.show');
    
    // Absensi detail route
    Route::get('/absensi/{jadwal}/{tanggal}/detail', [App\Http\Controllers\Guru\AbsensiController::class, 'detail'])->name('absensi.detail');
});

// Siswa Routes
Route::middleware(['auth:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'siswaDashboard'])->name('dashboard');
    Route::get('/profil', [DashboardController::class, 'siswaProfile'])->name('profil');
    
    // Add QR Code routes
    Route::get('/qrcode', [App\Http\Controllers\Siswa\QrCodeController::class, 'show'])->name('qrcode');
    Route::get('/qrcode/download', [App\Http\Controllers\Siswa\QrCodeController::class, 'download'])->name('qrcode.download');
    
    // Izin routes
    Route::get('/izin', [App\Http\Controllers\Siswa\IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [App\Http\Controllers\Siswa\IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [App\Http\Controllers\Siswa\IzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/{izin}', [App\Http\Controllers\Siswa\IzinController::class, 'show'])->name('izin.show');
    
    // Absensi routes
    Route::get('/absensi', [App\Http\Controllers\Siswa\AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{absensi}', [App\Http\Controllers\Siswa\AbsensiController::class, 'show'])->name('absensi.show');
});

// Orangtua Routes
Route::middleware(['auth:orangtua'])->prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'orangtuaDashboard'])->name('dashboard');
    Route::get('/absensi', [App\Http\Controllers\Orangtua\AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/profil', [DashboardController::class, 'orangtuaProfile'])->name('profil');
    Route::get('/notifikasi', [App\Http\Controllers\Orangtua\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{notifikasi}', [App\Http\Controllers\Orangtua\NotifikasiController::class, 'show'])->name('notifikasi.show');
    Route::patch('/notifikasi/{notifikasi}/read', [App\Http\Controllers\Orangtua\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    
    // Pesan Routes
    Route::get('/pesan', [App\Http\Controllers\Orangtua\PesanController::class, 'index'])->name('pesan.index');
    Route::get('/pesan/create', [App\Http\Controllers\Orangtua\PesanController::class, 'create'])->name('pesan.create');
    Route::post('/pesan', [App\Http\Controllers\Orangtua\PesanController::class, 'store'])->name('pesan.store');
    Route::get('/pesan/{pesan}', [App\Http\Controllers\Orangtua\PesanController::class, 'show'])->name('pesan.show');
    Route::get('/pesan/{pesan}/reply', [App\Http\Controllers\Orangtua\PesanController::class, 'reply'])->name('pesan.reply');
    Route::post('/pesan/{pesan}/reply', [App\Http\Controllers\Orangtua\PesanController::class, 'storeReply'])->name('pesan.storeReply');
    Route::patch('/pesan/{pesan}/end', [App\Http\Controllers\Orangtua\PesanController::class, 'end'])->name('pesan.end');
});

// Admin Profile Routes
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
    Route::put('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    
    // Admin Settings Routes
    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/admin/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    
    // Import route for admin to import siswa data
    Route::post('/admin/siswa/import', [SiswaController::class, 'import'])->name('admin.siswa.import');
    
    Route::get('/admin/siswa/template/download', [App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])
        ->name('admin.siswa.template');
});
