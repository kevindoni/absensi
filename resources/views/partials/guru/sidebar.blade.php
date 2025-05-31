<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('guru.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="sidebar-brand-text mx-3">AbsensiPro <sup>Guru</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Kehadiran
    </div>

    <!-- Nav Item - Presensi Guru -->
    <li class="nav-item {{ request()->routeIs('guru.presensi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.presensi.index') }}">
            <i class="fas fa-fw fa-user-check"></i>
            <span>Presensi Guru</span>
        </a>
    </li>

    <!-- Nav Item - Absensi Siswa -->
    <li class="nav-item {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.absensi.index') }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>Absensi Siswa</span>
        </a>
    </li>

    <!-- Nav Item - Izin Siswa -->
    <li class="nav-item {{ request()->routeIs('guru.izin.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.izin.index') }}">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Izin Siswa</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Laporan
    </div>

    <!-- Nav Item - Laporan -->
    <li class="nav-item {{ request()->routeIs('guru.laporan.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.laporan.index') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Laporan & Rekap</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Akun
    </div>

    <!-- Nav Item - Profil -->
    <li class="nav-item {{ request()->routeIs('guru.profil') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.profil') }}">
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
