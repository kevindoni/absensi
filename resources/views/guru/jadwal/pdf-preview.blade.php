@extends('layouts.guru')

@section('title', 'Preview PDF Jadwal')

@section('styles')
<style>
    .pdf-preview {
        background: white;
        max-width: 210mm;
        margin: 20px auto;
        padding: 15mm;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
    }
    
    .preview-actions {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .preview-actions h6 {
        margin-bottom: 10px;
        color: #5a5c69;
        font-weight: 600;
    }
    
    .letterhead {
        text-align: center;
        border-bottom: 3px double #000;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    
    .letterhead h1 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .letterhead h2 {
        margin: 5px 0;
        font-size: 14px;
        font-weight: normal;
    }
    
    .letterhead .subtitle {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    
    .document-title {
        text-align: center;
        margin: 20px 0;
        padding: 10px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        font-weight: bold;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 5px;
    }
    
    .guru-info {
        margin-bottom: 20px;
    }
    
    .guru-info table {
        width: 100%;
        font-size: 11px;
        border-collapse: collapse;
    }
    
    .guru-info table td {
        padding: 4px 8px;
        border: none;
    }
    
    .guru-info .label {
        font-weight: bold;
        width: 150px;
        color: #495057;
    }
    
    .statistics {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 10px;
    }
    
    .stat-item {
        flex: 1;
        text-align: center;
        padding: 12px;
        border: 1px solid #dee2e6;
        background: #e9ecef;
        border-radius: 4px;
    }
    
    .stat-number {
        font-size: 18px;
        font-weight: bold;
        color: #495057;
        display: block;
    }
    
    .stat-label {
        font-size: 9px;
        color: #6c757d;
        text-transform: uppercase;
        margin-top: 2px;
    }
    
    .day-section {
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .day-header {
        background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
        color: white;
        padding: 8px 12px;
        font-weight: bold;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    
    .schedule-table th,
    .schedule-table td {
        border: 1px solid #dee2e6;
        padding: 6px 8px;
        text-align: left;
        vertical-align: top;
    }
    
    .schedule-table th {
        background: #f8f9fa;
        font-weight: bold;
        font-size: 9px;
        text-transform: uppercase;
        color: #495057;
    }
    
    .schedule-table tr:nth-child(even) {
        background: #fdfdfe;
    }
    
    .subject-name {
        font-weight: bold;
        color: #495057;
    }
    
    .subject-code {
        font-size: 9px;
        color: #6c757d;
        font-style: italic;
    }
    
    .class-name {
        font-weight: 600;
        color: #28a745;
    }
    
    .time-slot {
        font-weight: 600;
        color: #dc3545;
    }
    
    .no-schedule {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 25px;
        background: #f8f9fa;
    }
    
    .weekly-summary {
        margin-top: 20px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
    }
    
    .weekly-summary h3 {
        margin: 0 0 10px 0;
        font-size: 12px;
        color: #495057;
        text-transform: uppercase;
    }
    
    .summary-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 10px;
    }
    
    .summary-item {
        flex: 1;
        text-align: center;
        padding: 5px;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 3px;
        min-width: 60px;
    }
    
    .signature-section {
        margin-top: 30px;
        text-align: right;
    }
    
    .signature-box {
        display: inline-block;
        text-align: center;
        margin-left: 50mm;
    }
    
    .signature-city {
        font-size: 10px;
        margin-bottom: 30px;
    }
    
    .signature-name {
        font-size: 10px;
        font-weight: bold;
        border-top: 1px solid #000;
        padding-top: 5px;
        margin-top: 20px;
    }
    
    .signature-title {
        font-size: 9px;
        color: #666;
    }
    
    .footer {
        margin-top: 25px;
        text-align: center;
        font-size: 9px;
        color: #6c757d;
        border-top: 2px solid #495057;
        padding-top: 10px;
    }
    
    .footer p {
        margin: 2px 0;
    }
    
    @media print {
        .preview-actions {
            display: none;
        }
        
        body {
            margin: 0;
        }
        
        .pdf-preview {
            margin: 0;
            box-shadow: none;
            border-radius: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Preview Actions -->
    <div class="preview-actions">
        <h6><i class="fas fa-tools mr-1"></i> Aksi</h6>
        <div class="btn-group-vertical w-100">
            <button onclick="window.print()" class="btn btn-primary btn-sm mb-2">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-success btn-sm mb-2">
                <i class="fas fa-download mr-1"></i> Download PDF
            </a>
            <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- PDF Preview Content -->
    <div class="pdf-preview">
        <div class="letterhead">
            <h1>{{ config('app.name', 'SMA NEGERI') }}</h1>
            <h2>SISTEM INFORMASI JADWAL MENGAJAR</h2>
            <div class="subtitle">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }} Semester {{ date('n') <= 6 ? 'Genap' : 'Ganjil' }}</div>
        </div>
        
        <div class="document-title">Jadwal Mengajar Guru</div>
        
        <div class="guru-info">
            <table>
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td>: {{ $guru->nama_lengkap }}</td>
                    <td class="label" style="text-align: right;">NIP</td>
                    <td style="text-align: right;">: {{ $guru->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td>: {{ $guru->email ?? '-' }}</td>
                    <td class="label" style="text-align: right;">No. Telepon</td>
                    <td style="text-align: right;">: {{ $guru->no_telepon ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td>: {{ $guru->alamat ?? '-' }}</td>
                    <td class="label" style="text-align: right;">Tanggal Cetak</td>
                    <td style="text-align: right;">: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="statistics">
            <div class="stat-item">
                <span class="stat-number">{{ $statistics['total_classes'] }}</span>
                <span class="stat-label">Total Kelas</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $statistics['total_subjects'] }}</span>
                <span class="stat-label">Mata Pelajaran</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $statistics['total_schedules'] }}</span>
                <span class="stat-label">Total Jadwal</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $statistics['weekly_hours'] }}</span>
                <span class="stat-label">Jam/Minggu</span>
            </div>
        </div>
        
        @foreach($hariMapping as $dayNumber => $dayName)
            <div class="day-section">
                <div class="day-header">
                    {{ $dayName }}
                    @if(isset($jadwalPerHari[$dayNumber]))
                        ({{ $jadwalPerHari[$dayNumber]->count() }} Jadwal)
                    @endif
                </div>
                
                @if(isset($jadwalPerHari[$dayNumber]) && $jadwalPerHari[$dayNumber]->count() > 0)
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th style="width: 12%">Jam Ke</th>
                                <th style="width: 18%">Waktu</th>
                                <th style="width: 30%">Mata Pelajaran</th>
                                <th style="width: 20%">Kelas</th>
                                <th style="width: 20%">Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalPerHari[$dayNumber]->sortBy('jam_mulai') as $jadwal)
                            <tr>
                                <td style="text-align: center;">{{ $jadwal->jam_ke ?? '-' }}</td>
                                <td class="time-slot">
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - 
                                    {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </td>
                                <td>
                                    <div class="subject-name">{{ $jadwal->pelajaran->nama_pelajaran }}</div>
                                    @if($jadwal->pelajaran->kode_pelajaran)
                                        <div class="subject-code">{{ $jadwal->pelajaran->kode_pelajaran }}</div>
                                    @endif
                                </td>
                                <td class="class-name">{{ $jadwal->kelas->nama_kelas }}</td>
                                <td>{{ $jadwal->ruangan ?? 'Belum ditentukan' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-schedule">
                        <strong>Tidak ada jadwal mengajar pada hari {{ $dayName }}</strong>
                    </div>
                @endif
            </div>
        @endforeach
        
        <div class="weekly-summary">
            <h3>Ringkasan Mingguan</h3>
            <div class="summary-grid">
                <div class="summary-item"><strong>Hari</strong></div>
                @foreach($hariMapping as $dayNumber => $dayName)
                    <div class="summary-item"><strong>{{ substr($dayName, 0, 3) }}</strong></div>
                @endforeach
            </div>
            <div class="summary-grid">
                <div class="summary-item"><strong>Jadwal</strong></div>
                @foreach($hariMapping as $dayNumber => $dayName)
                    <div class="summary-item">
                        {{ isset($jadwalPerHari[$dayNumber]) ? $jadwalPerHari[$dayNumber]->count() : 0 }}
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-city">{{ ucfirst(strtolower(config('app.city', 'Kota'))) }}, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</div>
                <div style="height: 40px;"></div>
                <div class="signature-name">{{ $guru->nama_lengkap }}</div>
                <div class="signature-title">NIP. {{ $guru->nip ?? '-' }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>{{ config('app.name', 'AbsensiPro') }}</strong> - Sistem Informasi Akademik</p>
            <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i:s') }}</p>
            <p>Dokumen ini dibuat secara otomatis oleh sistem</p>
        </div>
    </div>
</div>
@endsection
