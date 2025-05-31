@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Notifikasi</h1>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Notifications Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Semua Notifikasi</h6>
            @if(auth()->guard('admin')->user()->unreadNotifications->count() > 0)
                <form action="{{ route('admin.notifications.mark-all-as-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-check-double"></i> Tandai Semua Sudah Dibaca
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            <div class="list-group">
                @forelse($notifications as $notification)
                    <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'unread-notification' }}">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $notification->data['title'] ?? 'Notifikasi' }}</h5>
                            <small>{{ $notification->created_at->locale('id')->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $notification->data['message'] }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-{{ $notification->data['type'] ?? 'primary' }}">
                                <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }} mr-1"></i>
                                {{ ucfirst($notification->data['type'] ?? 'info') }}
                            </small>
                            @if(!$notification->read_at)
                                <button class="btn btn-sm btn-outline-primary mark-as-read" 
                                        data-id="{{ $notification->id }}"
                                        onclick="markAsRead('{{ $notification->id }}', this)">
                                    Tandai Sudah Dibaca
                                </button>
                            @else
                                <span class="badge badge-light">Sudah Dibaca</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-3x text-gray-300 mb-3"></i>
                        <p>Tidak ada notifikasi.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>

</div>

<style>
.unread-notification {
    background-color: rgba(0, 123, 255, 0.05);
    border-left: 4px solid #4e73df;
}
</style>
@endsection

@section('scripts')
<script>
    function markAsRead(id, button) {
        $.ajax({
            url: '{{ route("admin.notifications.mark-as-read") }}',
            type: 'POST',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (button) {
                        $(button).closest('.list-group-item').removeClass('unread-notification');
                        $(button).replaceWith('<span class="badge badge-light">Sudah Dibaca</span>');
                    }
                }
            }
        });
    }
</script>
@endsection
