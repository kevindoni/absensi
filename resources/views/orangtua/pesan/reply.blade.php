@extends('layouts.orangtua')

@section('title', 'Balas Pesan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Balas Pesan</h6>
            <a href="{{ route('orangtua.pesan.show', $pesan->id) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <!-- Tampilkan Pesan Asli -->
                <h6 class="font-weight-bold text-primary">Riwayat Percakapan</h6>
                
                <!-- Pesan Asli -->
                <div class="card border-left-primary mb-3">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $pesan->judul }}</h6>
                            <small class="text-muted">{{ $pesan->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        {!! nl2br(e($pesan->isi)) !!}
                    </div>
                </div>

                <!-- Balasan Sebelumnya (jika ada) -->
                @if($pesan->balasan)
                    <div class="card border-left-info mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Balasan Admin</h6>
                                <small class="text-muted">{{ $pesan->balasan_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($pesan->balasan)) !!}
                        </div>
                    </div>
                @endif

                <!-- Form Balasan -->
                @if($pesan->status !== 'diakhiri')
                    <form action="{{ route('orangtua.pesan.storeReply', $pesan->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="balasan" class="font-weight-bold text-primary">Balas Pesan:</label>
                            <textarea class="form-control @error('balasan') is-invalid @enderror" 
                                    id="balasan" name="balasan" rows="5" required>{{ old('balasan') }}</textarea>
                            @error('balasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim Balasan
                            </button>
                            <a href="{{ route('orangtua.pesan.show', $pesan->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Percakapan ini telah diakhiri
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
