@extends('layouts.guru')

@section('title', 'Ambil Absensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ambil Absensi Kelas</h1>
        <a href="{{ route('guru.absensi.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Jadwal</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">: {{ $jadwal->kelas->nama_kelas }}</dd>
                        
                        <dt class="col-sm-4">Mata Pelajaran</dt>
                        <dd class="col-sm-8">: {{ $jadwal->pelajaran->nama_pelajaran }}</dd>
                        
                        <dt class="col-sm-4">Hari</dt>
                        <dd class="col-sm-8">: {{ $jadwal->nama_hari }}</dd>
                    </dl>
                </div>
                
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Jam Pelajaran</dt>
                        <dd class="col-sm-8">: {{ $jadwal->periods ?? 'Jam ke-'.$jadwal->jam_ke }}</dd>
                        
                        <dt class="col-sm-4">Waktu</dt>
                        <dd class="col-sm-8">: {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</dd>
                        
                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi Siswa</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-sm btn-success mr-2" id="qr-scan-btn">
                    <i class="fas fa-qrcode fa-sm fa-fw"></i> Scan QR Code
                </button>
                <a href="#" class="btn btn-sm btn-info" id="quick-actions-btn" data-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-white-50"></i> Tindakan Cepat
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <div class="dropdown-header">Tandai Semua Sebagai:</div>
                    <a class="dropdown-item mark-all" data-status="hadir" href="javascript:void(0)">
                        <i class="fas fa-check fa-sm fa-fw text-success"></i> Hadir
                    </a>
                    <a class="dropdown-item mark-all" data-status="izin" href="javascript:void(0)">
                        <i class="fas fa-envelope fa-sm fa-fw text-info"></i> Izin
                    </a>
                    <a class="dropdown-item mark-all" data-status="sakit" href="javascript:void(0)">
                        <i class="fas fa-thermometer fa-sm fa-fw text-warning"></i> Sakit
                    </a>
                    <a class="dropdown-item mark-all" data-status="alpha" href="javascript:void(0)">
                        <i class="fas fa-times fa-sm fa-fw text-danger"></i> Alpha
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- QR Scanner Modal -->
            <div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="qrScannerModalLabel">Scan QR Code Siswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <p><i class="fas fa-info-circle"></i> Arahkan kamera ke QR code pada kartu siswa untuk mengisi absensi secara otomatis.</p>
                                    </div>
                                    <div id="reader" style="width: 100%"></div>
                                    <div id="qr-result-container" class="mt-3" style="display: none;">
                                        <div class="alert alert-success" id="qr-success-alert" style="display: none;">
                                            <span id="qr-success-message"></span>
                                        </div>
                                        <div class="alert alert-danger" id="qr-error-alert" style="display: none;">
                                            <span id="qr-error-message"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($siswa->count() > 0)
                <form action="{{ route('guru.absensi.store') }}" method="POST" id="absensiForm">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Nama Siswa</th>
                                    <th width="15%">NIS/NISN</th>
                                    <th width="25%">Status</th>
                                    <th width="20%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswa as $index => $s)
                                <tr data-siswa-id="{{ $s->id }}" data-siswa-nisn="{{ $s->nisn }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $s->nama_lengkap }}</td>
                                    <td>
                                        @if(!empty($s->nis) && !empty($s->nisn))
                                            {{ $s->nis }} / {{ $s->nisn }}
                                        @elseif(!empty($s->nis))
                                            {{ $s->nis }}
                                        @elseif(!empty($s->nisn))
                                            {{ $s->nisn }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <input type="hidden" name="siswa[{{ $index }}][id]" value="{{ $s->id }}">
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-success active">
                                                <input type="radio" name="siswa[{{ $index }}][status]" value="hadir" checked> Hadir
                                            </label>
                                            <label class="btn btn-outline-info">
                                                <input type="radio" name="siswa[{{ $index }}][status]" value="izin"> Izin
                                            </label>
                                            <label class="btn btn-outline-warning">
                                                <input type="radio" name="siswa[{{ $index }}][status]" value="sakit"> Sakit
                                            </label>
                                            <label class="btn btn-outline-danger">
                                                <input type="radio" name="siswa[{{ $index }}][status]" value="alpha"> Alpha
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm keterangan-field" name="siswa[{{ $index }}][keterangan]" placeholder="Keterangan (opsional)">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Data Absensi
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning">
                    Tidak ada siswa yang terdaftar di kelas ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .btn-group-toggle .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    .keterangan-field {
        display: none;
    }
    
    .btn-outline-info.active, 
    .btn-outline-warning.active, 
    .btn-outline-danger.active {
        color: white;
    }
</style>
@endsection

@section('page_scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    $(document).ready(function() {
        // Show keterangan field when status other than 'hadir' is selected
        $('input[type=radio][name^="siswa"]').change(function() {
            const keteranganField = $(this).closest('tr').find('.keterangan-field');
            if (this.value !== 'hadir') {
                keteranganField.slideDown();
                keteranganField.attr('required', true);
            } else {
                keteranganField.slideUp();
                keteranganField.attr('required', false);
            }
        });
        
        // Mark all students with specified status
        $('.mark-all').click(function() {
            const status = $(this).data('status');
            $('input[type=radio][value="' + status + '"]').prop('checked', true).change();
            
            // Update bootstrap active classes
            $('input[type=radio][name^="siswa"]').each(function() {
                $(this).parent().removeClass('active');
                if ($(this).val() === status && $(this).prop('checked')) {
                    $(this).parent().addClass('active');
                }
            });
        });        // QR Code scanner functionality
        let html5QrcodeScanner = null;
        
        // Function to play beep sound
        function playBeepSound() {
            try {
                const audio = new Audio('/sounds/beep.mp3');
                audio.volume = 0.5; // Set volume to 50%
                audio.play().catch(err => {
                    console.log('Could not play beep sound:', err);
                });
            } catch (error) {
                console.log('Error creating audio:', error);
            }
        }
        
        $('#qr-scan-btn').click(function() {
            $('#qrScannerModal').modal('show');
            
            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", 
                    { 
                        fps: 10, 
                        qrbox: 250,
                        rememberLastUsedCamera: true,
                        aspectRatio: 1.0
                    }
                );
                
                function onScanSuccess(decodedText, decodedResult) {
                    // Try to parse the QR code content
                    try {
                        // Assuming the QR code contains the NISN or a JSON with NISN
                        let nisn = decodedText;
                        
                        // If it's a JSON string, try to parse it
                        if (decodedText.startsWith('{') && decodedText.endsWith('}')) {
                            const qrData = JSON.parse(decodedText);
                            nisn = qrData.nisn || qrData.NISN || qrData.id;
                        }
                        
                        // Find the row with this NISN
                        const row = $(`tr[data-siswa-nisn="${nisn}"]`);
                        
                        if (row.length) {
                            // Mark student as present
                            const radioButton = row.find('input[type=radio][value="hadir"]');
                            radioButton.prop('checked', true);
                            radioButton.closest('label').addClass('active')
                                     .siblings().removeClass('active');
                                     
                            // Show success message
                            $('#qr-success-alert').show();
                            $('#qr-error-alert').hide();
                            $('#qr-success-message').text(`Berhasil! Kehadiran ${row.find('td:eq(1)').text()} telah dicatat.`);
                            $('#qr-result-container').show();
                            
                            // Optional: Highlight the row
                            row.addClass('table-success');
                            setTimeout(() => {
                                row.removeClass('table-success');
                            }, 3000);
                        } else {
                            // Show error if NISN not found
                            $('#qr-error-alert').show();
                            $('#qr-success-alert').hide();
                            $('#qr-error-message').text('Siswa dengan NISN tersebut tidak ditemukan dalam daftar kelas ini.');
                            $('#qr-result-container').show();
                        }
                    } catch (e) {
                        // Show error for invalid QR code
                        $('#qr-error-alert').show();
                        $('#qr-success-alert').hide();
                        $('#qr-error-message').text('QR Code tidak valid: ' + e.message);
                        $('#qr-result-container').show();
                    }
                }

                function onScanFailure(error) {
                    // Handle scan failure - usually not needed
                }

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        });
        
        // Stop scanner when modal is closed
        $('#qrScannerModal').on('hidden.bs.modal', function () {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
            $('#qr-result-container').hide();
            $('#qr-success-alert').hide();
            $('#qr-error-alert').hide();
        });
    });
</script>
@endsection
