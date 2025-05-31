<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran Siswa</title>
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 20px;
            }
            .container {
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
        
        /* Regular styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        h1, h3 {
            text-align: center;
            margin-bottom: 10px;
        }
        .period {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .summary {
            margin-top: 30px;
        }
        .summary table {
            width: 50%;
            margin-left: 0;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
        }
        .hadir { color: green; }
        .izin { color: orange; }
        .sakit { color: blue; }
        .alpa { color: red; }
        
        /* Print action button */
        .print-button {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            padding: 10px 20px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print print-button">
            <button class="btn" onclick="window.print()">Cetak PDF</button>
        </div>
        
        <h1>LAPORAN KEHADIRAN SISWA</h1>
        <div class="period">
            Periode: {{ date('d/m/Y', strtotime($tanggal_mulai)) }} - {{ date('d/m/Y', strtotime($tanggal_akhir)) }}<br>
            @if($kelas)
                Kelas: {{ $kelas->nama_kelas }}
            @else
                Semua Kelas
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">NISN</th>
                    <th width="20%">Nama Siswa</th>
                    <th width="15%">Kelas</th>
                    <th width="15%">Tanggal</th>
                    <th width="10%">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->siswa->nisn }}</td>
                        <td>{{ $item->siswa->nama_lengkap }}</td>
                        <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>                        <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                        <td class="{{ $item->status }}">{{ ucfirst($item->status) }}</td>
                        <td>
                            @if(strtolower($item->status) == 'terlambat' && $item->minutes_late > 0)
                                @php
                                    $minutes = abs($item->minutes_late);
                                    $hours = floor($minutes / 60);
                                    $remainingMinutes = $minutes % 60;
                                    
                                    if ($hours > 0 && $remainingMinutes > 0) {
                                        $timeText = $hours . ' jam ' . $remainingMinutes . ' menit';
                                    } elseif ($hours > 0) {
                                        $timeText = $hours . ' jam';
                                    } else {
                                        $timeText = $remainingMinutes . ' menit';
                                    }
                                @endphp
                                Terlambat {{ $timeText }}
                            @elseif(strtolower($item->status) == 'hadir')
                                Hadir tepat waktu
                            @elseif(strtolower($item->status) == 'alpha')
                                Tidak hadir
                            @else
                                {{ $item->keterangan ?? ucfirst($item->status) }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center">Tidak ada data kehadiran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary">
            <h3>Ringkasan</h3>
            <table>
                <tr>
                    <th>Total Kehadiran</th>
                    <td>{{ $summary->total ?? 0 }}</td>
                </tr>
                <tr>
                    <th>Hadir</th>
                    <td>{{ $summary->hadir ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->hadir / $summary->total) * 100, 2) : 0 }}%)</td>
                </tr>
                <tr>
                    <th>Izin</th>
                    <td>{{ $summary->izin ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->izin / $summary->total) * 100, 2) : 0 }}%)</td>
                </tr>
                <tr>
                    <th>Sakit</th>
                    <td>{{ $summary->sakit ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->sakit / $summary->total) * 100, 2) : 0 }}%)</td>
                </tr>
                <tr>
                    <th>Alpa</th>
                    <td>{{ $summary->alpa ?? 0 }} ({{ isset($summary->total) && $summary->total > 0 ? round(($summary->alpa / $summary->total) * 100, 2) : 0 }}%)</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Dicetak pada: {{ date('d/m/Y H:i:s') }}
        </div>
    </div>
    
    @if(isset($isPrintMode) && $isPrintMode)
    <script>
        // Auto-print when the page loads
        window.addEventListener('load', function() {
            // Brief delay to ensure the page is fully loaded
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
    @endif
</body>
</html>
