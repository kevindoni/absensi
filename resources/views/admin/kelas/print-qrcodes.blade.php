<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $kepsek = DB::table('settings')->where('key', 'kepala_sekolah')->value('value') ?: 'NAMA KEPALA SEKOLAH';
        $nip = DB::table('settings')->where('key', 'nip_kepala_sekolah')->value('value') ?: '-';
        $logoPath = DB::table('settings')->where('key', 'logo_path')->value('value');
        $schoolName = DB::table('settings')->where('key', 'school_name')->value('value');
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card - {{ $kelas->nama_kelas }}</title>
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        /* Modern base styles */
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            font-family: 'Poppins', Arial, sans-serif;
        }

        /* Print container with precise grid */
        .print-container {
            display: grid;
            grid-template-columns: repeat(2, 88mm);
            grid-auto-rows: 62mm;
            gap: 2mm;
            width: 182mm;
            margin: 10mm auto;
            padding: 0;
        }

        /* Premium ID card styling */
        .id-card {
            width: 88mm;
            height: 62mm;
            background: white;
            border-radius: 3mm;
            overflow: hidden;
            position: relative;
            box-shadow: 0 1mm 3mm rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
            border: 1px solid #e0e0e0;
        }

        /* Elegant card header */
        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 2mm 3mm;
            height: 10mm;
            display: flex;
            align-items: center;
            position: relative;
        }        
        .school-logo {
            height: 7mm;
            margin-right: 2mm;
            /* Removed filter to show logo in original colors */
        }

        .school-info {
            flex-grow: 1;
        }

        .school-name {
            font-size: 8pt;
            font-weight: 600;
            margin: 0;
            line-height: 1.1;
            letter-spacing: 0.2px;
        }

        .card-title {
            font-size: 6pt;
            margin: 0;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        /* Main content area */
        .card-body {
            display: flex;
            flex-grow: 1;
            padding: 3mm;
        }

        /* Professional photo section */
        .photo-section {
            width: 22mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 3mm;
        }

        .photo-placeholder {
            width: 20mm;
            height: 26mm;
            background: #f5f7fa;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .photo-placeholder::after {
            content: "Pas Foto 3×4";
            font-size: 6pt;
            color: #95a5a6;
            text-align: center;
        }

        .academic-year {
            font-size: 6pt;
            color: #7f8c8d;
            text-align: center;
            font-weight: 500;
            margin-top: 1mm;
        }        
        /* Info section with signature */
        .info-section {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }        .student-info {
            margin-bottom: 0;
        }

        .student-name {
            font-size: 9pt;
            font-weight: 600;
            margin: 0 0 1mm 0;
            color: #2c3e50;
            line-height: 1.2;
        }

        .student-details {
            font-size: 7pt;
            margin: 0;
            color: #34495e;
            line-height: 1.3;
        }        .student-id {
            font-weight: 600;
            color: #2980b9;
        }          
        /* Redesigned bottom section */
        .bottom-section {
            display: flex;
            flex-direction: column;
            margin-top: -10mm; /* Negative margin to move up */
            align-items: flex-start;
        }
          .qr-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 1mm;
            margin-left: 30mm;
            margin-top: 0;
        }        .qr-container svg {
            width: 25mm !important;
            height: 25mm !important;
            background: white;
            padding: 0.5mm;
            border: 0.5px solid #ecf0f1;
        }/* Signature section */
        .signature-section {
            text-align: right;
            margin-left: auto;
            width: 100%;
            margin-top: 0.2mm;
        }

        .signature-line {
            width: 30mm;
            border-top: 0.5px solid #2c3e50;
            margin-left: auto;
            margin-bottom: 1mm;
        }

        .signature-title {
            font-size: 6pt;
            color: #34495e;
            margin: 0;
        }

        .signature-name {
            font-size: 7pt;
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
        }

        .signature-nip {
            font-size: 5pt;
            color: #7f8c8d;
            margin: 0;
        }

        /* Professional footer */        
        .card-footer {
            background: #ecf0f1;
            border-top: 1px solid #d5dbdb;
            padding: 1mm;
            text-align: center;
            font-size: 5.5pt;
            color: #7f8c8d;
            font-weight: 500;
            margin-top: auto;
        }

        /* Print controls */
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            gap: 10px;
        }

        /* Perfect print output */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            .print-container {
                width: 182mm;
                margin: 0 auto;
            }

            .id-card {
                box-shadow: none;
                border: 0.5px solid #ddd;
            }            .card-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }            .qr-container svg {
                width: 25mm !important;
                height: 25mm !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        @foreach($kelas->siswa as $s)
        <div class="id-card">
            <div class="card-header">
                <img src="{{ asset($logoPath ?: 'sbadmin2/img/logo.png') }}" alt="School Logo" class="school-logo">
                <div class="school-info text-center">
                    <p class="school-name">{{ $schoolName }}</p>
                    <p class="card-title">KARTU ABSENSI PESERTA DIDIK</p>
                </div>
                <img src="{{ asset($logoPath ?: 'sbadmin2/img/logo.png') }}" alt="School Logo" class="school-logo">
            </div>
            
            <div class="card-body">
                <div class="photo-section">
                    <div class="photo-placeholder"></div>
                    <div class="academic-year">TA {{ date('Y') }}/{{ date('Y')+1 }}</div>
                </div>
                
                <div class="info-section">
                    <div class="student-info">
                        <h5 class="student-name">{{ $s->nama_lengkap }}</h5>                        
                        <p class="student-details">                            
                            <span class="student-id">NISN: {{ $s->nisn }}</span><br>                            @if(isset($kelas->jurusan) && !empty($kelas->jurusan))
                                Jurusan: {{ $kelas->jurusan }}<br>
                            @endif
                            {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}<br>
                            {{ $s->tanggal_lahir ? date('j M Y', strtotime($s->tanggal_lahir)) : '-' }}
                        </p>
                    </div>                    <div class="bottom-section">
                        <div class="qr-container">
                            @if($s->qr_token)
                                {!! App\Http\Controllers\QrController::generateStandardQrCode($s) !!}
                            @else
                                <div style="font-size: 7pt; color: #e74c3c;">QR Code tidak tersedia</div>
                            @endif
                        </div>
                        
                        <div class="signature-section">
                            <div class="signature-line"></div>
                            <p class="signature-title">Kepala Sekolah</p>
                            <p class="signature-name">{{ $kepsek }}</p>
                            @if($nip != '-')
                                <p class="signature-nip">NIP. {{ $nip }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
              <div class="card-footer">
                Kartu ini berlaku sebagai absensi dan wajib dibawa setiap hari sekolah.
            </div>
        </div>
        @endforeach
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary btn-sm shadow">
            <i class="fas fa-print"></i> Cetak
        </button>
        <a href="{{ route('admin.kelas.qrcodes', $kelas->id) }}" class="btn btn-secondary btn-sm shadow">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</body>
</html>