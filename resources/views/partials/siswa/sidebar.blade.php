<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('siswa.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="sidebar-brand-text mx-3">AbsensiPro <sup>Siswa</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Absensi
    </div>

    <!-- Nav Item - Absensi -->
    <li class="nav-item {{ request()->routeIs('siswa.absensi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.absensi.index') }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>Riwayat Absensi</span>
        </a>
    </li>

    <!-- Nav Item - Izin -->
    <li class="nav-item {{ request()->routeIs('siswa.izin.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.izin.index') }}">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Pengajuan Izin</span>
        </a>
    </li>

    <!-- Nav Item - QR Code -->
    <li class="nav-item {{ request()->routeIs('siswa.qrcode.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.qrcode') }}">
            <i class="fas fa-fw fa-qrcode"></i>
            <span>QR Code Saya</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Akun
    </div>

    <!-- Nav Item - Profil -->
    <li class="nav-item {{ request()->routeIs('siswa.profil') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.profil') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Profil Saya</span>
        </a>
    </li>

    <!-- Nav Item - Logout -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
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
