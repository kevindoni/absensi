@extends('layouts.admin')

@section('title', 'Absensi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Absensi</h1>
    </div>

    <div class="row">
        <!-- Generate QR Code Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Generate QR Code Absensi</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.absensi.qrcode.generate') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                            <select class="form-control" id="kelas_id" name="kelas_id" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach(\App\Models\Kelas::all() as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-qrcode"></i> Generate QR Code
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Attendance Card -->
        <div class="col-lg-6 mb-4">
            <!-- Recent attendance content here -->
        </div>
    </div>

    <!-- Attendance List Card -->
    <div class="card shadow mb-4">
        <!-- Attendance list content here -->
    </div>
</div>
@endsection
