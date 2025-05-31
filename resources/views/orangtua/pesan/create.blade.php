@extends('layouts.orangtua')

@section('title', 'Kirim Pesan Baru')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kirim Pesan Baru</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('orangtua.pesan.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="judul">Judul Pesan</label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                           id="judul" name="judul" value="{{ old('judul') }}" required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="isi">Isi Pesan</label>
                    <textarea class="form-control @error('isi') is-invalid @enderror" 
                              id="isi" name="isi" rows="5" required>{{ old('isi') }}</textarea>
                    @error('isi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                    <a href="{{ route('orangtua.pesan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
