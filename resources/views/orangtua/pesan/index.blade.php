@extends('layouts.orangtua')

@section('title', 'Pesan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pesan</h1>
        <a href="{{ route('orangtua.pesan.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Kirim Pesan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($pesan->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-envelope-open fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada pesan</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Status</th>
                                <th>Balasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesan as $item)
                                <tr>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $item->judul }}</td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $item->status === 'dibalas' ? 'success' : 
                                            ($item->status === 'dibaca' ? 'info' : 'warning') 
                                        }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->balasan)
                                            <small class="text-muted">{{ \Str::limit($item->balasan, 50) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('orangtua.pesan.show', $item->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $pesan->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
