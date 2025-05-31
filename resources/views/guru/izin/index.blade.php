@extends('layouts.guru')

@section('title', 'Data Izin Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Izin Siswa</h1>
        <a href="{{ route('guru.izin.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Input Izin Baru
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Data Izin -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Izin/Sakit Siswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Bukti Surat</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($izin as $data)
                        <tr>
                            <td>{{ $data->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $data->siswa->nama_lengkap ?? '-' }}</td>
                            <td>{{ $data->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>
                                @if($data->status == 'izin')
                                    <span class="badge badge-info">Izin</span>
                                @elseif($data->status == 'sakit')
                                    <span class="badge badge-warning">Sakit</span>
                                @endif
                            </td>
                            <td>
                                @if($data->bukti_surat)
                                    <a href="{{ asset('storage/surat_izin/' . $data->bukti_surat) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file"></i> Lihat
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $data->keterangan ?? '-' }}</td>
                            <td>
                                <a href="{{ route('guru.izin.show', $data->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $izin->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": false,
            "info": false,
            "searching": true,
            "ordering": true,
            "order": [[0, 'desc']]
        });
    });
</script>
@endsection
