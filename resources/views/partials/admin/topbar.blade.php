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
    <ul class="navbar-nav ml-auto">        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                @php
                    $unreadCount = auth()->guard('admin')->user()->unreadNotifications->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge badge-danger badge-counter">
                        {{ $unreadCount > 5 ? '5+' : $unreadCount }}
                    </span>
                @endif
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Notifikasi
                </h6>
                @forelse(auth()->guard('admin')->user()->unreadNotifications->take(5) as $notification)
                    <a class="dropdown-item d-flex align-items-center notification-item" 
                       href="#" 
                       data-id="{{ $notification->id }}"
                       onclick="markAsRead('{{ $notification->id }}')">
                        <div class="mr-3">
                            <div class="icon-circle 
                                {{ $notification->data['type'] == 'warning' ? 'bg-warning' : 
                                   ($notification->data['type'] == 'success' ? 'bg-success' : 
                                   ($notification->data['type'] == 'danger' ? 'bg-danger' : 'bg-primary')) }}">
                                <i class="fas 
                                    {{ $notification->data['type'] == 'warning' ? 'fa-exclamation-triangle' : 
                                       ($notification->data['type'] == 'success' ? 'fa-check' : 
                                       ($notification->data['type'] == 'danger' ? 'fa-times' : 'fa-bell')) }} 
                                   text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ $notification->created_at->locale('id')->diffForHumans() }}</div>
                            <span class="font-weight-bold">{{ $notification->data['message'] }}</span>
                        </div>
                    </a>
                @empty
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div>
                            <span class="font-weight-bold">Tidak ada notifikasi baru</span>
                        </div>
                    </a>
                @endforelse
                <a class="dropdown-item text-center small text-gray-500" href="{{ route('admin.notifications.index') }}">Tampilkan Semua Notifikasi</a>
                @if($unreadCount > 0)
                    <a class="dropdown-item text-center small text-primary" href="#" 
                       onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();">
                        Tandai Semua Sudah Dibaca
                    </a>
                    <form id="mark-all-read-form" action="{{ route('admin.notifications.mark-all-as-read') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::guard('admin')->user()->name ?? 'Administrator' }}
                </span>
                <img class="img-profile rounded-circle" src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('admin.profil') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Pengaturan
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
