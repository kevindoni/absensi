@extends('layouts.admin')

@section('title', 'Jadwal Mengajar')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jadwal Mengajar</h1>
        <div>
            <a href="{{ route('admin.jadwal.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Jadwal
            </a>
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm ml-2" data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-filter fa-sm text-white-50"></i> Filter
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Mengajar</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Tampilan:</div>
                    <a class="dropdown-item" href="{{ route('admin.jadwal.index', ['view' => 'table']) }}">Tabel</a>
                    <a class="dropdown-item" href="{{ route('admin.jadwal.index', ['view' => 'grid']) }}">Grid</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(request('view') == 'grid')
                <!-- Grid View -->
                <div class="row">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $key => $hari)
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary h-100">
                                <div class="card-header py-2 bg-primary text-white">
                                    <h6 class="m-0 font-weight-bold">{{ $hari }}</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                    @php
                                        $jadwalHari = $jadwal->where('hari', $key + 1)->sortBy('jam_mulai');
                                    @endphp
                                    
                                    @forelse($jadwalHari as $j)
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $j->pelajaran->nama_pelajaran }}</h6>
                                                <small>{{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}</small>
                                            </div>
                                            <p class="mb-1">{{ $j->guru->nama_lengkap }}</p>
                                            <small>Kelas {{ $j->kelas->nama_kelas }}</small>
                                            <div class="mt-2">
                                                <a href="{{ route('admin.jadwal.edit', $j->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="list-group-item text-center py-3">
                                            Tidak ada jadwal
                                        </div>
                                    @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Table View (Default) -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Periode</th>
                                <th>Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwal as $key => $j)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $j->nama_hari }}</td>
                                <td>{{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}</td>
                                <td>
                                    @php
                                        $periods = $j->jam_ke_list ?? [];
                                        if (!is_array($periods) && is_string($periods)) {
                                            try {
                                                $periods = json_decode($periods, true) ?: [];
                                            } catch (\Exception $e) {
                                                $periods = [];
                                            }
                                        }
                                        
                                        if (count($periods) > 1) {
                                            echo 'Jam ke-' . min($periods) . ' s/d ' . max($periods);
                                        } elseif (count($periods) == 1) {
                                            echo 'Jam ke-' . $periods[0];
                                        } else {
                                            echo $j->jam_ke ? 'Jam ke-' . $j->jam_ke : '-';
                                        }
                                    @endphp
                                </td>
                                <td>{{ $j->guru->nama_lengkap }}</td>
                                <td>{{ $j->pelajaran->nama_pelajaran }}</td>
                                <td>{{ $j->kelas->nama_kelas }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.jadwal.edit', $j->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data jadwal mengajar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Jadwal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.jadwal.index') }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="filter_guru">Guru</label>
                        <select class="form-control" id="filter_guru" name="guru_id">
                            <option value="">Semua Guru</option>
                            @foreach(\App\Models\Guru::all() as $g)
                                <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filter_kelas">Kelas</label>
                        <select class="form-control" id="filter_kelas" name="kelas_id">
                            <option value="">Semua Kelas</option>
                            @foreach(\App\Models\Kelas::all() as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filter_hari">Hari</label>
                        <select class="form-control" id="filter_hari" name="hari">
                            <option value="">Semua Hari</option>
                            @php
                                $hariList = [
                                    1 => 'Senin',
                                    2 => 'Selasa',
                                    3 => 'Rabu',
                                    4 => 'Kamis',
                                    5 => 'Jumat',
                                    6 => 'Sabtu',
                                    7 => 'Minggu'
                                ];
                            @endphp
                            @foreach($hariList as $key => $h)
                                <option value="{{ $key }}" {{ request('hari') == $key ? 'selected' : '' }}>
                                    {{ $h }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <input type="hidden" name="view" value="{{ request('view') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request('guru_id') || request('kelas_id') || request('hari'))
                        <a href="{{ route('admin.jadwal.index', ['view' => request('view')]) }}" class="btn btn-danger">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[1, 'asc'], [2, 'asc']],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
@endsection
