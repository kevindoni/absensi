@extends('layouts.admin')

@section('title', 'Edit Jadwal Mengajar')

@section('content')
<div class="container-fluid">
    <!-- Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Jadwal Mengajar</h1>
        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Jadwal</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.jadwal.update', $jadwal->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="guru_id">Guru <span class="text-danger">*</span></label>
                            <select class="form-control" id="guru_id" name="guru_id" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($guru as $g)
                                <option value="{{ $g->id }}" {{ old('guru_id', $jadwal->guru_id) == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama_lengkap }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                            <select class="form-control" id="kelas_id" name="kelas_id" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id', $jadwal->kelas_id) == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pelajaran_id">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-control" id="pelajaran_id" name="pelajaran_id" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($pelajaran as $p)
                                <option value="{{ $p->id }}" {{ old('pelajaran_id', $jadwal->pelajaran_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_pelajaran }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hari">Hari <span class="text-danger">*</span></label>
                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                @foreach($hari as $key => $h)
                                <label class="btn btn-outline-primary flex-fill {{ old('hari', $jadwal->hari) == $key ? 'active' : '' }}">
                                    <input type="radio" name="hari" id="hari_{{ $key }}" value="{{ $key }}" 
                                        {{ old('hari', $jadwal->hari) == $key ? 'checked' : '' }} required> {{ $h }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jam_ke">Jam Ke <span class="text-danger">*</span></label>
                            <div class="jam-ke-container">
                                @php
                                    $jamKeValues = explode(',', $jadwal->jam_ke ?? '');
                                    $jamKeAwal = !empty($jamKeValues) ? min($jamKeValues) : '';
                                @endphp
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    </div>
                                    <select class="form-control" id="jam_ke_awal" name="jam_ke_awal" required>
                                        <option value="">-- Pilih Jam Ke --</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('jam_ke_awal', $jamKeAwal) == $i ? 'selected' : '' }}>Jam ke-{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div id="jam_ke_range" class="mt-2">
                                    @php
                                        $jamKeValues = explode(',', $jadwal->jam_ke ?? '');
                                    @endphp
                                    @foreach($jamKeValues as $period)
                                        @if(!empty($period))
                                            <span class="period-badge">Jam ke-{{ $period }}</span>
                                        @endif
                                    @endforeach
                                </div>
                                <input type="hidden" name="jam_ke" id="jam_ke" value="{{ old('jam_ke', $jadwal->jam_ke) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
                            <select class="form-control" id="jam_mulai" name="jam_mulai" required>
                                <option value="">-- Pilih Jam Mulai --</option>
                                @php
                                    $currentTime = old('jam_mulai', date('H:i', strtotime($jadwal->jam_mulai)));
                                @endphp
                                @for ($hour = 7; $hour <= 16; $hour++)
                                    @foreach ([0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55] as $minute)
                                        @php
                                            $formatted = sprintf('%02d:%02d', $hour, $minute);
                                        @endphp
                                        <option value="{{ $formatted }}" {{ $currentTime == $formatted ? 'selected' : '' }}>
                                            {{ $formatted }}
                                        </option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="durasi">Durasi <span class="text-danger">*</span></label>
                            <select class="form-control" id="durasi" name="durasi" required>
                                @php
                                    // Calculate duration based on periods or time difference
                                    $jamKeValues = explode(',', $jadwal->jam_ke ?? '');
                                    $periodDuration = count($jamKeValues);
                                    
                                    // Also calculate from time difference as fallback
                                    $startTime = \Carbon\Carbon::parse($jadwal->jam_mulai);
                                    $endTime = \Carbon\Carbon::parse($jadwal->jam_selesai);
                                    $diffMinutes = $endTime->diffInMinutes($startTime);
                                    $minutesPerJP = 45; // Default
                                    
                                    // Try to guess the minutes per JP
                                    if ($diffMinutes % 30 == 0 && $diffMinutes <= 180) {
                                        $minutesPerJP = 30;
                                    } else if ($diffMinutes % 35 == 0 && $diffMinutes <= 210) {
                                        $minutesPerJP = 35;
                                    } else if ($diffMinutes % 40 == 0 && $diffMinutes <= 240) {
                                        $minutesPerJP = 40;
                                    } else if ($diffMinutes % 60 == 0 && $diffMinutes <= 360) {
                                        $minutesPerJP = 60;
                                    } else if ($diffMinutes % 50 == 0 && $diffMinutes <= 300) {
                                        $minutesPerJP = 50;
                                    }
                                    
                                    $timeDuration = ceil($diffMinutes / $minutesPerJP);
                                    $duration = $periodDuration > 0 ? $periodDuration : $timeDuration;
                                @endphp
                                @for ($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('durasi', $duration) == $i ? 'selected' : '' }}>
                                        {{ $i }} Jam Pelajaran
                                    </option>
                                @endfor
                            </select>
                            <small class="text-muted">Jumlah jam pelajaran berturut-turut</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="menit_per_jp">Menit/JP <span class="text-danger">*</span></label>
                            <select class="form-control" id="menit_per_jp" name="menit_per_jp" required>
                                @foreach([30, 35, 40, 45, 50, 60] as $menit)
                                    <option value="{{ $menit }}" {{ old('menit_per_jp', $minutesPerJP) == $menit ? 'selected' : '' }}>
                                        {{ $menit }} Menit
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jam_selesai">Jam Selesai</label>
                            <input type="text" class="form-control" id="jam_selesai" name="jam_selesai" 
                                   value="{{ old('jam_selesai', date('H:i', strtotime($jadwal->jam_selesai))) }}" readonly>
                            <small class="text-muted">Dihitung otomatis</small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary px-5">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .period-badge {
        display: inline-block;
        background-color: #4e73df;
        color: white;
        border-radius: 15px;
        padding: 4px 10px;
        margin-right: 5px;
        margin-bottom: 5px;
        font-size: 12px;
    }
    
    #jam_ke_range {
        min-height: 30px;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jamMulaiSelect = document.getElementById('jam_mulai');
        const durasiSelect = document.getElementById('durasi');
        const menitPerJpSelect = document.getElementById('menit_per_jp');
        const jamSelesaiInput = document.getElementById('jam_selesai');
        const jamKeAwalSelect = document.getElementById('jam_ke_awal');
        const jamKeRange = document.getElementById('jam_ke_range');
        const hiddenJamKe = document.getElementById('jam_ke');

        function calculateEndTime() {
            const selectedTime = jamMulaiSelect.value;
            const duration = parseInt(durasiSelect.value);
            const menitPerJP = parseInt(menitPerJpSelect.value);

            if (!selectedTime || isNaN(duration) || isNaN(menitPerJP)) {
                jamSelesaiInput.value = '';
                return;
            }

            const [startHour, startMinute] = selectedTime.split(':').map(Number);
            const totalStartMinutes = (startHour * 60) + startMinute;
            const totalDurationMinutes = duration * menitPerJP;
            const totalEndMinutes = totalStartMinutes + totalDurationMinutes;

            const endHour = Math.floor(totalEndMinutes / 60) % 24;
            const endMinute = totalEndMinutes % 60;

            jamSelesaiInput.value = `${endHour.toString().padStart(2, '0')}:${endMinute.toString().padStart(2, '0')}`;
        }

        function updatePeriodDisplay() {
            const startPeriod = parseInt(jamKeAwalSelect.value);
            const duration = parseInt(durasiSelect.value);
            jamKeRange.innerHTML = '';

            if (isNaN(startPeriod) || startPeriod < 1) {
                hiddenJamKe.value = '';
                return;
            }

            const periods = [];
            for (let i = 0; i < duration; i++) {
                const period = startPeriod + i;
                periods.push(period);

                const badge = document.createElement('span');
                badge.className = 'period-badge';
                badge.textContent = `Jam ke-${period}`;
                jamKeRange.appendChild(badge);
            }

            hiddenJamKe.value = periods.join(',');
        }

        // Event listeners
        jamMulaiSelect.addEventListener('change', calculateEndTime);
        durasiSelect.addEventListener('change', function () {
            updatePeriodDisplay();
            calculateEndTime();
        });
        menitPerJpSelect.addEventListener('change', calculateEndTime);
        jamKeAwalSelect.addEventListener('change', updatePeriodDisplay);

        // Initial calls
        calculateEndTime();
        updatePeriodDisplay();
    });
</script>
@endsection
