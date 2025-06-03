@extends('layouts.admin')

@section('title', 'Manajemen QR Code')

@section('styles')
<style>
    .action-buttons .btn {
        margin-bottom: 5px;
    }
    .card-qr {
        transition: all 0.3s;
    }
    .card-qr:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .qr-preview {
        max-width: 150px;
        margin: 0 auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen QR Code</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.qrcode.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Analytics QR Code
            </a>
            <a href="{{ route('admin.qrcode.settings') }}" class="btn btn-outline-primary">
                <i class="fas fa-cogs"></i> Pengaturan
            </a>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Pengaturan QR Code</h6>
        </div>
        <div class="card-body">
            <p>Kelola pengaturan QR code seperti masa berlaku, validasi jadwal, dll.</p>
            <a href="{{ route('admin.qrcode.settings') }}" class="btn btn-primary">
                <i class="fas fa-cogs"></i> Buka Pengaturan QR Code
            </a>
        </div>
    </div>

    <!-- QR Code Actions -->
    <div class="row">
        <!-- Generate QR for Class -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 card-qr border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">QR Code Kelas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p>Generate QR Code untuk seluruh siswa dalam satu kelas. QR Code akan dicetak dalam format PDF.</p>
                            <div class="form-group">
                                <label for="kelas_id">Pilih Kelas</label>
                                <select id="kelas_id" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="btnCetakQR" class="btn btn-primary">
                                <i class="fas fa-print"></i> Cetak QR Code Kelas
                            </button>
                            
                            <form action="{{ route('admin.qrcode.bulk-reset') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="reset_kelas_id">Reset QR Code Kelas</label>
                                    <select name="kelas_id" id="reset_kelas_id" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Yakin ingin mereset QR Code seluruh siswa di kelas ini?')">
                                    <i class="fas fa-sync"></i> Reset QR Code Kelas
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4 text-center">                            
                            <div class="qr-preview mt-3">
                                @php
                                $sampleQrCode = App\Http\Controllers\QrController::generateStandardQrCode(
                                    new App\Models\Siswa(['qr_token' => 'Sample QR Code']), 
                                    150
                                );
                                @endphp
                                {!! $sampleQrCode !!}
                                <div class="text-center mt-2">
                                    <small class="text-muted">Contoh QR Code</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Generate QR for Individual -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 card-qr border-left-success">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">QR Code Individual</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p>Generate QR Code untuk siswa secara individual. Cari siswa dan cetak QR Code.</p>
                            <div class="form-group">
                                <label for="siswa_search">Cari Siswa</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="siswa_search" placeholder="Masukkan nama atau NISN...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="btn_search">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="search_results" class="list-group mb-3" style="max-height: 200px; overflow-y: auto;">
                                <!-- Search results will appear here -->
                            </div>
                            
                            <div class="action-buttons">
                                <button type="button" id="btn_generate_qr" class="btn btn-success btn-block mb-2" disabled>
                                    <i class="fas fa-qrcode"></i> Generate QR Code
                                </button>
                                
                                <button type="button" id="btn_reset_qr" class="btn btn-warning btn-block" disabled>
                                    <i class="fas fa-sync"></i> Reset QR Code
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="qr-preview mt-3">
                                @php
                                $sampleQrCode = QrCode::size(150) // Reduced from 200 to 150
                        ->errorCorrection('H')
                        ->generate('Student QR Code');
                                @endphp
                                {!! $sampleQrCode !!}
                                <div class="text-center mt-2">
                                    <small class="text-muted">Contoh QR Code Siswa</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Batch Operations -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Operasi Massal</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Reset Semua QR Code</h5>
                            <p class="card-text">Mereset QR Code untuk seluruh siswa di semua kelas.</p>
                            <form action="{{ route('admin.qrcode.bulk-reset') }}" method="POST">
                                @csrf
                                <input type="hidden" name="all_students" value="1">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin mereset QR Code untuk SEMUA siswa?')">
                                    <i class="fas fa-exclamation-triangle"></i> Reset Semua QR Code
                                </button>
                            </form>
                        </div>
                    </div>
                </div>                
                <div class="col-md-6">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Validasi QR Code</h5>
                            <p class="card-text">Pindai QR Code untuk memvalidasi keabsahan data siswa.</p>
                            <a href="{{ route('admin.qrcode.validate', ['qrToken' => 'scan']) }}" class="btn btn-info">
                                <i class="fas fa-qrcode"></i> Mulai Pemindaian QR
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Search functionality
        $('#btn_search').on('click', function() {
            searchSiswa();
        });
        
        $('#siswa_search').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchSiswa();
            }
        });
        
        function searchSiswa() {
            const query = $('#siswa_search').val();
            if (query.length < 3) {
                alert('Masukkan minimal 3 karakter untuk mencari');
                return;
            }
            
            // Show loading indicator
            $('#search_results').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>');
            
            $.ajax({
                url: "{{ route('admin.siswa.search') }}",
                type: "GET",
                data: { q: query },
                success: function(response) {
                    let html = '';
                    
                    if (response.data.length === 0) {
                        html = '<div class="alert alert-info">Tidak ada hasil ditemukan</div>';
                    } else {
                        response.data.forEach(function(siswa) {
                            html += `
                                <a href="#" class="list-group-item list-group-item-action siswa-item" 
                                   data-id="${siswa.id}" data-name="${siswa.nama_lengkap}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">${siswa.nama_lengkap}</h6>
                                        <small>${siswa.nisn}</small>
                                    </div>
                                    <small>Kelas: ${siswa.kelas ? siswa.kelas.nama_kelas : 'N/A'}</small>
                                </a>
                            `;
                        });
                    }
                    
                    $('#search_results').html(html);
                    
                    // Attach click event to search results
                    $('.siswa-item').on('click', function(e) {
                        e.preventDefault();
                        $('.siswa-item').removeClass('active');
                        $(this).addClass('active');
                        
                        // Enable action buttons
                        $('#btn_generate_qr, #btn_reset_qr').prop('disabled', false);
                        
                        // Store selected student ID
                        localStorage.setItem('selectedSiswaId', $(this).data('id'));
                        localStorage.setItem('selectedSiswaName', $(this).data('name'));
                    });
                },
                error: function(error) {
                    $('#search_results').html('<div class="alert alert-danger">Error saat mencari data</div>');
                    console.error(error);
                }
            });
        }
        
        // Generate QR code button
        $('#btn_generate_qr').on('click', function() {
            const siswaId = localStorage.getItem('selectedSiswaId');
            if (siswaId) {
                window.open("{{ url('admin/siswa') }}/" + siswaId + "/qrcode", "_blank");
            }
        });
        
        // Reset QR code button
        $('#btn_reset_qr').on('click', function() {
            const siswaId = localStorage.getItem('selectedSiswaId');
            const siswaName = localStorage.getItem('selectedSiswaName');
            
            if (siswaId && confirm('Yakin ingin mereset QR Code untuk ' + siswaName + '?')) {
                $.ajax({
                    url: "{{ url('admin/siswa') }}/" + siswaId + "/reset-qrcode",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PATCH"
                    },
                    success: function(response) {
                        alert('QR Code berhasil direset');
                    },
                    error: function(error) {
                        alert('Gagal mereset QR Code');
                        console.error(error);
                    }
                });
            }
        });
        
        // Print QR Code for Class
        $('#btnCetakQR').on('click', function() {
            const kelasId = $('#kelas_id').val();
            if (kelasId) {
                window.open("{{ url('admin/kelas') }}/" + kelasId + "/qrcodes", "_blank");
            }
        });

        // Before form submit
        $('form').on('submit', function() {
            // Enable all fields before submitting
            $('#qr_validity_period').prop('disabled', false);
        });
    });
</script>
@endsection
