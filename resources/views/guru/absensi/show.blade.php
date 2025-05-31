@extends('layouts.guru')

@section('title', 'Detail Absensi')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Absensi</h1>
        <div>
            <a href="{{ route('guru.absensi.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Detail Absensi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Absensi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Tanggal</th>
                        <td>{{ $absensi->tanggal->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <td>{{ $absensi->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td>{{ $absensi->jadwal->kelas->nama_kelas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jam Pelajaran</th>
                        <td>{{ date('H:i', strtotime($absensi->jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($absensi->jadwal->jam_selesai)) }}</td>
                    </tr>
                    <tr>
                        <th>Guru</th>
                        <td>{{ $absensi->guru->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($absensi->is_completed)
                                <span class="badge badge-success">Selesai</span>
                            @else
                                <span class="badge badge-warning">Sedang Berlangsung</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>    <!-- Detail Statistik -->
    <div class="row justify-content-center">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $totalSiswa }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hadir</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $hadir + $terlambat }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Izin/Sakit</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $izinSakit }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-notes-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Alpha</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $alpha }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Kehadiran -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kehadiran Siswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Keterangan</th>
                            @if(!$absensi->is_completed)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensiDetail as $index => $detail)
                        <tr class="{{ $detail->status == 'hadir' ? 'table-success' : ($detail->status == 'alpha' ? 'table-danger' : ($detail->status == 'terlambat' ? 'table-warning' : 'table-info')) }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->siswa->nisn ?? '-' }}</td>
                            <td>{{ $detail->siswa->nama_lengkap ?? 'Unknown' }}</td>                            <td>
                                @if($detail->status == 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif($detail->status == 'terlambat')
                                    <span class="badge badge-warning">Terlambat</span>
                                @elseif($detail->status == 'izin')
                                    <span class="badge badge-info">Izin</span>
                                @elseif($detail->status == 'sakit')
                                    <span class="badge badge-warning">Sakit</span>
                                @elseif($detail->status == 'alpha')
                                    <span class="badge badge-danger">Alpha</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($detail->status) }}</span>
                                @endif
                            </td>                            
                            <td>{{ $detail->created_at->format('H:i:s') }}</td>
                            <td>
                                @if($detail->keterangan && strpos($detail->keterangan, 'Terlambat') !== false && $detail->minutes_late)
                                    @php
                                        $minutes = abs($detail->minutes_late);
                                        $hours = floor($minutes / 60);
                                        $remainingMinutes = $minutes % 60;
                                        
                                        if ($hours > 0 && $remainingMinutes > 0) {
                                            $timeText = $hours . ' jam ' . $remainingMinutes . ' menit';
                                        } elseif ($hours > 0) {
                                            $timeText = $hours . ' jam';
                                        } else {
                                            $timeText = $remainingMinutes . ' menit';
                                        }
                                    @endphp
                                    Terlambat {{ $timeText }}
                                @else
                                    {{ $detail->keterangan ?? '-' }}
                                @endif
                            </td>
                            @if(!$absensi->is_completed)
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-toggle="modal" 
                                            data-target="#editStatusModal"
                                            data-detail-id="{{ $detail->id }}"
                                            data-nama="{{ $detail->siswa->nama_lengkap ?? 'Unknown' }}"
                                            data-status="{{ $detail->status }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editStatusForm" action="" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="editStatusModalLabel">Edit Status Kehadiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_siswa">Nama Siswa</label>
                        <input type="text" class="form-control" id="nama_siswa" readonly>
                    </div>
                    <div class="form-group">
                        <label for="status">Status Kehadiran</label>                        <select class="form-control" id="status" name="status" required>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan (opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .navbar, .no-print, footer, .btn, form, .modal {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #fff !important;
        }
        
        body {
            background-color: #fff !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
        
        // Handle edit status modal
        $('#editStatusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var detailId = button.data('detail-id');
            var nama = button.data('nama');
            var status = button.data('status');
            
            var modal = $(this);
            modal.find('#nama_siswa').val(nama);
            modal.find('#status').val(status);
            
            // Fix the route URL generation by using a JavaScript variable instead of blade directive
            let updateUrl = '{{ url("guru/absensi/detail") }}/' + detailId + '/update-status';
            modal.find('#editStatusForm').attr('action', updateUrl);
        });
    });
</script>
@endsection
