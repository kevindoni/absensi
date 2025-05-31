@extends('layouts.orangtua')

@section('title', 'Detail Pesan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pesan</h6>
            <a href="{{ route('orangtua.pesan.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <h5 class="font-weight-bold">{{ $pesan->judul }}</h5>
                <p class="text-muted small mb-2">
                    Dikirim: {{ $pesan->created_at->format('d/m/Y H:i') }}
                    <span class="ml-2 badge badge-{{ $pesan->status_badge }}">
                        {{ $pesan->status_label }}
                    </span>
                </p>
                <div class="card bg-light">
                    <div class="card-body">
                        {!! nl2br(e($pesan->isi)) !!}
                    </div>
                </div>
            </div>

            @if($pesan->balasan)
                <div class="mt-4">
                    <h6 class="font-weight-bold text-primary">Riwayat Percakapan</h6>
                    
                    <!-- Balasan dari Admin/Guru -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <small class="text-muted">Dibalas pada: {{ $pesan->balasan_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($pesan->balasan)) !!}
                        </div>
                    </div>

                    @unless($pesan->status === 'diakhiri')
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('orangtua.pesan.reply', $pesan->id) }}" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Balas
                            </a>
                            <form action="{{ route('orangtua.pesan.end', $pesan->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary" onclick="return confirm('Akhiri percakapan ini?')">
                                    <i class="fas fa-times"></i> Akhiri Percakapan
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Percakapan telah diakhiri
                        </div>
                    @endunless
                </div>
            @else
                @unless($pesan->status === 'diakhiri')
                    <div class="mt-4">
                        <a href="{{ route('orangtua.pesan.reply', $pesan->id) }}" class="btn btn-primary">
                            <i class="fas fa-reply"></i> Balas Pesan
                        </a>
                    </div>
                @endunless
            @endif
        </div>
    </div>
</div>
@endsection
