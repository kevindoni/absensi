@extends('layouts.guru')

@section('title', 'Input Izin Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Input Izin Siswa</h1>
        <a href="{{ route('guru.izin.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Input Izin -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Input Izin</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.izin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                            <select class="form-control" id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="siswa_id">Siswa <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="siswa_id" name="siswa_id" required>
                                <option value="">Pilih Siswa</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusIzin" value="izin" checked>
                                <label class="form-check-label" for="statusIzin">
                                    Izin
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusSakit" value="sakit">
                                <label class="form-check-label" for="statusSakit">
                                    Sakit
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="keterangan">Keterangan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required>{{ old('keterangan') }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="bukti_surat">Bukti Surat (jpg, png, pdf, maks 2MB)</label>
                            <input type="file" class="form-control-file" id="bukti_surat" name="bukti_surat">
                            <small class="form-text text-muted">Upload surat izin atau surat dokter.</small>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize select2 for student selection
    $('#siswa_id').select2({
        placeholder: "Pilih siswa...",
        allowClear: true,
        width: '100%'
    });
    
    // Handle class change
    $('#kelas_id').on('change', function() {
        const kelasId = $(this).val();
        if (kelasId) {
            // Fetch students for the selected class
            $.ajax({
                url: "{{ route('guru.search.students-by-class') }}",
                type: "GET",
                data: { kelas_id: kelasId },
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Pilih siswa...</option>';
                        
                        // Add student options
                        response.data.forEach(function(student) {
                            options += `<option value="${student.id}">${student.nama_lengkap} (${student.nisn})</option>`;
                        });
                        
                        // Update the student select
                        $('#siswa_id').html(options);
                    }
                },
                error: function(error) {
                    console.error("Error fetching students:", error);
                }
            });
        } else {
            // Reset student select if no class is selected
            $('#siswa_id').html('<option value="">Pilih siswa...</option>');
        }
    });
});
</script>
@endsection
