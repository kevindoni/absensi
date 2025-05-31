@extends('layouts.guru')

@section('title', 'Edit Absensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Absensi</h1>
        <a href="{{ route('guru.absensi.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Absensi</h6>
        </div>
        <div class="card-body">
            @php
                // Get the first absensi record to extract jadwal information
                $firstAbsensi = $absensi->first();
                $jadwal = null;
                
                // Check if the relationship method exists before trying to access it
                if ($firstAbsensi && method_exists($firstAbsensi, 'jadwal')) {
                    $jadwal = $firstAbsensi->jadwal;
                }
            @endphp
            
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</dd>
                        
                        <dt class="col-sm-4">Mata Pelajaran</dt>
                        <dd class="col-sm-8">: {{ $jadwal && isset($jadwal->pelajaran) ? $jadwal->pelajaran->nama_pelajaran : '-' }}</dd>
                    </dl>
                </div>
                
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">: {{ $jadwal && isset($jadwal->kelas) ? $jadwal->kelas->nama_kelas : '-' }}</dd>
                        
                        <dt class="col-sm-4">Total Siswa</dt>
                        <dd class="col-sm-8">: {{ $absensi->count() }} siswa</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Absensi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.absensi.update', $tanggal) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Nama Siswa</th>
                                <th width="10%">NISN</th>
                                <th width="30%">Status</th>
                                <th width="20%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absensi as $index => $a)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $a->siswa->nama_lengkap }}</td>
                                <td>{{ $a->siswa->nisn }}</td>
                                <td>
                                    <input type="hidden" name="siswa[{{ $index }}][id]" value="{{ $a->id }}">
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-success {{ $a->status == 'hadir' ? 'active' : '' }}">
                                            <input type="radio" name="siswa[{{ $index }}][status]" value="hadir" {{ $a->status == 'hadir' ? 'checked' : '' }}> Hadir
                                        </label>
                                        <label class="btn btn-outline-info {{ $a->status == 'izin' ? 'active' : '' }}">
                                            <input type="radio" name="siswa[{{ $index }}][status]" value="izin" {{ $a->status == 'izin' ? 'checked' : '' }}> Izin
                                        </label>
                                        <label class="btn btn-outline-warning {{ $a->status == 'sakit' ? 'active' : '' }}">
                                            <input type="radio" name="siswa[{{ $index }}][status]" value="sakit" {{ $a->status == 'sakit' ? 'checked' : '' }}> Sakit
                                        </label>
                                        <label class="btn btn-outline-danger {{ $a->status == 'alpha' ? 'active' : '' }}">
                                            <input type="radio" name="siswa[{{ $index }}][status]" value="alpha" {{ $a->status == 'alpha' ? 'checked' : '' }}> Alpha
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm keterangan-field {{ in_array($a->status, ['izin', 'sakit', 'alpha']) ? '' : 'd-none' }}" 
                                           name="siswa[{{ $index }}][keterangan]" 
                                           value="{{ $a->keterangan }}" 
                                           placeholder="Keterangan">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 text-center">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('guru.absensi.show', $tanggal) }}" class="btn btn-secondary px-5">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
            </form>
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
    
    .btn-outline-info.active, 
    .btn-outline-warning.active, 
    .btn-outline-danger.active {
        color: white;
    }
</style>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Show/hide keterangan field based on selected status
        $('input[type=radio][name^="siswa"]').change(function() {
            const keteranganField = $(this).closest('tr').find('.keterangan-field');
            if (this.value !== 'hadir') {
                keteranganField.removeClass('d-none');
            } else {
                keteranganField.addClass('d-none');
                keteranganField.val('');
            }
        });
        
        // Add quick actions to mark all with a specific status
        $('.mark-all').click(function(e) {
            e.preventDefault();
            const status = $(this).data('status');
            
            // Set all radio buttons with this status to checked
            $('input[type=radio][value="' + status + '"]').prop('checked', true);
            
            // Update Bootstrap's active classes on the labels
            $('.btn-group-toggle .btn').removeClass('active');
            $('input[type=radio][value="' + status + '"]').closest('label').addClass('active');
            
            // Show/hide keterangan fields
            if (status !== 'hadir') {
                $('.keterangan-field').removeClass('d-none');
            } else {
                $('.keterangan-field').addClass('d-none').val('');
            }
        });
    });
</script>
@endsection
