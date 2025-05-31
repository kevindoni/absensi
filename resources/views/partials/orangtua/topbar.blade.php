<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Info -->
    <div class="d-none d-sm-inline-block mr-auto ml-md-3 my-2 my-md-0">
        <div class="text-gray-600">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">{{ Auth::guard('orangtua')->user()->unreadNotifications->count() }}</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Notifikasi Terbaru
                </h6>
                @forelse(Auth::guard('orangtua')->user()->notifications->take(5) as $notification)
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('orangtua.notifikasi.show', $notification->id) }}">
                        <div class="mr-3">
                            <div class="icon-circle {{ $notification->read_at ? 'bg-secondary' : 'bg-primary' }}">
                                <i class="fas fa-bell text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                            <span class="{{ $notification->read_at ? '' : 'font-weight-bold' }}">{{ Str::limit($notification->data['message'], 50) }}</span>
                        </div>
                    </a>
                @empty
                    <a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada notifikasi baru</a>
                @endforelse
                <a class="dropdown-item text-center small text-gray-500" href="{{ route('orangtua.notifikasi.index') }}">Lihat Semua Notifikasi</a>
            </div>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">{{ Auth::guard('orangtua')->user()->unreadPesan ?? 0 }}</span>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                    Pesan Terbaru
                </h6>
                @forelse(Auth::guard('orangtua')->user()->pesanTerbaru ?? [] as $pesan)
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('orangtua.pesan.show', $pesan->id) }}">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="{{ asset('sbadmin2/img/undraw_profile_2.svg') }}" alt="User Avatar">
                            <div class="status-indicator {{ $pesan->read_at ? '' : 'bg-success' }}"></div>
                        </div>
                        <div>
                            <div class="text-truncate">{{ Str::limit($pesan->judul, 30) }}</div>
                            <div class="small text-gray-500">{{ $pesan->sender->nama ?? 'System' }} · {{ $pesan->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada pesan baru</a>
                @endforelse
                <a class="dropdown-item text-center small text-gray-500" href="{{ route('orangtua.pesan.index') }}">Lihat Semua Pesan</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::guard('orangtua')->user()->nama_lengkap ?? 'Orangtua' }}
                </span>
                <img class="img-profile rounded-circle" src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('orangtua.profil') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
