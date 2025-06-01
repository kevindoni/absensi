@extends('layouts.guru')

@section('title', 'Jadwal Pribadi')

@section('styles')
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
<style>
    .calendar-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .fc-event {
        border: none !important;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 600;
        color: #5a5c69;
    }
    
    .fc-button-primary {
        background-color: #4e73df !important;
        border-color: #4e73df !important;
    }
    
    .fc-button-primary:hover {
        background-color: #2e59d9 !important;
        border-color: #2e59d9 !important;
    }
    
    .fc-today-button, .fc-prev-button, .fc-next-button {
        background-color: #858796 !important;
        border-color: #858796 !important;
    }
    
    .fc-today-button:hover, .fc-prev-button:hover, .fc-next-button:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }
    
    .schedule-summary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .summary-item {
        text-align: center;
        padding: 10px;
    }
    
    .summary-number {
        font-size: 2rem;
        font-weight: bold;
        display: block;
    }
    
    .summary-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 15px;
        margin-bottom: 5px;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 3px;
        margin-right: 6px;
    }
    
    .quick-actions .btn {
        margin-right: 10px;
        margin-bottom: 10px;
    }
    
    .fc-daygrid-event-harness {
        margin-bottom: 1px;
    }
    
    @media (max-width: 768px) {
        .calendar-container {
            padding: 10px;
        }
        
        .fc-toolbar {
            flex-direction: column;
        }
        
        .fc-toolbar-chunk {
            margin: 5px 0;
        }
          .summary-item {
            margin-bottom: 15px;
        }
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-lg .card {
        transition: transform 0.2s;
    }
    
    .modal-lg .card:hover {
        transform: translateY(-2px);
    }
    
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt mr-2"></i>Jadwal Pribadi
        </h1>        <div class="quick-actions">
            <a href="{{ route('guru.jadwal.weekly') }}" class="btn btn-info btn-sm">
                <i class="fas fa-calendar-week mr-1"></i> Tampilan Mingguan
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </button>                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('guru.jadwal.preview-pdf') }}" target="_blank">
                        <i class="fas fa-eye mr-2"></i> Preview Detail
                    </a>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.preview-compact-pdf') }}" target="_blank">
                        <i class="fas fa-eye mr-2"></i> Preview Kompak
                    </a>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.preview-weekly-pdf') }}" target="_blank">
                        <i class="fas fa-eye mr-2"></i> Preview Mingguan
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.export-pdf') }}">
                        <i class="fas fa-file-alt mr-2"></i> Format Detail
                    </a>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.export-compact-pdf') }}">
                        <i class="fas fa-th mr-2"></i> Format Kompak
                    </a>
                    <a class="dropdown-item" href="{{ route('guru.jadwal.export-weekly-pdf') }}">
                        <i class="fas fa-calendar-week mr-2"></i> Format Mingguan
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#exportOptionsModal">
                        <i class="fas fa-cog mr-2"></i> Opsi Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Summary -->
    <div class="schedule-summary">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="summary-item">
                    <span class="summary-number">{{ $statistics['total_classes'] }}</span>
                    <span class="summary-label">Total Kelas</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-item">
                    <span class="summary-number">{{ $statistics['total_subjects'] }}</span>
                    <span class="summary-label">Mata Pelajaran</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-item">
                    <span class="summary-number">{{ $statistics['weekly_hours'] }}</span>
                    <span class="summary-label">Jam/Minggu</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-item">
                    <span class="summary-number">{{ $statistics['active_days'] }}</span>
                    <span class="summary-label">Hari Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-primary mb-3">
                <i class="fas fa-palette mr-2"></i>Legenda Warna
            </h6>
            <div class="legend-container">
                @foreach($subjectColors as $subject => $color)
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: {{ $color }}"></div>
                        <span class="text-sm">{{ $subject }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    @if(!empty($todaySchedule))
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-day mr-2"></i>Jadwal Hari Ini - {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Ruangan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todaySchedule as $schedule)
                        <tr>
                            <td class="font-weight-bold">
                                {{ date('H:i', strtotime($schedule->jam_mulai)) }} - 
                                {{ date('H:i', strtotime($schedule->jam_selesai)) }}
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $subjectColors[$schedule->pelajaran->nama_pelajaran] ?? '#6c757d' }}; color: white;">
                                    {{ $schedule->pelajaran->nama_pelajaran }}
                                </span>
                            </td>
                            <td>{{ $schedule->kelas->nama_kelas }}</td>
                            <td>{{ $schedule->ruangan ?? '-' }}</td>
                            <td>
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $startTime = \Carbon\Carbon::parse($now->format('Y-m-d') . ' ' . $schedule->jam_mulai);
                                    $endTime = \Carbon\Carbon::parse($now->format('Y-m-d') . ' ' . $schedule->jam_selesai);
                                @endphp
                                
                                @if($now->lt($startTime))
                                    <span class="badge badge-secondary">Belum Dimulai</span>
                                @elseif($now->between($startTime, $endTime))
                                    <span class="badge badge-success">Sedang Berlangsung</span>
                                @else
                                    <span class="badge badge-light">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/id.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        events: {
            url: '{{ route("guru.jadwal.data") }}',
            method: 'GET',
            failure: function() {
                alert('Terjadi kesalahan saat memuat data jadwal!');
            }
        },
        eventClick: function(info) {
            // Show detailed information about the schedule
            var event = info.event;
            var extendedProps = event.extendedProps;
            
            var modalContent = `
                <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Jadwal</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Mata Pelajaran:</strong></td>
                                        <td>${event.title}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kelas:</strong></td>
                                        <td>${extendedProps.kelas}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Waktu:</strong></td>
                                        <td>${extendedProps.jam_mulai} - ${extendedProps.jam_selesai}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ruangan:</strong></td>
                                        <td>${extendedProps.ruangan || '-'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kode Pelajaran:</strong></td>
                                        <td>${extendedProps.kode_pelajaran || '-'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <a href="{{ route('guru.absensi.index') }}" class="btn btn-primary">
                                    <i class="fas fa-clipboard-list mr-1"></i>Lihat Absensi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            $('#scheduleModal').remove();
            
            // Add modal to body and show
            $('body').append(modalContent);
            $('#scheduleModal').modal('show');
        },
        eventDidMount: function(info) {
            // Add tooltip
            $(info.el).tooltip({
                title: info.event.extendedProps.kelas + ' - ' + info.event.extendedProps.jam_mulai + ' sampai ' + info.event.extendedProps.jam_selesai,
                placement: 'top',
                trigger: 'hover'
            });
        },
        dayMaxEvents: 3, // Limit number of events shown per day
        moreLinkClick: function(info) {
            calendar.changeView('timeGridDay', info.date);
        }
    });
    
    calendar.render();
    
    // Refresh calendar data every 5 minutes
    setInterval(function() {
        calendar.refetchEvents();
    }, 300000);
    
    // Export feedback handling
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading states to export buttons
        const exportButtons = document.querySelectorAll('a[href*="export"]');
        exportButtons.forEach(button => {
            if (!button.hasAttribute('target')) { // Don't add loading to preview links
                button.addEventListener('click', function(e) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...';
                    this.classList.add('disabled');
                    
                    // Reset after 3 seconds
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('disabled');
                        
                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success('PDF berhasil diunduh!', 'Export Berhasil');
                        } else {
                            // Fallback alert
                            setTimeout(() => {
                                alert('PDF berhasil diunduh!');
                            }, 500);
                        }
                    }, 3000);
                });
            }
        });
        
        // Handle modal export buttons
        const modalExportButtons = document.querySelectorAll('#exportOptionsModal a[href*="export"]');
        modalExportButtons.forEach(button => {
            button.addEventListener('click', function() {
                $('#exportOptionsModal').modal('hide');
            });
        });
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + P for PDF export
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                $('#exportOptionsModal').modal('show');
            }
            
            // Ctrl + Shift + P for quick export
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'P') {
                e.preventDefault();
                window.location.href = "{{ route('guru.jadwal.export-pdf') }}";
            }
        });
    });
});
</script>

<!-- Export Options Modal -->
<div class="modal fade" id="exportOptionsModal" tabindex="-1" role="dialog" aria-labelledby="exportOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="exportOptionsModalLabel">
                    <i class="fas fa-file-export mr-2"></i>Opsi Export Jadwal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Format Detail -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-left-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary mr-3">
                                        <i class="fas fa-file-alt text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold">Format Detail</h6>
                                        <small class="text-muted">Portrait, Lengkap dengan Info</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">
                                    Format lengkap dengan informasi guru, statistik, dan jadwal harian yang detail. 
                                    Cocok untuk laporan resmi dan dokumentasi.
                                </p>
                                <div class="row small text-muted mb-3">
                                    <div class="col-6">
                                        <i class="fas fa-file-alt mr-1"></i> A4 Portrait
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-clock mr-1"></i> ~2-3 halaman
                                    </div>
                                </div>
                                <div class="btn-group w-100">
                                    <a href="{{ route('guru.jadwal.preview-pdf') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye mr-1"></i> Preview
                                    </a>
                                    <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Format Kompak -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-left-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-success mr-3">
                                        <i class="fas fa-th text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold">Format Kompak</h6>
                                        <small class="text-muted">Landscape, Grid Mingguan</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">
                                    Format grid mingguan yang ringkas dan mudah dibaca. 
                                    Cocok untuk referensi cepat dan dipasang di dinding.
                                </p>
                                <div class="row small text-muted mb-3">
                                    <div class="col-6">
                                        <i class="fas fa-file-alt mr-1"></i> A4 Landscape
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-clock mr-1"></i> 1 halaman
                                    </div>
                                </div>                                <div class="btn-group w-100">
                                    <a href="{{ route('guru.jadwal.preview-compact-pdf') }}" target="_blank" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-eye mr-1"></i> Preview
                                    </a>
                                    <a href="{{ route('guru.jadwal.export-compact-pdf') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Format Mingguan -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-left-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-warning mr-3">
                                        <i class="fas fa-calendar-week text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold">Format Mingguan</h6>
                                        <small class="text-muted">Portrait, Per Minggu</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">
                                    Format jadwal per minggu dengan tanggal spesifik dan detail harian. 
                                    Cocok untuk planning mingguan dan laporan periode tertentu.
                                </p>
                                <div class="row small text-muted mb-3">
                                    <div class="col-6">
                                        <i class="fas fa-file-alt mr-1"></i> A4 Portrait
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-clock mr-1"></i> ~2 halaman
                                    </div>
                                </div>
                                <div class="btn-group w-100">
                                    <a href="{{ route('guru.jadwal.preview-weekly-pdf') }}" target="_blank" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-eye mr-1"></i> Preview
                                    </a>
                                    <a href="{{ route('guru.jadwal.export-weekly-pdf') }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Export Statistics -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar mr-2"></i>Informasi yang Akan Diekspor
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 text-center mb-2">
                                <div class="text-primary font-weight-bold">{{ $statistics['total_classes'] }}</div>
                                <small class="text-muted">Total Kelas</small>
                            </div>
                            <div class="col-md-3 col-6 text-center mb-2">
                                <div class="text-success font-weight-bold">{{ $statistics['total_subjects'] }}</div>
                                <small class="text-muted">Mata Pelajaran</small>
                            </div>
                            <div class="col-md-3 col-6 text-center mb-2">
                                <div class="text-info font-weight-bold">{{ $statistics['weekly_hours'] }}</div>
                                <small class="text-muted">Jam/Minggu</small>
                            </div>
                            <div class="col-md-3 col-6 text-center mb-2">
                                <div class="text-warning font-weight-bold">{{ $statistics['active_days'] }}</div>
                                <small class="text-muted">Hari Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            <div class="modal-footer">
                <div class="text-left mr-auto">
                    <small class="text-muted">
                        <i class="fas fa-keyboard mr-1"></i>
                        Shortcut: <kbd>Ctrl+P</kbd> untuk membuka opsi export, <kbd>Ctrl+Shift+P</kbd> untuk export cepat
                    </small>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>                <div class="btn-group">
                    <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-primary">
                        <i class="fas fa-file-alt mr-1"></i> Export Detail
                    </a>
                    <a href="{{ route('guru.jadwal.export-compact-pdf') }}" class="btn btn-success">
                        <i class="fas fa-th mr-1"></i> Export Kompak
                    </a>
                    <a href="{{ route('guru.jadwal.export-weekly-pdf') }}" class="btn btn-warning">
                        <i class="fas fa-calendar-week mr-1"></i> Export Mingguan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
