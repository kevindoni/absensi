# Sistem Informasi Absensi Sekolah

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)

Modern web-based school attendance system built with Laravel. Features QR Code technology for contactless attendance and WhatsApp integration for automatic parent notifications.

## ✨ Key Features

- **Multi-Role System** - Admin, Teacher, Student & Parent access
- **QR Code Attendance** - Modern contactless check-in system
- **WhatsApp Integration** - Automatic notifications to parents
- **Real-time Reporting** - Instant attendance analytics
- **Mobile Responsive** - Works on all devices
- **Scalable Architecture** - Handles thousands of users

## 🛠 Tech Stack

- **Backend**: Laravel 11.x, PHP 8.1+, MySQL 8.0+
- **Frontend**: Bootstrap 4.6, jQuery, SB Admin 2
- **Integration**: WhatsApp Web API (Baileys), Redis
- **Security**: Multi-guard authentication, CSRF protection

## 📦 Quick Installation

### Requirements
- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM
- Git

### Installation Steps

1. **Clone & Setup**
   ```bash
   git clone https://github.com/kevindoni/absenQrCode.git
   cd absenQrCode
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```env
   # Edit .env file
   DB_DATABASE=your_database_name
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

4. **WhatsApp Service Configuration**
   ```env
   # Add to .env
   WHATSAPP_SERVICE_PATH=d:/laragon/www/absensi/whatsapp-service
   WHATSAPP_SERVICE_URL=http://localhost:3001
   ```

5. **Database Migration & Assets**
   ```bash
   php artisan migrate --seed
   npm run build
   ```

6. **WhatsApp Service Setup**
   ```bash
   cd whatsapp-service
   npm install
   pm2 start ecosystem.config.js
   cd ..
   ```

7. **Start Application**
   ```bash
   php artisan serve
   ```

### 🔑 Demo Accounts

| Role | Email | Password | URL |
|------|-------|----------|-----|
| **Admin** | admin@example.com | password | `/auth/admin/login` |
| **Guru** | guru@example.com | password | `/auth/guru/login` |
| **Siswa** | siswa@example.com | password | `/auth/siswa/login` |
| **Orang Tua** | ortu@example.com | password | `/auth/orangtua/login` |

## 🚀 Core Features

### Admin Panel
- ✅ User & role management
- ✅ Class & student management
- ✅ Subject & teacher assignment
- ✅ Schedule management
- ✅ QR Code generation & reset
- ✅ Comprehensive reporting
- ✅ WhatsApp gateway management

### Teacher Module
- ✅ Schedule viewing
- ✅ QR Code attendance scanner
- ✅ Manual attendance input
- ✅ Student excuse management
- ✅ Attendance reports
- ✅ Time-based validation

### Student Portal
- ✅ Personal QR Code
- ✅ Attendance history
- ✅ Schedule viewing
- ✅ Statistics dashboard
- ✅ Online excuse system

### Parent Dashboard
- ✅ Child attendance monitoring
- ✅ WhatsApp notifications
- ✅ Attendance history
- ✅ Real-time alerts

## 📱 WhatsApp Service

### Quick Setup
```bash
# Install service
cd whatsapp-service
npm install

# Auto-configure path (Windows)
./configure-path.ps1

# Start service
pm2 start ecosystem.config.js

# Auto-start on boot
./add-to-startup.ps1
```

### Service Management
```bash
# Check status
pm2 status

# View logs
pm2 logs whatsapp-service

# Restart service
pm2 restart whatsapp-service

# Health check
curl http://localhost:3001/health
```

### Admin Panel Integration
1. Login to admin panel
2. Navigate to **WhatsApp → Settings**
3. Scan QR Code to connect device
4. Configure message templates
5. Test notifications

## 📸 Screenshots

### Home Page
![image](https://github.com/user-attachments/assets/5594c375-593a-4e35-a7f5-58df1730ab70)

### Admin Dashboard
![image](https://github.com/user-attachments/assets/53b55c3d-8c80-44f5-bc7d-131b397ddc95)

### QR Code Scanner
![image](https://github.com/user-attachments/assets/fb17d97d-8288-4fca-9d86-4561c24b1444)

### WhatsApp Integration
![image](https://github.com/user-attachments/assets/c2d83a63-9e43-430a-8754-dcd9cffd7bf9)

## 🔧 Configuration

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=root
DB_PASSWORD=

# WhatsApp Service
WHATSAPP_SERVICE_PATH=d:/laragon/www/absensi/whatsapp-service
WHATSAPP_SERVICE_URL=http://localhost:3001

# Queue (for notifications)
QUEUE_CONNECTION=database
```

### System Requirements (Production)
- **CPU**: 2+ cores
- **RAM**: 4GB minimum, 8GB recommended
- **Storage**: 20GB+ SSD
- **PHP**: 8.1+ with OPcache
- **MySQL**: 8.0+ optimized

## 📚 Documentation

### Key Business Rules
- Students can only check-in during active class hours
- QR codes are valid only for scheduled sessions
- Teachers can edit attendance within 24 hours
- Configurable lateness tolerance
- Automatic parent notifications for absences

### Security Features
- Multi-guard authentication system
- Time-based QR code validation
- CSRF protection
- Session management
- Input validation & sanitization

## 🐛 Troubleshooting

### Common Issues

**WhatsApp Service Not Working**
```bash
# Check service status
pm2 status
curl http://localhost:3001/health

# Restart service
pm2 restart whatsapp-service

# Reset sessions (re-scan QR)
rm -rf whatsapp-service/sessions/*
pm2 restart whatsapp-service
```

**Database Connection Error**
```bash
# Check configuration
php artisan config:show database

# Clear config cache
php artisan config:clear
```

**QR Scanner Issues**
- Ensure HTTPS in production
- Grant camera permissions
- Use modern browser (Chrome, Firefox, Safari)

## 🚧 Roadmap

### Version 1.1 (Q3 2025)
- Mobile application (Android/iOS)
- Email notification system
- Advanced analytics dashboard
- Bulk operations for admin

### Version 1.2 (Q4 2025)
- Academic system integration
- Biometric attendance
- Multi-language support
- Business intelligence reporting

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Developer

**DONI** - Full Stack Laravel Developer  
📧 049536109@ecampus.ut.ac.id  
🏫 Universitas Terbuka

## 🙏 Acknowledgments

- Laravel Framework
- SB Admin 2 Template
- Baileys WhatsApp API
- HTML5-QRCode Library
- Bootstrap & jQuery
- Universitas Terbuka

## 📞 Support

For questions, suggestions, or bug reports:
- 📧 Email: 049536109@ecampus.ut.ac.id
- 🐛 Issues: Create an issue on GitHub
- 🔄 Pull Requests: Contributions welcome!

---

**Made with ❤️ for Indonesian Education**
