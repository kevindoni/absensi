<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-school"></i>
        </div>
        <div class="sidebar-brand-text mx-3">AbsensiPro <sup>Admin</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Master Data
    </div>

    <!-- Nav Item - Kelas -->
    <li class="nav-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.kelas.index') }}">
            <i class="fas fa-fw fa-school"></i>
            <span>Data Kelas</span>
        </a>
    </li>

    <!-- Nav Item - Guru -->
    <li class="nav-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.guru.index') }}">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Data Guru</span>
        </a>
    </li>

    <!-- Nav Item - Tahun Ajaran -->
    <li class="nav-item {{ request()->routeIs('admin.academic-year.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.academic-year.index') }}">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Tahun Ajaran</span>
        </a>
    </li>

    <!-- Nav Item - Siswa -->
    <li class="nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.siswa.index') }}">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Data Siswa</span>
        </a>
    </li>

    <!-- Nav Item - Pelajaran -->
    <li class="nav-item {{ request()->routeIs('admin.pelajaran.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.pelajaran.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Data Pelajaran</span>
        </a>
    </li>

    <!-- Nav Item - Jadwal -->
    <li class="nav-item {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.jadwal.index') }}">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Jadwal Mengajar</span>
        </a>
    </li>

    <!-- Nav Item - Orang Tua -->
    <li class="nav-item {{ request()->routeIs('admin.orangtua.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.orangtua.index') }}">
            <i class="fas fa-fw fa-user-friends"></i>
            <span>Data Orang Tua</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        QR Code
    </div>

    <!-- Nav Item - QR Code Management -->
    <li class="nav-item {{ request()->routeIs('admin.qrcode.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.qrcode.index') }}">
            <i class="fas fa-fw fa-qrcode"></i>
            <span>Manajemen QR Code</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Laporan
    </div>

    <!-- Nav Item - Laporan -->
    <li class="nav-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.laporan.index') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Laporan</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Pengaturan
    </div>

    <!-- Nav Item - Notifikasi Settings -->
    <li class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.notifications.settings') }}">
            <i class="fas fa-fw fa-bell"></i>
            <span>Pengaturan Notifikasi</span>
        </a>
    </li>
    <!-- Nav Item - System Settings -->
    <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.settings.index') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Pengaturan Sistem</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->
