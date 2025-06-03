# Sistem Informasi Absensi Sekolah

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)

**Setup WhatsApp Service**
   
   ### A. Instalasi Dependencies
   ```bash
   # Masuk ke direktori WhatsApp service
   cd whatsapp-service
   
   # Install dependencies
   npm install
   ```
   
   ### B. Konfigurasi Path Service (Jika Diperlukan)
   
   **Menggunakan Script Otomatis:**
   ```bash
   # PowerShell (Recommended)
   ./configure-path.ps1
   
   # Atau menggunakan Batch
   ./configure-path.bat
   ```
   
   **Konfigurasi Manual:**
   
   Edit file `whatsapp-service/config.ps1`:
   ```powershell
   # Sesuaikan path dengan lokasi instalasi Anda
   $global:WHATSAPP_SERVICE_PATH = "d:\laragon\www\absensi\whatsapp-service"
   ```
   
   Edit file `whatsapp-service/config.bat`:
   ```batch
   REM Sesuaikan path dengan lokasi instalasi Anda
   set WHATSAPP_SERVICE_PATH=d:\laragon\www\absensi\whatsapp-service
   ```
   
   ### C. Menjalankan Service
   
   **Development Mode:**
   ```bash
   # Jalankan secara langsung (untuk testing)
   npm start
   ```
   
   **Production Mode (Recommended):**
   ```bash
   # Menggunakan PM2 (otomatis restart)
   npm run start:pm2
   
   # Atau manual dengan PM2
   pm2 start ecosystem.config.js
   ```
   
   ### D. Setup Auto-Start (Windows)
   
   **Menggunakan Windows Startup:**
   ```bash
   # Jalankan sebagai Administrator
   ./add-to-startup.ps1
   ```
   
   **Menggunakan Task Scheduler:**
   ```bash
   # Jalankan sebagai Administrator
   ./create-task-scheduler.ps1
   ```
   
   ### E. Verifikasi Service
   ```bash
   # Cek status PM2
   pm2 status
   
   # Cek health service
   ./health-check.ps1
   
   # Test connection
   curl http://localhost:3001/health
   ```
   
   ### F. Konfigurasi di Laravel Admin
   
   1. Login ke dashboard admin
   2. Masuk ke menu **WhatsApp → Settings**
   3. Pastikan **Gateway URL** sesuai dengan `.env`:
      ```
      http://localhost:3001
      ```
   4. Scan QR Code untuk menghubungkan WhatsApp
   5. Test kirim pesan untuk verifikasi
   
   > **📱 Tips WhatsApp Service:**
   > - Service berjalan di port 3001 (default)
   > - QR Code akan muncul di terminal saat pertama kali dijalankan
   > - Session WhatsApp tersimpan di folder `sessions/`
   > - Log service tersimpan di folder `logs/`
   > - Gunakan PM2 untuk auto-restart service
   
   ```bash
   # Kembali ke root directory
   cd ..
   ```

10. **Start development server**ql.com)

Aplikasi Sistem Informasi Absensi Sekolah adalah platform berbasis web yang dibangun menggunakan framework Laravel. Aplikasi ini dirancang untuk memudahkan proses pencatatan dan pemantauan kehadiran siswa di sekolah. Dengan menggunakan teknologi QR Code, aplikasi ini menawarkan cara modern dan efisien dalam mengelola absensi siswa.

## 📋 Tentang Aplikasi

### Overview
Sistem Informasi Absensi Sekolah merupakan solusi digital komprehensif yang dikembangkan untuk mengatasi tantangan pencatatan kehadiran siswa di institusi pendidikan. Aplikasi ini menggabungkan teknologi QR Code modern dengan sistem manajemen berbasis web yang user-friendly, memungkinkan proses absensi yang lebih akurat, efisien, dan transparan.

### Latar Belakang
Dalam era digital saat ini, banyak sekolah masih menggunakan sistem absensi manual yang rentan terhadap kesalahan, manipulasi, dan membutuhkan waktu lama untuk pemrosesan data. Sistem ini hadir sebagai solusi untuk:
- **Meminimalisir human error** dalam pencatatan kehadiran
- **Mengotomatisasi proses** notifikasi kepada orang tua
- **Menyediakan laporan real-time** untuk monitoring kehadiran
- **Meningkatkan transparansi** antara sekolah dan orang tua
- **Mengoptimalkan workflow** administratif sekolah

### Keunggulan Utama
- ✅ **Multi-Role System** - Mendukung 4 role pengguna dengan hak akses berbeda
- ✅ **QR Code Technology** - Sistem absensi modern dan anti-manipulasi  
- ✅ **WhatsApp Integration** - Notifikasi otomatis kepada orang tua
- ✅ **Real-time Reporting** - Laporan dan statistik kehadiran instant
- ✅ **Mobile Responsive** - Dapat diakses dari berbagai perangkat
- ✅ **Data Security** - Sistem autentikasi berlapis dan validasi ketat
- ✅ **Scalable Architecture** - Dapat menangani ribuan siswa dan guru

### Target Pengguna
- **Sekolah Dasar (SD)**
- **Sekolah Menengah Pertama (SMP)**  
- **Sekolah Menengah Atas (SMA/SMK)**
- **Madrasah dan Pesantren**
- **Lembaga Kursus dan Pelatihan**

### Visi & Misi

#### 🎯 Visi
Menjadi solusi sistem absensi digital terdepan yang mendukung transformasi pendidikan Indonesia menuju era digital yang lebih efisien, transparan, dan berkelanjutan.

#### 📋 Misi
1. **Digitalisasi Proses Absensi** - Menghadirkan sistem absensi digital yang mudah digunakan dan dapat diandalkan
2. **Peningkatan Transparansi** - Memberikan akses real-time kepada semua stakeholder pendidikan
3. **Efisiensi Administratif** - Mengurangi beban kerja administratif guru dan staff sekolah
4. **Komunikasi Efektif** - Memfasilitasi komunikasi yang baik antara sekolah dan orang tua
5. **Data-Driven Decision** - Menyediakan analitik dan laporan untuk pengambilan keputusan yang tepat

### Teknologi & Arsitektur
- **Backend**: Laravel 11.x dengan PHP 8.1+
- **Database**: MySQL 8.0+ dengan optimasi indexing
- **Frontend**: Bootstrap 4.6 + jQuery dengan SB Admin 2
- **Real-time**: WhatsApp Web API menggunakan Baileys
- **Security**: Multi-guard authentication dan CSRF protection
- **Performance**: Redis caching dan queue system

## 🚀 Fitur Utama

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

### 6. Notifikasi WhatsApp
- Notifikasi otomatis ke orang tua saat anak tidak hadir.
- Template pesan yang dapat dikustomisasi.
- Manajemen koneksi WhatsApp melalui QR Code.
- Sistem antrian untuk pengiriman pesan massal.
- Validasi nomor WhatsApp dengan format Indonesia.

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
- Notifikasi WhatsApp otomatis ketidakhadiran.
- Riwayat absensi lengkap dengan keterangan.
- Dashboard dengan informasi kehadiran terkini.

## Teknologi yang Digunakan

### Backend
- **Framework**: Laravel 11.x
- **Database**: MySQL 8.0
- **Authentication**: Multi-Guard Authentication
- **Queue**: Database/Redis (untuk notifikasi WhatsApp)
- **Cache**: File/Redis (untuk optimasi performa)
- **WhatsApp API**: Baileys (Node.js WhatsApp Web API)

### Frontend
- **Template**: SB Admin 2 (Bootstrap 4)
- **JavaScript**: jQuery, DataTables
- **CSS Framework**: Bootstrap 4.6
- **QR Scanner**: HTML5-QR-Code Library
- **Charts**: Chart.js untuk visualisasi data
- **Icons**: Font Awesome 5

### Libraries & Packages
- **QR Code**: `simplesoftwareio/simple-qrcode`
- **Excel Export**: `maatwebsite/excel`
- **PDF**: `dompdf/dompdf`
- **Image Processing**: `intervention/image`
- **Notifications**: Laravel built-in notifications
- **DataTables**: `yajra/laravel-datatables`
- **WhatsApp Integration**: Baileys WhatsApp Web API
- **Queue System**: Laravel Queue untuk WhatsApp messaging

### Development Tools
- **Package Manager**: Composer (PHP), NPM (JavaScript)
- **Task Runner**: Laravel Mix / Vite
- **Code Style**: PSR-12 Standard
- **Version Control**: Git

## Extensi PHP & JavaScript yang Digunakan

### PHP Extensions (Required)
- **php-openssl** - Untuk enkripsi dan keamanan
- **php-pdo** - Database abstraction layer
- **php-mbstring** - Multi-byte string handling
- **php-tokenizer** - Token parsing untuk Laravel
- **php-xml** - XML processing
- **php-ctype** - Character type checking
- **php-json** - JSON data interchange
- **php-bcmath** - Binary Calculator untuk precision math
- **php-fileinfo** - File information detection
- **php-gd** - Graphics library untuk QR Code generation
- **php-zip** - Archive handling untuk Excel export/import
- **php-curl** - HTTP client untuk external API calls

### Composer Packages (PHP Dependencies)
```json
{
  "require": {
    "php": "^8.0",
    "bacon/bacon-qr-code": "^2.0",
    "doctrine/dbal": "^4.2", 
    "filament/filament": "^3.2",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1",
    "maatwebsite/excel": "^3.1",
    "phpoffice/phpspreadsheet": "^1.29",
    "simplesoftwareio/simple-qrcode": "^4.2"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pail": "^1.2.2",
    "laravel/pint": "^1.13",
    "laravel/sail": "^1.41",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.6",
    "phpunit/phpunit": "^11.5.3"
  }
}
```

### NPM Packages (JavaScript Dependencies)
```json
{
  "devDependencies": {
    "@tailwindcss/vite": "^4.0.0",
    "axios": "^1.8.2",
    "concurrently": "^9.0.1",
    "laravel-vite-plugin": "^1.2.0",
    "tailwindcss": "^4.0.0",
    "vite": "^6.2.4"
  },
  "dependencies": {
    "datatables.net-bs4": "^2.3.1"
  }
}
```

### Frontend Libraries (Included)
- **jQuery 3.x** - JavaScript library
- **Bootstrap 4.6** - CSS framework
- **DataTables** - Advanced table functionality
- **Chart.js** - Data visualization
- **Font Awesome 5** - Icon library
- **HTML5-QRCode** - QR Code scanner
- **SB Admin 2** - Admin dashboard template
- **jQuery Easing** - Animation effects

## Status Implementasi Fitur

### Superadmin
- ✅ Manajemen User
- ✅ Manajemen Kelas dan Siswa
- ✅ Manajemen Mapel & Guru Mapel
- ✅ Manajemen Jadwal
- ✅ Manajemen Tahun Ajaran
- ✅ Manajemen QR Code (cetak QR code masal untuk siswa per kelas)
- ✅ Fitur reset QR Code siswa (individual dan bulk)
- ✅ Monitoring & Laporan
- ✅ Pengaturan sistem validasi QR Code
- ✅ Pengaturan toleransi keterlambatan
- ✅ Manajemen WhatsApp (koneksi, template, pengaturan)
- ✅ Notifikasi WhatsApp otomatis untuk orang tua
- ✅ Template pesan WhatsApp yang dapat dikustomisasi

### Guru
- ✅ Lihat jadwal mengajar
- ✅ Mulai absensi per sesi with QR
- ✅ Lihat daftar siswa per kelas
- ✅ Laporan kehadiran per mapel
- ✅ Input izin manual dengan surat
- ✅ Absensi Guru (check-in dan check-out)
- ✅ Validasi waktu absensi dengan toleransi keterlambatan
- ✅ QR Code scanner terintegrasi
- ✅ Laporan dan export Excel

### Siswa
- ✅ QR Code unik di dashboard
- ✅ Download QR Code personal
- ✅ Riwayat kehadiran pribadi
- ✅ Dashboard dengan statistik kehadiran
- ✅ Izin online system
- ✅ Lihat jadwal pribadi

### Orang Tua
- ✅ Dashboard dengan notifikasi
- ✅ Sistem notifikasi terintegrasi
- ✅ Notifikasi WhatsApp otomatis untuk ketidakhadiran
- ✅ Lihat data anak detail
- ✅ Riwayat kehadiran anak lengkap
- ✅ Notifikasi real-time ketidakhadiran

### Fitur Validasi dan Keamanan
- ✅ Time-based QR Scan Window (QR valid sesuai jadwal)
- ✅ Validasi Scan QR berdasarkan jadwal
- ✅ Batasan keterlambatan (konfigurasi menit)
- ✅ Sistem toleransi keterlambatan
- ✅ Validasi QR Code berdasarkan tahun ajaran aktif
- ✅ Notifikasi WhatsApp otomatis untuk orang tua
- ✅ Integrasi layanan pesan WhatsApp dengan Baileys
- ✅ Template pesan WhatsApp yang dapat dikustomisasi
- ✅ Manajemen koneksi WhatsApp melalui QR Code
- ✅ Sistem antrian untuk pengiriman pesan WhatsApp

## Status Aturan Bisnis
- ✅ Absensi siswa hanya pada jam pelajaran aktif
- ✅ QR code hanya berlaku untuk jadwal yang sudah ditentukan
- ✅ Batasan maksimal keterlambatan (konfigurasi menit)
- ✅ Batasan waktu edit absensi untuk guru (24 jam)
- ✅ Check-in dan check-out guru
- ✅ Reset QR Code oleh superadmin

## 📦 Cara Instalasi

### Persyaratan Sistem
- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM (untuk asset compilation dan WhatsApp service)
- Git

### 🔧 Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/kevindoni/absenQrCode.git
   cd absenQrCode
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Setup environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_database
   DB_PASSWORD=password_database
   ```

6. **Configure WhatsApp Service**
   
   Edit file `.env` dan tambahkan konfigurasi WhatsApp Service:
   ```env
   # WhatsApp Service Configuration
   WHATSAPP_SERVICE_PATH=d:/laragon/www/absensi/whatsapp-service
   WHATSAPP_SERVICE_URL=http://localhost:3001
   ```
   
   > **📝 Catatan:** 
   > - Untuk **development (Windows)**: gunakan path seperti `d:/laragon/www/absensi/whatsapp-service`
   > - Untuk **production (Linux)**: gunakan path seperti `/var/www/absensi/whatsapp-service`
   > - URL service default adalah `http://localhost:3001`, sesuaikan jika menggunakan port berbeda

7. **Run database migrations dan seeding**
   ```bash
   php artisan migrate --seed
   ```

8. **Build assets (optional)**
   ```bash
   npm run build
   ```

9. **Setup WhatsApp Service**
   ```bash
   # Masuk ke direktori WhatsApp service
   cd whatsapp-service
   
   # Install dependencies
   npm install
   
   # Jalankan WhatsApp service
   npm start
   
   # Kembali ke root directory
   cd ..
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

10. **Akses aplikasi**
   
   Buka browser: `http://localhost:8000`

### 🔑 Demo Accounts

Setelah seeding, gunakan akun berikut untuk login:

| Role | Email | Password | URL Login |
|------|-------|----------|-----------|
| **Admin** | admin@example.com | password | `/auth/admin/login` |
| **Guru** | guru@example.com | password | `/auth/guru/login` |
| **Siswa** | siswa@example.com | password | `/auth/siswa/login` |
| **Orang Tua** | ortu@example.com | password | `/auth/orangtua/login` |

## 📸 Screenshots

### HOME
![image](https://github.com/user-attachments/assets/5594c375-593a-4e35-a7f5-58df1730ab70)
![image](https://github.com/user-attachments/assets/95b34d33-fdba-4590-88ee-767b19d1198e)


### Dashboard Admin
![image](https://github.com/user-attachments/assets/53b55c3d-8c80-44f5-bc7d-131b397ddc95)


### QR Code Scanner
![image](https://github.com/user-attachments/assets/fb17d97d-8288-4fca-9d86-4561c24b1444)


### Whatsapp Gateway Integration
![image](https://github.com/user-attachments/assets/c2d83a63-9e43-430a-8754-dcd9cffd7bf9)


### Laporan Kehadiran
![image](https://github.com/user-attachments/assets/58fdc4b3-7918-4672-9bd4-31fd2a61c6a0)


## 📱 Konfigurasi WhatsApp Service

### 🔧 Environment Configuration

Aplikasi ini terintegrasi dengan WhatsApp Service untuk notifikasi otomatis. Berikut adalah konfigurasi yang diperlukan:

#### File .env (Development)
```env
# WhatsApp Service Configuration
WHATSAPP_SERVICE_PATH=d:/laragon/www/absensi/whatsapp-service
WHATSAPP_SERVICE_URL=http://localhost:3001
```

#### File .env.production (Production)
```env
# WhatsApp Service Configuration  
WHATSAPP_SERVICE_PATH=/var/www/absensi/whatsapp-service
WHATSAPP_SERVICE_URL=http://localhost:3001
```

### 🚀 Quick Start Guide

#### 1. Instalasi Service
```bash
cd whatsapp-service
npm install
```

#### 2. Konfigurasi Path (Jika Diperlukan)
```bash
# Otomatis menggunakan script
./configure-path.ps1

# Atau manual edit config.ps1 dan config.bat
```

#### 3. Jalankan Service
```bash
# Development
npm start

# Production (PM2)
pm2 start ecosystem.config.js
```

#### 4. Setup Auto-Start
```bash
# Windows Startup
./add-to-startup.ps1

# Task Scheduler  
./create-task-scheduler.ps1
```

### 🔄 Service Management

#### Mengelola Service dengan PM2
```bash
# Start service
pm2 start ecosystem.config.js

# Stop service  
pm2 stop whatsapp-service

# Restart service
pm2 restart whatsapp-service

# Check status
pm2 status

# View logs
pm2 logs whatsapp-service

# Monitor resources
pm2 monit
```

#### Health Check & Monitoring
```bash
# Manual health check
./health-check.ps1

# Test API endpoint
curl http://localhost:3001/health

# Check service uptime
curl http://localhost:3001/uptime
```

### 📁 Struktur File Service

```
whatsapp-service/
├── 📄 server.js              # Main server file
├── 📄 package.json           # Dependencies
├── 📄 ecosystem.config.js    # PM2 configuration
├── 📁 sessions/              # WhatsApp sessions
├── 📁 logs/                  # Service logs
├── 🔧 config.ps1             # PowerShell config
├── 🔧 config.bat             # Batch config
├── ⚙️ configure-path.ps1     # Path setup script
├── 🚀 start-service.ps1      # Start script
├── 🛑 stop-service.bat       # Stop script
├── 🔄 restart-service.bat    # Restart script
├── 🏥 health-check.ps1       # Health monitoring
├── 🕒 add-to-startup.ps1     # Auto-startup setup
└── 📊 create-task-scheduler.ps1  # Task scheduler
```

### 🌐 Integrasi dengan Laravel

#### Database Settings
Service URL otomatis tersimpan dalam database `settings` dengan key:
- `whatsapp_gateway_url` = URL service (default: http://localhost:3001)

#### Artisan Commands
```bash
# Update WhatsApp URL setting
php artisan whatsapp:update-url

# Test WhatsApp integration  
php artisan whatsapp:demo

# Check service status
php artisan whatsapp:status
```

#### Service Configuration dalam Laravel
```php
// Mendapatkan service URL dari setting
$serviceUrl = Setting::getSetting('whatsapp_gateway_url', 'http://localhost:3001');

// Test connection ke service
$response = Http::timeout(5)->get($serviceUrl . '/health');
```

### 🔐 Security & Best Practices

#### Port Configuration
- **Default Port**: 3001
- **Custom Port**: Edit `ecosystem.config.js` dan `.env`
- **Firewall**: Buka port untuk akses lokal

#### Session Management
- Session WhatsApp tersimpan di folder `sessions/`
- Backup session untuk menghindari scan ulang QR Code
- Session otomatis refresh setiap 24 jam

#### Auto-Restart Policies
```javascript
// ecosystem.config.js
{
  autorestart: true,
  watch: false,
  max_memory_restart: '1G',
  restart_delay: 5000
}
```

### 🐛 Troubleshooting

#### Service Tidak Bisa Start
```bash
# Check port availability
netstat -an | findstr :3001

# Check PM2 processes
pm2 list

# Clear PM2 processes
pm2 delete all
pm2 start ecosystem.config.js
```

#### WhatsApp Disconnected
```bash
# Restart service
pm2 restart whatsapp-service

# Clear sessions (akan scan QR lagi)
rm -rf sessions/*
pm2 restart whatsapp-service
```

#### Path Configuration Issues
```bash
# Reset path configuration
./configure-path.ps1

# Verify configuration
./verify-auto-startup.ps1
```

### 📊 Monitoring & Logs

#### Log Locations
- **PM2 Logs**: `~/.pm2/logs/`
- **Service Logs**: `whatsapp-service/logs/`
- **Laravel Logs**: `storage/logs/`

#### Real-time Monitoring
```bash
# PM2 monitoring dashboard
pm2 monit

# Tail service logs
pm2 logs whatsapp-service --lines 50

# Health check with details
./health-check.ps1
```

## 🛠️ Tech Stack

1. **Prioritas Tinggi**
   - Implementasi jadwal pribadi untuk siswa
   - Modul orang tua untuk pemantauan detail kehadiran anak
   - Batasan waktu edit absensi untuk guru (24 jam)
   - Riwayat kehadiran anak yang lebih lengkap

2. **Prioritas Menengah**
   - Notifikasi real-time ketidakhadiran untuk orang tua
   - Integrasi dengan sistem akademik sekolah
   - Aplikasi mobile untuk siswa dan orang tua
   - Sistem notifikasi Email otomatis

3. **Prioritas Rendah**
   - Fitur notifikasi via Telegram
   - Analisis prediktif untuk tren ketidakhadiran
   - Sistem reward untuk kehadiran konsisten
   - Integrasi layanan pesan eksternal lainnya

## Kontributor

- **Developer**: DONI - *Full Stack Laravel Developer*
- **Email**: 049536109@ecampus.ut.ac.id
- **Institution**: Universitas Terbuka
- **Role**: Lead Developer & System Architect

### Kontribusi
- Sistem autentikasi multi-role (Admin, Guru, Siswa, Orang Tua)
- Implementasi QR Code technology untuk absensi
- Database design dan migrasi
- Frontend responsive dengan SB Admin 2
- Sistem validasi waktu dan toleransi keterlambatan
- Modul laporan dan export Excel/PDF
- Notification system integration
- WhatsApp integration dengan Baileys API
- Template pesan WhatsApp yang dapat dikustomisasi
- Sistem antrian untuk pengiriman notifikasi massal

## Lisensi

Aplikasi ini dilisensikan di bawah **MIT License**.

### MIT License

Copyright (c) 2025 DONI - Universitas Terbuka

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

## Acknowledgments

- **Laravel Framework** - The PHP framework for web artisans
- **SB Admin 2** - Bootstrap 4 admin template
- **HTML5 QR Code** - QR Code scanner library
- **PhpSpreadsheet** - Excel export functionality
- **DataTables** - Advanced table plugin for jQuery
- **Baileys** - WhatsApp Web API library
- **Universitas Terbuka** - Supporting institution

## 📊 Project Statistics

### Codebase Overview
- **Total Lines of Code**: ~15,000+ lines
- **PHP Files**: 80+ files
- **JavaScript Files**: 25+ files
- **Database Tables**: 15+ tables
- **Migration Files**: 20+ migrations
- **Blade Templates**: 50+ views

### Feature Coverage
- **Core Features**: 100% implemented
- **Admin Panel**: 95% complete
- **Teacher Module**: 100% functional
- **Student Portal**: 95% complete
- **Parent Dashboard**: 90% functional
- **WhatsApp Integration**: 100% operational
- **Reporting System**: 100% functional

### Testing & Quality
- **Code Standards**: PSR-12 compliant
- **Security**: Multi-layer authentication
- **Performance**: Optimized with caching
- **Browser Support**: Chrome, Firefox, Safari, Edge
- **Mobile Compatibility**: Fully responsive

## Support & Contact

Jika Anda memiliki pertanyaan, saran, atau menemukan bug, silakan:

1. **Email**: 049536109@ecampus.ut.ac.id
2. **Issues**: Buat issue di repository GitHub
3. **Pull Requests**: Kontribusi welcome!

## Changelog

### Version 1.0.0 (2025-06-01)
- ✅ Initial release
- ✅ Multi-role authentication system
- ✅ QR Code attendance management
- ✅ Academic year management
- ✅ Time-based validation system
- ✅ Comprehensive reporting
- ✅ Parent notification system
- ✅ Mobile-responsive design
- ✅ WhatsApp integration dengan Baileys API
- ✅ Template pesan WhatsApp yang dapat dikustomisasi
- ✅ Sistem antrian untuk notifikasi WhatsApp
- ✅ Manajemen koneksi WhatsApp melalui admin panel

## 🚧 Development History & Roadmap

### Sejarah Pengembangan
**📅 Q1 2025**: Inisiasi proyek dan analisis kebutuhan sistem absensi digital
**📅 Q2 2025**: Development fase 1 - Core system dan QR Code integration
**📅 Q2 2025**: Development fase 2 - WhatsApp notification system
**📅 Juni 2025**: Production release v1.0.0 dengan fitur lengkap

### Roadmap Pengembangan

#### 🎯 Version 1.1 (Q3 2025)
- ⏳ Mobile application untuk Android/iOS
- ⏳ Email notification system
- ⏳ Advanced analytics dan dashboard
- ⏳ Bulk operations untuk admin

#### 🎯 Version 1.2 (Q4 2025)
- ⏳ Integration dengan sistem akademik (SIAKAD)
- ⏳ Biometric attendance (fingerprint/face recognition)
- ⏳ Multi-language support (Bahasa Indonesia & English)
- ⏳ Advanced reporting dengan business intelligence

#### 🎯 Version 2.0 (2026)
- ⏳ Cloud-based deployment
- ⏳ API ecosystem untuk third-party integration
- ⏳ Machine learning untuk predictive analytics
- ⏳ Blockchain integration untuk audit trail

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
   - Manajemen WhatsApp (koneksi, template, pengaturan)
   - Test notifikasi WhatsApp

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
   - ✅ Pengaturan notifikasi WhatsApp untuk orang tua siswa
   - ✅ Integrasi dengan layanan pesan WhatsApp
   - ✅ Konfigurasi aturan notifikasi WhatsApp

4. **Fitur Monitoring Lanjutan**
   - Analytics kehadiran siswa dengan visualisasi lebih detail
   - Deteksi pola ketidakhadiran siswa
   - Laporan perbandingan antar periode

## FAQ (Frequently Asked Questions)

### Q: Bagaimana cara reset QR Code siswa?
**A**: Admin dapat reset QR Code melalui:
1. Login sebagai Admin → Siswa → Pilih siswa → Reset QR Code
2. Atau melalui menu QR Code Management → Reset QR Code

### Q: Mengapa QR Code tidak bisa di-scan?
**A**: Pastikan:
- QR Code dalam kondisi clear/tidak blur
- Kamera memiliki akses permission
- Berada dalam jam pelajaran yang aktif
- Siswa terdaftar dalam kelas yang sedang diajar

### Q: Bagaimana mengatur toleransi keterlambatan?
**A**: Admin dapat mengatur melalui:
Settings → QR Code Settings → Late Tolerance (dalam menit)

### Q: Bisakah guru mengedit absensi yang sudah tercatat?
**A**: Ya, guru dapat mengedit dalam batas waktu 24 jam setelah absensi dicatat.

### Q: Bagaimana cara export laporan?
**A**: Melalui menu Laporan → pilih filter → Export Excel/PDF

### Q: Bagaimana cara setup WhatsApp untuk notifikasi?
**A**: 
1. **Konfigurasi Environment**:
   ```env
   # Tambahkan ke .env
   WHATSAPP_SERVICE_PATH=d:/laragon/www/absensi/whatsapp-service
   WHATSAPP_SERVICE_URL=http://localhost:3001
   ```

2. **Install dan Jalankan Service**:
   ```bash
   cd whatsapp-service
   npm install
   pm2 start ecosystem.config.js
   ```

3. **Setup di Admin Panel**:
   - Admin login → WhatsApp Management → Connect Device
   - Scan QR Code dengan WhatsApp di HP
   - Setup template pesan di WhatsApp Templates
   - Aktifkan notifikasi di Notification Settings

4. **Verifikasi Koneksi**:
   - Cek status di dashboard admin
   - Test kirim pesan manual
   - Monitor logs: `pm2 logs whatsapp-service`

### Q: WhatsApp tidak terkoneksi atau pesan tidak terkirim?
**A**: 
1. **Cek Service Status**:
   ```bash
   pm2 status
   curl http://localhost:3001/health
   ./health-check.ps1
   ```

2. **Restart Service**:
   ```bash
   pm2 restart whatsapp-service
   # Atau menggunakan script
   ./restart-service.bat
   ```

3. **Reset Session (jika perlu scan QR lagi)**:
   ```bash
   pm2 stop whatsapp-service
   rm -rf sessions/*
   pm2 start ecosystem.config.js
   ```

4. **Troubleshooting Checklist**:
   - ✅ Service berjalan di port 3001
   - ✅ Gateway URL benar di admin panel
   - ✅ Nomor HP format Indonesia (+62xxx)
   - ✅ WhatsApp Web tidak logout di HP
   - ✅ Template pesan sudah dikonfigurasi

### Q: Bagaimana cara pindah service ke path/server lain?
**A**:
1. **Menggunakan Script Otomatis**:
   ```bash
   ./configure-path.ps1
   # Ikuti instruksi untuk move files
   ```

2. **Manual Migration**:
   ```bash
   # Stop service lama
   pm2 stop whatsapp-service
   pm2 delete whatsapp-service
   
   # Copy files ke lokasi baru
   cp -r whatsapp-service /new/path/
   
   # Update .env
   WHATSAPP_SERVICE_PATH=/new/path/whatsapp-service
   
   # Start dari lokasi baru
   cd /new/path/whatsapp-service
   pm2 start ecosystem.config.js
   ```

### Q: Service auto-start saat Windows boot?
**A**:
```bash
# Menggunakan Windows Startup
./add-to-startup.ps1

# Atau Task Scheduler (recommended)
./create-task-scheduler.ps1

# Verifikasi setup
./verify-auto-startup.ps1
```

## Troubleshooting

### Masalah Umum dan Solusi

#### 1. Error 500 - Internal Server Error
```bash
# Cek log error
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

#### 2. QR Scanner tidak bekerja
- Pastikan mengakses melalui HTTPS (untuk production)
- Berikan permission kamera pada browser
- Gunakan browser modern (Chrome, Firefox, Safari)

#### 3. Database Connection Error
```bash
# Cek konfigurasi .env
cat .env | grep DB_

# Test koneksi database
php artisan tinker
DB::connection()->getPdo();
```

#### 4. Assets tidak termuat
```bash
# Compile ulang assets
npm run build

# Clear cache browser
Ctrl + F5 (hard refresh)
```

#### 5. Session Timeout
```bash
# Increase session lifetime di .env
SESSION_LIFETIME=120

# Clear session cache
php artisan session:table
php artisan migrate
```

#### 6. WhatsApp Service Issues
```bash
# Cek status WhatsApp service
cd whatsapp-service
npm start

# Restart WhatsApp service
npm restart

# Clear WhatsApp sessions
rm -rf sessions/*

# Cek log WhatsApp service
tail -f logs/whatsapp.log
```

#### 7. WhatsApp Messages Not Sending
```bash
# Cek queue jobs
php artisan queue:work

# Clear failed jobs
php artisan queue:flush

# Test WhatsApp connection
php artisan whatsapp:test
```

## Performance Optimization

### Recommended Settings
```env
# .env optimizations
APP_DEBUG=false
LOG_LEVEL=error
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# WhatsApp service settings
WHATSAPP_SERVICE_URL=http://localhost:3000
WHATSAPP_QUEUE_DELAY=5
WHATSAPP_MAX_RETRIES=3
```

### Database Optimization
```sql
-- Index untuk performa query
CREATE INDEX idx_absensi_tanggal ON absensis(tanggal);
CREATE INDEX idx_absensi_siswa ON absensis(siswa_id);
CREATE INDEX idx_jadwal_hari ON jadwal_mengajar(hari);
```

### Server Requirements (Production)
- **CPU**: 2+ cores
- **RAM**: 4GB minimum, 8GB recommended
- **Storage**: 20GB+ SSD
- **PHP**: 8.1+ with OPcache enabled
- **MySQL**: 8.0+ with optimized settings
