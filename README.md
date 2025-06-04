# ��� Sistem Informasi Absensi Sekolah

<div align="center">

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql)](https://mysql.com)
[![WhatsApp](https://img.shields.io/badge/WhatsApp-Integration-25D366?logo=whatsapp)](https://whatsapp.com)

**��� Platform Absensi Digital dengan QR Code & WhatsApp Integration**

*Sistem absensi modern untuk sekolah dengan notifikasi WhatsApp otomatis dan analytics real-time*

[��� Demo](#-demo-accounts) • [⚡ Install](#-quick-installation) • [��� Features](#-features) • [��� Contributing](#-contributing)

</div>

## ��� Overview

Modern attendance system for schools with QR Code scanning and automatic WhatsApp notifications. Built with Laravel 11 and designed for Indonesian educational institutions.

### ✨ Key Features
- ��� **QR Code Scanning** - Real-time attendance with mobile scanner
- ��� **WhatsApp Integration** - Automatic parent notifications
- ��� **Multi-Role System** - Admin, Teacher, Student, Parent dashboards
- ��� **Analytics Dashboard** - Attendance reports and insights
- ��� **Secure & Fast** - Session management with location validation

## ��� Features

<details>
<summary><b>��� Multi-Role System</b></summary>

### Admin Features
- ���️ Complete system control and configuration
- ��� User management (teachers, students, parents)
- ��� Advanced analytics and reporting
- ��� School settings and class management
- ��� WhatsApp service configuration

### Teacher Features
- ��� QR Code scanner for attendance
- ��� Class and subject management
- �� Manual attendance input
- ��� Class attendance reports
- ��� Parent communication

### Student Features
- ��� Personal QR code for attendance
- ��� Personal attendance history
- ��� Class schedule view
- ��� Leave request submission

### Parent Features
- ��� Real-time WhatsApp notifications
- ��� Child attendance monitoring
- ��� Attendance statistics
- ��� School communication portal

</details>

<details>
<summary><b>��� QR Code System</b></summary>

- **��� Secure Encryption** - AES-256 encrypted QR codes
- **⏰ Time-based Validation** - QR codes valid only during class hours
- **��� Unique Per Student** - Individual QR codes for each student
- **��� Location Verification** - GPS-based attendance validation
- **⚡ Fast Scanning** - 2-second scan and validation
- **���️ Anti-fraud Protection** - Duplicate and fake scan prevention

</details>

<details>
<summary><b>��� WhatsApp Integration</b></summary>

- **��� Automatic Notifications** - Instant parent alerts on attendance
- **��� Bulk Messaging** - Broadcast messages to multiple parents
- **��� Custom Templates** - Configurable message templates
- **��� Delivery Status** - Track message delivery and read status
- **��� Retry Logic** - Automatic retry for failed messages
- **��� Multi-language Support** - Indonesian and English templates

</details>

<details>
<summary><b>��� Analytics & Reporting</b></summary>

- **��� Real-time Dashboard** - Live attendance statistics
- **��� Detailed Reports** - Daily, weekly, monthly attendance reports
- **��� Visual Analytics** - Charts and graphs for attendance trends
- **��� Export Options** - PDF, Excel, CSV export formats
- **��� Advanced Filtering** - Filter by class, date, student, status
- **��� Mobile Responsive** - Access reports on any device

</details>

<details>
<summary><b>��� System Features</b></summary>

- **��� Multi-School Support** - Manage multiple schools in one system
- **��� Flexible Scheduling** - Customizable class schedules
- **�� Session Management** - Secure login with session timeout
- **��� Dark/Light Theme** - User preference themes
- **��� Mobile Responsive** - Works on desktop, tablet, and mobile
- **��� Real-time Updates** - Live data synchronization
- **��� Automatic Backup** - Scheduled database backups
- **���️ Security Features** - CSRF protection, SQL injection prevention

</details>

## ��� Quick Installation

<details>
<summary><b>⚡ 5-Minute Setup Guide</b></summary>

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js 16+

### Installation Steps

1. **Clone Repository**
```bash
git clone https://github.com/kevindoni/absenQrCode.git
cd absenQrCode
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup**
```bash
php artisan migrate
php artisan db:seed
```

5. **Build Assets**
```bash
npm run build
```

6. **WhatsApp Service Setup**
```bash
cd whatsapp-service
npm install
npm start
```

7. **Start Application**
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

</details>

<details>
<summary><b>��� Configuration</b></summary>

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi
DB_USERNAME=root
DB_PASSWORD=

# WhatsApp Service
WHATSAPP_SERVICE_URL=http://localhost:3000
WHATSAPP_ENABLED=true

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
```

### WhatsApp Service Setup
1. Start WhatsApp service: `cd whatsapp-service && npm start`
2. Scan QR code with WhatsApp on your phone
3. Service will automatically connect and start listening

</details>

## ��� Demo Accounts

<details>
<summary><b>��� Try Live Demo</b></summary>

| Role | Username | Password | Features |
|------|----------|----------|----------|
| ��� **Admin** | `admin` | `password` | Full system access |
| ��‍��� **Teacher** | `teacher` | `password` | Class management |
| ��� **Student** | `student` | `password` | Personal attendance |
| ���‍���‍���‍��� **Parent** | `parent` | `password` | Child monitoring |

**Demo URL:** [https://demo-absensi.example.com](https://demo-absensi.example.com)

### Demo Features to Try
- ✅ QR Code scanning
- ✅ WhatsApp notifications
- ✅ Real-time dashboard
- ✅ Report generation
- ✅ Multi-role access

</details>

## ��� Tech Stack

<details>
<summary><b>��� Technologies Used</b></summary>

### Backend
- **Laravel 11** - PHP Framework
- **MySQL 8.0** - Database
- **Redis** - Caching & Sessions
- **WhatsApp Web.js** - WhatsApp Integration

### Frontend
- **Blade Templates** - Server-side rendering
- **Bootstrap 5** - UI Framework
- **Chart.js** - Analytics visualization
- **jQuery** - DOM manipulation

### DevOps & Tools
- **Composer** - PHP dependency management
- **NPM** - Node.js packages
- **Vite** - Asset bundling
- **Git** - Version control

</details>

## ��� Documentation

<details>
<summary><b>��� Comprehensive Guides</b></summary>

### API Documentation
- **Authentication** - JWT token-based auth
- **Attendance API** - RESTful endpoints
- **WhatsApp API** - Message sending endpoints
- **Reports API** - Data export endpoints

### User Guides
- **Admin Manual** - Complete system administration
- **Teacher Guide** - Classroom management
- **Parent Portal** - Monitoring child attendance
- **Mobile App** - QR scanning instructions

### Development
- **Contributing Guide** - How to contribute
- **API Reference** - Complete endpoint documentation
- **Database Schema** - ERD and table structures
- **Deployment Guide** - Production setup

</details>

## ��� Troubleshooting

<details>
<summary><b>��� Common Issues & Solutions</b></summary>

### WhatsApp Connection Issues
```bash
# Restart WhatsApp service
cd whatsapp-service
npm restart

# Clear session and reconnect
rm -rf sessions/*
npm start
```

### Database Connection
```bash
# Check database connection
php artisan tinker
DB::connection()->getPdo();

# Run migrations
php artisan migrate:fresh --seed
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Performance Issues
```bash
# Clear all caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

</details>

## ��� Contributing

<details>
<summary><b>��� How to Contribute</b></summary>

We welcome contributions! Here's how to get started:

### Development Setup
1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Make changes and test thoroughly
4. Commit changes: `git commit -m 'Add amazing feature'`
5. Push to branch: `git push origin feature/amazing-feature`
6. Open a Pull Request

### Contribution Guidelines
- Follow PSR-12 coding standards
- Write comprehensive tests
- Update documentation
- Follow semantic versioning

### Areas for Contribution
- ��� Bug fixes
- ✨ New features
- ��� Documentation improvements
- ��� Translations
- ��� Performance optimizations

</details>

## ��� License

<details>
<summary><b>⚖️ MIT License</b></summary>

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

### What this means:
- ✅ Commercial use allowed
- ✅ Modification allowed
- ✅ Distribution allowed
- ✅ Private use allowed
- ❌ No warranty provided
- ❌ No liability

</details>

## ��� Support & Contact

<details>
<summary><b>��� Get Help</b></summary>

### Support Channels
- ��� **Email:** support@absensi-app.com
- ��� **WhatsApp:** +62 812-3456-7890
- ��� **Issues:** [GitHub Issues](https://github.com/kevindoni/absenQrCode/issues)
- �� **Documentation:** [Wiki](https://github.com/kevindoni/absenQrCode/wiki)

### Community
- ��� **GitHub Discussions** - Community Q&A
- ��� **Telegram Group** - Real-time support
- ��� **Twitter** - Updates and announcements

### Business Inquiries
- �� **Enterprise Solutions**
- ��� **School Partnerships**
- ��� **Custom Development**
- ��� **Training & Consulting**

</details>

---

<div align="center">

**��� Made with ❤️ for Indonesian Education**

*Transforming school attendance management with modern technology*

⭐ **Star this repo if you find it helpful!**

</div>
