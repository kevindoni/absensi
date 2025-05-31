@extends('layouts.orangtua')

@section('title', 'Detail Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Notifikasi</h6>
            <a href="{{ route('orangtua.notifikasi.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $notifikasi->judul }}</h5>
            <p class="text-muted small">
                {{ $notifikasi->created_at->translatedFormat('l, d F Y H:i') }}
            </p>
            <hr>
            <div class="card-text">
                {!! nl2br(e($notifikasi->pesan)) !!}
            </div>
            
            @if(!$notifikasi->read_at)
                <div class="mt-4">
                    <form action="{{ route('orangtua.notifikasi.read', $notifikasi->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-check fa-sm"></i> Tandai Sudah Dibaca
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
