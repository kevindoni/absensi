<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Excel XML declaration needs to be plain text, not processed by Laravel -->
    <!--
    <?xml version="1.0"?>
    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
     xmlns:o="urn:schemas-microsoft-com:office:office"
     xmlns:x="urn:schemas-microsoft-com:office:excel"
     xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
    <Worksheet ss:Name="Laporan Kehadiran">
     <Table>
     </Table>
    </Worksheet>
    </Workbook>
    -->
    <style>
        td { mso-number-format:\@; }
        .title { font-size:16pt; font-weight:bold; text-align:center; }
        .subtitle { font-size:12pt; text-align:center; }
        th { background-color:#f2f2f2; font-weight:bold; }
        .hadir { color: green; }
        .izin { color: orange; }
        .sakit { color: blue; }
        .alpa { color: red; }
    </style>
</head>
<body>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <td colspan="7" class="title">LAPORAN KEHADIRAN SISWA</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle">Periode: {{ date('d/m/Y', strtotime($tanggal_mulai)) }} - {{ date('d/m/Y', strtotime($tanggal_akhir)) }}</td>
        </tr>
        @if($kelas)
        <tr>
            <td colspan="7" class="subtitle">Kelas: {{ $kelas->nama_kelas }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="7">&nbsp;</td>
        </tr>
        <tr>
            <th>No</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Keterangan</th>
        </tr>
        @foreach($data as $key => $item)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $item->siswa->nisn }}</td>
            <td>{{ $item->siswa->nama_lengkap }}</td>
            <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>            <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
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
        @endforeach
    </table>
</body>
</html>
