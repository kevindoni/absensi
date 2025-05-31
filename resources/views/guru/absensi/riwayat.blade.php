@extends('layouts.guru')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Absensi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatAbsensi as $absensi)
                        <tr>
                            <td>{{ $absensi->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $absensi->jadwal->kelas->nama_kelas }}</td>
                            <td>{{ $absensi->jadwal->pelajaran->nama_pelajaran }}</td>
                            <td>
                                <span class="badge badge-{{ $absensi->is_completed ? 'success' : 'warning' }}">
                                    {{ $absensi->is_completed ? 'Selesai' : 'Belum Selesai' }}
                                </span>
                            </td>
                            <td>{{ $absensi->keterangan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $riwayatAbsensi->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
