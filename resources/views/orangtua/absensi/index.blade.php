@extends('layouts.orangtua')

@section('title', 'Rekap Absensi Anak')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rekap Absensi Anak</h6>
        </div>
        <div class="card-body">
            @if($absensi->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-calendar-check fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada data absensi</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Anak</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absensi as $item)
                                <tr>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $item->siswa->nama_lengkap }}</td>
                                    <td>{{ $item->jadwal->kelas->nama_kelas ?? '-' }}</td>
                                    <td>{{ $item->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $item->status === 'hadir' ? 'success' : 
                                            ($item->status === 'sakit' ? 'warning' : 
                                            ($item->status === 'izin' ? 'info' : 'danger')) 
                                        }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $absensi->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
