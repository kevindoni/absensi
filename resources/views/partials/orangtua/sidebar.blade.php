<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('orangtua.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-friends"></i>
        </div>
        <div class="sidebar-brand-text mx-3">AbsensiPro <sup>Ortu</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('orangtua.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('orangtua.dashboard') }}">
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
    <li class="nav-item {{ request()->routeIs('orangtua.absensi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('orangtua.absensi.index') }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>Absensi Anak</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Komunikasi
    </div>

    <!-- Nav Item - Notifikasi -->
    <li class="nav-item {{ request()->routeIs('orangtua.notifikasi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('orangtua.notifikasi.index') }}">
            <i class="fas fa-fw fa-bell"></i>
            <span>Notifikasi</span>
        </a>
    </li>



    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Akun
    </div>

    <!-- Nav Item - Profil -->
    <li class="nav-item {{ request()->routeIs('orangtua.profil') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('orangtua.profil') }}">
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
