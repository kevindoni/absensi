@extends('layouts.guru')

@section('title', 'Detail Absensi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Absensi - {{ $jadwalMengajar->pelajaran->nama_pelajaran }}</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Kelas:</strong> {{ $jadwalMengajar->kelas->nama_kelas }}<br>
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensi as $abs)
                        <tr>
                            <td>{{ $abs->siswa->nisn }}</td>
                            <td>{{ $abs->siswa->nama_lengkap }}</td>
                            <td>
                                @if($abs->status === 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif($abs->status === 'terlambat')
                                    <span class="badge badge-warning">Terlambat</span>
                                @elseif($abs->status === 'izin')
                                    <span class="badge badge-info">Izin</span>
                                @elseif($abs->status === 'sakit')
                                    <span class="badge badge-primary">Sakit</span>
                                @elseif($abs->status === 'alpha')
                                    <span class="badge badge-danger">Alpha</span>
                                @else
                                    <span class="badge badge-secondary">{{ $abs->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($abs->status === 'terlambat' && $abs->keterangan)
                                    @php
                                        // Extract minutes from the keterangan
                                        preg_match('/(\d+)/', $abs->keterangan, $matches);
                                        $totalMenit = isset($matches[0]) ? (int)$matches[0] : 0;
                                        
                                        if ($totalMenit > 0) {
                                            $jam = floor($totalMenit / 60);
                                            $menit = $totalMenit % 60;
                                            $terlambatText = 'Terlambat ';
                                            
                                            if ($jam > 0) {
                                                $terlambatText .= $jam . ' jam ';
                                            }
                                            if ($menit > 0 || $jam == 0) {
                                                $terlambatText .= $menit . ' menit';
                                            }
                                            $terlambatText = trim($terlambatText);
                                        } else {
                                            $terlambatText = $abs->keterangan;
                                        }
                                    @endphp
                                    {{ $terlambatText }}
                                @else
                                    {{ $abs->keterangan ?: '-' }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection