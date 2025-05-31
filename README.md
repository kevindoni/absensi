# Sistem Informasi Absensi Sekolah

## Deskripsi Aplikasi

Aplikasi Sistem Informasi Absensi Sekolah adalah platform berbasis web yang dibangun menggunakan framework Laravel. Aplikasi ini dirancang untuk memudahkan proses pencatatan dan pemantauan kehadiran siswa di sekolah. Dengan menggunakan teknologi QR Code, aplikasi ini menawarkan cara modern dan efisien dalam mengelola absensi siswa.

## Fitur Utama

### 1. Multi User Role
- **Admin/Superadmin**: Mengelola seluruh data master, laporan, dan pengaturan sistem.
- **Guru**: Mencatat absensi siswa, melihat riwayat absensi, dan membuat laporan.
- **Siswa**: Melihat riwayat kehadiran dan status absensi.
- **Orang Tua**: Memantau kehadiran anaknya di sekolah.

### 2. Manajemen Absensi
- Pencatatan absensi manual dan otomatis via QR Code.
- Kategorisasi status kehadiran: Hadir, Sakit, Izin, dan Alpha (tanpa keterangan).
- Keterangan tambahan untuk setiap absensi.
- Pencatatan waktu absensi secara real-time.

### 3. Teknologi QR Code
- Setiap siswa memiliki QR Code unik.
- Pemindaian QR Code oleh guru untuk mencatat kehadiran.
- QR Code dapat ditampilkan di kartu siswa atau perangkat mobile.

### 4. Laporan dan Statistik
- Laporan harian, mingguan, dan bulanan.
- Statistik kehadiran per siswa, kelas, atau mata pelajaran.
- Export laporan dalam format Excel.
- Cetak laporan dalam format PDF.

### 5. Dashboard Interaktif
- Tampilan visual untuk statistik kehadiran.
- Informasi real-time tentang status absensi.
- Notifikasi untuk ketidakhadiran tanpa keterangan.

## Struktur Sistem

### 1. Modul Admin/Superadmin
- Manajemen data master (siswa, guru, kelas, mata pelajaran, jadwal).
- Manajemen pengguna dan hak akses.
- Manajemen Tahun Ajaran (tambah tahun ajaran dan aktifkan).
- Manajemen QR Code (generate, reset).
- Laporan keseluruhan absensi.
- Pengaturan sistem.

### 2. Modul Guru
- Dashboard dengan informasi mengajar hari ini.
- Pencatatan absensi siswa (manual atau via QR Code).
- Check-in dan check-out saat mengajar.
- Edit dan hapus data absensi (dengan batasan waktu 24 jam).
- Laporan absensi per kelas atau mata pelajaran.
- Input izin untuk siswa yang memiliki surat (sakit/izin).

### 3. Modul Siswa
- Dashboard dengan riwayat kehadiran.
- Statistik kehadiran pribadi.
- QR Code personal untuk absensi.
- Lihat jadwal pelajaran.

### 4. Modul Orang Tua
- Pemantauan kehadiran anak.
- Notifikasi ketidakhadiran.
- Riwayat absensi lengkap dengan keterangan.

## Teknologi yang Digunakan

### Backend
- Framework Laravel 12
- Database MySQL
- Authentication Guard untuk multi-user login

### Frontend
- SB Admin 2 Template (Bootstrap 4)
- JavaScript/jQuery
- DataTables untuk pengelolaan tabel
- HTML5 QR Code Scanner untuk pemindaian QR Code

### Fitur Tambahan
- Export data ke Excel menggunakan PhpSpreadsheet
- Cetak laporan dengan desain responsif
- Notifikasi real-time untuk admin dan orang tua

## Fitur yang Belum Diimplementasikan

### Superadmin
- ✓ Manajemen User
- ✓ Manajemen Kelas dan Siswa
- ✓ Manajemen Mapel & Guru Mapel
- ✓ Manajemen Jadwal
- ✓ Manajemen Tahun Ajaran
- ✓ Manajemen QR Code (cetak QR code masal untuk siswa per kelas)
- ❌ Fitur reset QR Code siswa
- ✓ Monitoring & Laporan

### Guru
- ✓ Lihat jadwal mengajar
- ✓ Mulai absensi per sesi with QR
- ✓ Lihat daftar siswa per kelas
- ✓ Laporan kehadiran per mapel
- ✓ Input izin manual dengan surat
- ✓ Absensi Guru (check-in dan check-out)
- ❌ Validasi waktu absensi (hanya saat jam pelajaran aktif)

### Siswa
- ❌ QR Code unik di dashboard
- ❌ Lihat jadwal pribadi
- ❌ Riwayat kehadiran pribadi

### Orang Tua
- ❌ Lihat data anak
- ❌ Riwayat kehadiran anak
- ❌ Notifikasi ketidakhadiran/keterlambatan

### Fitur Validasi dan Keamanan
- ❌ Time-based QR Scan Window (QR valid hanya saat jadwal berlangsung)
- ❌ Validasi Scan QR berdasarkan jadwal
- ❌ Batasan keterlambatan (misal 10 menit)
- ❌ Notifikasi WhatsApp/Email

## Aturan Bisnis yang Belum Diimplementasikan
- ❌ Absensi siswa hanya pada jam pelajaran aktif
- ❌ QR code hanya berlaku untuk jadwal yang sudah ditentukan
- ❌ Batasan maksimal keterlambatan (10 menit)
- ❌ Batasan waktu edit absensi untuk guru (24 jam)
- ❌ Check-in dan check-out guru
- ❌ Reset QR Code oleh superadmin

## Cara Instalasi

1. Clone repositori dari GitHub
   ```
   git clone https://github.com/username/absensi-sekolah.git
   ```

2. Instal dependensi menggunakan Composer
   ```
   composer install
   ```

3. Salin file .env.example menjadi .env
   ```
   cp .env.example .env
   ```

4. Generate application key
   ```
   php artisan key:generate
   ```

5. Lakukan migrasi dan seeding database
   ```
   php artisan migrate --seed
   ```

6. Jalankan development server
   ```
   php artisan serve
   ```

## Demonstrasi Sistem

### Akun Demo
- Admin: admin@example.com / password
- Guru: guru@example.com / password
- Siswa: siswa@example.com / password
- Orang Tua: ortu@example.com / password

## Langkah Pengembangan Selanjutnya

1. **Prioritas Tinggi**
   - Implementasi manajemen QR Code dan validasi scan
   - Sistem check-in/check-out untuk guru
   - Dashboard siswa dengan QR Code personal
   - Validasi waktu pada absensi (time-based)

2. **Prioritas Menengah**
   - Modul orang tua untuk memantau kehadiran siswa
   - Manajemen tahun ajaran
   - Sistem notifikasi untuk ketidakhadiran

3. **Prioritas Rendah**
   - Integrasi dengan sistem akademik sekolah
   - Aplikasi mobile untuk siswa dan orang tua
   - Fitur notifikasi via WhatsApp/Telegram
   - Analisis prediktif untuk tren ketidakhadiran
   - Sistem reward untuk kehadiran konsisten

## Kontributor

- Developer: [Nama Developer]
- Designer: [Nama Designer]
- Tester: [Nama Tester]

## Lisensi

Aplikasi ini dilisensikan di bawah [Jenis Lisensi].

## Fitur Admin yang Sudah Diimplementasikan

1. **Manajemen User**
   - CRUD data admin, guru, siswa
   - Pengaturan hak akses

2. **Manajemen Kelas dan Siswa**
   - CRUD data kelas
   - Assign siswa ke kelas
   - Melihat detail siswa per kelas
   - Cetak QR Code masal untuk siswa per kelas

3. **Manajemen Mata Pelajaran & Guru**
   - CRUD data mata pelajaran
   - Mapping guru ke mata pelajaran
   - Pengelolaan guru pengajar

4. **Manajemen Jadwal**
   - Pengaturan jadwal belajar (hari, jam, guru, kelas, mapel)
   - Pengelolaan jam pelajaran

5. **Manajemen Absensi**
   - Lihat data absensi seluruh kelas
   - Filter absensi berdasarkan tanggal, kelas, guru

6. **Dashboard**
   - Ringkasan statistik absensi
   - Informasi kehadiran terkini
   - Grafik kehadiran

7. **Laporan dan Ekspor Data**
   - Laporan absensi harian, mingguan, bulanan
   - Export laporan dalam format Excel
   - Cetak laporan dalam format PDF

8. **Fitur Tambahan**
   - Pencarian data siswa dan guru
   - Filter dan sorting pada tabel data
   - Interface yang responsif

### Fitur Admin yang Masih Perlu Diimplementasikan

1. **Pengembangan Manajemen QR Code**
   - ✓ Fitur reset QR Code untuk siswa tertentu
   - ✓ Regenerasi QR Code jika diperlukan (misalnya hilang/rusak)
   - ✓ Validasi QR Code berdasarkan tahun ajaran aktif

2. **Pengaturan Validasi Absensi**
   - ✓ Konfigurasi batasan waktu keterlambatan
   - ✓ Pengaturan periode validitas QR Code
   - ✓ Konfigurasi aturan absensi per kelas atau mapel

3. **Notifikasi dan Integrasi**
   - Pengaturan notifikasi untuk orang tua siswa
   - Integrasi dengan layanan pesan (WhatsApp)
   - Konfigurasi aturan notifikasi

4. **Fitur Monitoring Lanjutan**
   - Analytics kehadiran siswa dengan visualisasi lebih detail
   - Deteksi pola ketidakhadiran siswa
   - Laporan perbandingan antar periode




Looking at the sidebar and the overall system, here are the features that still need to be implemented:

For Guru Module:

Input izin manual dengan surat
Absensi Guru (check-in dan check-out)
Validasi waktu absensi (hanya saat jam pelajaran aktif)
For Siswa Module:

QR Code unik di dashboard
Lihat jadwal pribadi
Riwayat kehadiran pribadi
For Orang Tua Module:

Lihat data anak
Riwayat kehadiran anak
Notifikasi ketidakhadiran/keterlambatan
Notifications and Integrations:

Notifikasi WhatsApp/Email for absences
Integrasi dengan layanan pesan
---

## ��� Instalasi untuk Hosting (Production)


### Langkah Instalasi di Hosting

#### 1. Upload dan Extract
```bash
# Upload backup ke hosting
# Extract di server hosting
tar -xzf absensi-backup-YYYYMMDD-HHMMSS.tar.gz
```

#### 2. Setup Environment
```bash
cp .env.example .env
# Edit .env dengan konfigurasi hosting (database, domain, dll)
```

#### 3. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev --no-interaction
npm install && npm run build
```

#### 4. Setup Aplikasi
```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

#### 5. Optimize untuk Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 6. Set Permissions
```bash
chmod -R 755 storage/ bootstrap/cache/ public/uploads/
```

### Requirements Hosting
- **PHP:** >= 8.1
- **Database:** MySQL >= 5.7
- **Extensions:** OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, GD
- **Memory:** 512MB+ RAM
- **Storage:** 1GB+ SSD

### Keamanan Production
- Set document root ke folder `public/`
- Install SSL certificate
- Set proper file permissions (644 untuk file, 755 untuk folder)
- Proteksi file `.env`

### Troubleshooting
- **Error 500:** Check `storage/logs/laravel.log`
- **Permission Error:** `chmod -R 755 storage/ bootstrap/cache/`
- **Database Error:** Periksa kredensial di `.env`

### Maintenance
```bash
# Update aplikasi
php artisan down
# Upload update
composer install --no-dev
php artisan migrate --force
php artisan optimize
php artisan up

# Backup database
mysqldump -u username -p database_name > backup.sql
```

---
