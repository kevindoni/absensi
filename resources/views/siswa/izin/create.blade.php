@extends('layouts.siswa')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Pengajuan Izin</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.izin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                           id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="jenis">Jenis</label>
                    <select class="form-control @error('jenis') is-invalid @enderror" 
                            id="jenis" name="jenis" required>
                        <option value="">Pilih Jenis Izin</option>
                        <option value="izin" {{ old('jenis') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ old('jenis') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                    @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                              id="keterangan" name="keterangan" rows="3" required>{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="bukti">Bukti (Opsional)</label>
                    <input type="file" class="form-control-file @error('bukti') is-invalid @enderror" 
                           id="bukti" name="bukti" accept="image/*,application/pdf">
                    <small class="form-text text-muted">Format: JPG, PNG, atau PDF. Maksimal 2MB.</small>
                    @error('bukti')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('siswa.izin.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
