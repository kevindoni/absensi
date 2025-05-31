@extends('layouts.orangtua')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Notifikasi</h6>
        </div>
        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada notifikasi</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <a href="{{ route('orangtua.notifikasi.show', $notification->id) }}" 
                           class="list-group-item list-group-item-action {{ !$notification->read_at ? 'bg-light font-weight-bold' : '' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notifikasi' }}</h6>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
                            <small class="text-muted">
                                @if($notification->read_at)
                                    <i class="fas fa-check-double"></i> Dibaca {{ $notification->read_at->diffForHumans() }}
                                @else
                                    <i class="fas fa-check"></i> Belum dibaca
                                @endif
                            </small>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
