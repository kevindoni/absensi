@extends('layouts.siswa')

@section('title', 'Riwayat Izin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Izin</h1>
        <a href="{{ route('siswa.izin.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Ajukan Izin
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($izinList as $izin)
                            <tr>
                                <td>{{ $izin->tanggal->format('d/m/Y') }}</td>
                                <td><span class="badge badge-{{ $izin->jenis == 'sakit' ? 'warning' : 'info' }}">
                                    {{ ucfirst($izin->jenis) }}
                                </span></td>
                                <td><span class="badge badge-{{ $izin->status == 'disetujui' ? 'success' : ($izin->status == 'ditolak' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($izin->status) }}
                                </span></td>
                                <td>{{ \Str::limit($izin->keterangan, 50) }}</td>
                                <td>
                                    <a href="{{ route('siswa.izin.show', $izin->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data izin</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $izinList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
