<!DOCTYPE html>
<html lang="id">
<head>    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="AbsensiPro - Sistem Absensi Sekolah Modern">
    <meta name="author" content="AbsensiPro">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AbsensiPro - Sistem Absensi Sekolah Digital</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }        :root {
            --primary-color: #0473a7;
            --secondary-color: #8b5cf6;
            --accent-color: #06d6a0;
            --guru-color: #4e73df;
            --siswa-color: #06d6a0;
            --orangtua-color: #f97316;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }        /* Dark Theme Variables */
        [data-theme="dark"] {
            --primary-color: #0473a7;
            --secondary-color: #06b6d4;
            --accent-color: #0284c7;
            --guru-color: #4e73df;
            --siswa-color: #06d6a0;
            --orangtua-color: #f97316;
            --text-dark: #f9fafb;
            --text-light: #d1d5db;
            --bg-light: #1f2937;
            --white: #111827;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.2);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.3), 0 8px 10px -6px rgb(0 0 0 / 0.2);
            --shadow-xl: 0 25px 50px -12px rgb(0 0 0 / 0.4);
        }        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-color);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Dark theme background adjustment */
        [data-theme="dark"] body {
            background: #1e293b;
        }

        /* Theme Toggle Switch */
        .theme-toggle {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .theme-toggle {
            background: rgba(31, 41, 55, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .theme-switch {
            position: relative;
            width: 60px;
            height: 30px;
            background: #e5e7eb;
            border-radius: 15px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        [data-theme="dark"] .theme-switch {
            background: #374151;
        }

        .theme-switch::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: #fff;
            border-radius: 50%;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .theme-switch.dark::before {
            transform: translateX(30px);
            background: #1f2937;
        }

        .theme-switch .sun-icon,
        .theme-switch .moon-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.75rem;
            transition: all 0.3s ease;
        }

        .theme-switch .sun-icon {
            left: 6px;
            color: #f59e0b;
            opacity: 1;
        }

        .theme-switch .moon-icon {
            right: 6px;
            color: #6b7280;
            opacity: 0.5;
        }

        .theme-switch.dark .sun-icon {
            opacity: 0.5;
        }

        .theme-switch.dark .moon-icon {
            opacity: 1;
            color: #fbbf24;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.6;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            width: 120px;
            height: 120px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 80px;
            height: 80px;
            top: 70%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape-4 {
            width: 60px;
            height: 60px;
            top: 30%;
            right: 30%;
            animation-delay: 1s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.6;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.8;
            }
        }        /* Main Container */
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 0;
            box-shadow: none;
            width: 100%;
            overflow: hidden;
            border: none;
            animation: slideInUp 1s ease-out;
            transition: background-color 0.3s ease;
        }

        [data-theme="dark"] .welcome-card {
            background: rgba(17, 24, 39, 0.95);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }        /* Header */
        .hero-section {
            background: var(--primary-color);
            color: #ffffff;
            text-align: center;
            padding: 2.5rem 2rem 3.5rem;
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2.5rem;
            background: var(--white);
            border-radius: 2rem 2rem 0 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.95;
            margin-bottom: 1rem;
        }

        .hero-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        /* Content */
        .content-section {
            padding: 3rem 2rem 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.125rem;
            color: var(--text-light);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Login Cards */
        .login-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }        .login-card {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        [data-theme="dark"] .login-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--card-color);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .login-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.15);
            border-color: var(--card-color);
        }

        .login-card:hover::before {
            transform: scaleX(1);
        }

        .card-guru {
            --card-color: var(--guru-color);
        }

        .card-siswa {
            --card-color: var(--siswa-color);
        }

        .card-orangtua {
            --card-color: var(--orangtua-color);
        }        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--card-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--white);
            transition: transform 0.4s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .login-card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .card-description {
            font-size: 1rem;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .card-button {
            background: var(--card-color);
            color: var(--white);
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }

        .card-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: var(--white);
        }

        /* Features Section */
        .features-section {
            background: var(--bg-light);
            padding: 3rem 2rem;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
        }

        .features-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .feature-item {
            text-align: center;
            padding: 1.5rem;
        }        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
        }

        .feature-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }

        .feature-description {
            font-size: 0.95rem;
            color: var(--text-light);
            line-height: 1.6;
        }        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            background: var(--white);
            color: var(--text-light);
            font-size: 0.9rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .footer {
            border-top-color: rgba(255, 255, 255, 0.1);
        }        /* Responsive Design */
        @media (max-width: 768px) {
            .theme-toggle {
                top: 1rem;
                right: 1rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-section {
                padding: 2rem 1.5rem 3rem;
            }
            
            .content-section {
                padding: 2rem 1.5rem;
            }
            
            .login-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .login-card {
                padding: 2rem 1.5rem;
            }
        }        @media (max-width: 480px) {
            .theme-toggle {
                top: 0.75rem;
                right: 0.75rem;
                padding: 0.375rem;
            }
            
            .theme-switch {
                width: 50px;
                height: 25px;
            }
            
            .theme-switch::before {
                width: 19px;
                height: 19px;
                top: 3px;
                left: 3px;
            }
            
            .theme-switch.dark::before {
                transform: translateX(25px);
            }
            
            .main-container {
                padding: 1rem 0.5rem;
            }
            
            .welcome-card {
                border-radius: 1rem;
            }
            
            .hero-section {
                padding: 1.5rem 1rem 2.5rem;
            }
            
            .hero-title {
                font-size: 1.75rem;
            }
            
            .hero-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Theme Toggle -->
    <div class="theme-toggle">
        <button class="theme-switch" id="themeToggle" aria-label="Toggle dark mode">
            <i class="fas fa-sun sun-icon"></i>
            <i class="fas fa-moon moon-icon"></i>
        </button>
    </div>

    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
    </div>

    <div class="main-container">
        <div class="welcome-card">
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-content">
                    <div class="hero-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h1 class="hero-title">AbsensiPro</h1>
                    <p class="hero-subtitle">Sistem Absensi Sekolah Digital yang Modern, Akurat, dan Terpercaya</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="content-section">
                <h2 class="section-title">Selamat Datang!</h2>
                <p class="section-subtitle">
                    Pilih jenis pengguna untuk mengakses sistem absensi digital yang mudah dan efisien
                </p>

                <!-- Login Options -->
                <div class="login-grid">
                    <div class="login-card card-guru">
                        <div class="card-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3 class="card-title">Portal Guru</h3>
                        <p class="card-description">
                            Kelola absensi kelas, buat laporan kehadiran, dan pantau perkembangan siswa dengan mudah
                        </p>
                        <a href="{{ route('guru.login') }}" class="card-button">
                            <i class="fas fa-sign-in-alt"></i>
                            Masuk sebagai Guru
                        </a>
                    </div>

                    <div class="login-card card-siswa">
                        <div class="card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3 class="card-title">Portal Siswa</h3>
                        <p class="card-description">
                            Lihat riwayat kehadiran, jadwal pelajaran, dan informasi akademik terbaru
                        </p>
                        <a href="{{ route('siswa.login') }}" class="card-button">
                            <i class="fas fa-sign-in-alt"></i>
                            Masuk sebagai Siswa
                        </a>
                    </div>

                    <div class="login-card card-orangtua">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="card-title">Portal Orang Tua</h3>
                        <p class="card-description">
                            Pantau kehadiran anak, komunikasi dengan guru, dan terima notifikasi real-time
                        </p>
                        <a href="{{ route('orangtua.login') }}" class="card-button">
                            <i class="fas fa-sign-in-alt"></i>
                            Masuk sebagai Orang Tua
                        </a>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="features-section">
                    <h3 class="features-title">
                        <i class="fas fa-star" style="color: #fbbf24; margin-right: 0.5rem;"></i>
                        Keunggulan Sistem Kami
                    </h3>
                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="feature-title">Real-time Monitoring</h4>
                            <p class="feature-description">
                                Pantau kehadiran siswa secara langsung dengan sistem yang akurat dan responsif
                            </p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h4 class="feature-title">Laporan Komprehensif</h4>
                            <p class="feature-description">
                                Dapatkan laporan kehadiran yang detail dengan analisis dan visualisasi data
                            </p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h4 class="feature-title">Notifikasi Otomatis</h4>
                            <p class="feature-description">
                                Sistem notifikasi real-time untuk orang tua dan guru tentang kehadiran siswa
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} AbsensiPro. Dikembangkan dengan ❤️ untuk pendidikan Indonesia.</p>
            </div>
        </div>
    </div>    <script>
        // Theme Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            
            // Check for saved theme preference or default to light mode
            const savedTheme = localStorage.getItem('theme') || 'light';
            
            // Apply the saved theme
            if (savedTheme === 'dark') {
                html.setAttribute('data-theme', 'dark');
                themeToggle.classList.add('dark');
            }
            
            // Theme toggle event listener
            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                
                if (currentTheme === 'dark') {
                    // Switch to light mode
                    html.removeAttribute('data-theme');
                    themeToggle.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    // Switch to dark mode
                    html.setAttribute('data-theme', 'dark');
                    themeToggle.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            });

            // Add entrance animations to cards
            const cards = document.querySelectorAll('.login-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.2}s`;
                card.style.animation = 'slideInUp 0.8s ease-out forwards';
            });

            // Add hover sound effect (optional)
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click ripple effect
            const buttons = document.querySelectorAll('.card-button');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = button.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;
                    
                    button.style.position = 'relative';
                    button.style.overflow = 'hidden';
                    button.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        });

        // Add ripple animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>