@extends('layouts.siswa')

@section('title', 'Riwayat Absensi')

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
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $item)
                            <tr>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td>{{ $item->jadwal->pelajaran->nama_pelajaran ?? '-' }}</td>
                                <td>{{ $item->jadwal->kelas->nama_kelas ?? '-' }}</td>
                                <td>
                                    @if($item->status == 'hadir')
                                        <span class="badge badge-success">Hadir</span>
                                    @elseif($item->status == 'sakit')
                                        <span class="badge badge-warning">Sakit</span>
                                    @elseif($item->status == 'izin')
                                        <span class="badge badge-info">Izin</span>
                                    @else
                                        <span class="badge badge-danger">Alpha</span>
                                    @endif
                                </td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data absensi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $absensi->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
