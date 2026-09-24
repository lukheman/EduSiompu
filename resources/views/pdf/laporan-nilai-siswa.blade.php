<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai {{ $siswa->nama_siswa }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 11px; color: #111; }
        .kop { border-bottom: 3px double #111; padding-bottom: 10px; margin-bottom: 16px; }
        .kop h2 { margin: 0; font-size: 18px; }
        .kop p { margin: 2px 0; font-size: 11px; }
        .kop-logo { width: 90px; text-align: center; vertical-align: middle; }
        .kop-logo img { width: 75px; height: 75px; }
        .kop-teks { text-align: center; vertical-align: middle; }
        .judul { text-align: center; margin: 12px 0; }
        .judul h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        table { border-collapse: collapse; width: 100%; }
        table.biodata td { padding: 3px 6px; vertical-align: top; }
        table.nilai th, table.nilai td { border: 1px solid #333; padding: 5px 6px; text-align: center; }
        table.nilai th { background: #fdba74; color: #000000; }
        table.nilai td.nama, table.nilai td.mapel { text-align: left; }
        .ttd { margin-top: 24px; width: 100%; }
        .ttd td { width: 50%; text-align: center; vertical-align: top; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td class="kop-logo">@if($logoKiri)<img src="{{ $logoKiri }}" alt="Logo">@endif</td>
            <td class="kop-teks">
                <h2>PEMERINTAH PROVINSI SULAWESI TENGGARA</h2>
                <h2>DINAS PENDIDIKAN DAN KEBUDAYAAN</h2>
                <h2>SMA NEGERI 1 SIOMPU</h2>
                <p> <b>Alamat: Jln. Poros Siompu Desa Batuwu Kecamatan Siompu</b> </p>
            </td>
            <td class="kop-logo">@if($logoKanan)<img src="{{ $logoKanan }}" alt="Logo">@endif</td>
        </tr>
    </table>

    <div class="judul">
        <h3>LAPORAN NILAI PESERTA DIDIK</h3>
        <p>SEMESTER {{ strtoupper($tahunAjaran->semester) }} TAHUN PELAJARAN {{ $tahunAjaran->nama_tahun }}</p>
    </div>

    <table class="biodata">
        <tr>
            <td width="150">Nama Peserta Didik</td><td width="10">:</td><td><strong>{{ $siswa->nama_siswa }}</strong></td>
            <td width="120">Kelas</td><td width="10">:</td><td>{{ $siswa->kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td><td>:</td><td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
            <td>Semester</td><td>:</td><td>{{ ucfirst($tahunAjaran->semester) }}</td>
        </tr>
    </table>

    <br>
    <table class="nilai">
        <thead>
            <tr>
                <th width="25">No</th>
                <th>Mata Pelajaran</th>
                <th>Afektif</th>
                <th>Psikomotor</th>
                <th>Tugas</th>
                <th>UH</th>
                <th>NTS</th>
                <th>NUS</th>
                <th>NR</th>
                <th>H</th>
                <th>S</th>
                <th>I</th>
                <th>A</th>
            </tr>
        </thead>
        <tbody>
            @forelse($baris as $index => $row)
                @php $nilai = $row['nilai']; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="mapel">{{ $row['mapel']->nama_mapel }}</td>
                    <td>{{ $nilai?->rata_afektif ?? '-' }}</td>
                    <td>{{ $nilai?->rata_psikomotor ?? '-' }}</td>
                    <td>{{ $nilai?->rata_tugas ?? '-' }}</td>
                    <td>{{ $nilai?->rata_ulangan_harian ?? '-' }}</td>
                    <td>{{ $nilai?->rata_tugas ?? '-' }}</td>
                    <td>{{ $nilai?->nilai_ulangan_semester ?? '-' }}</td>
                    <td><strong>{{ $nilai?->nilai_raport ?? '-' }}</strong></td>
                    <td>{{ $row['rekap']['hadir'] }}</td>
                    <td>{{ $row['rekap']['sakit'] }}</td>
                    <td>{{ $row['rekap']['izin'] }}</td>
                    <td>{{ $row['rekap']['alpa'] }}</td>
                </tr>
            @empty
                <tr><td colspan="13">Belum ada mata pelajaran.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p style="font-size: 9px;">Keterangan kehadiran: H = Hadir, S = Sakit, I = Izin, A = Alpa</p>

    @if($raport?->catatan)
        <p><strong>Catatan Wali Kelas:</strong> <em>&ldquo;{{ $raport->catatan }}&rdquo;</em></p>
    @endif

    <table class="ttd">
        <tr>
            <td></td>
            <td>Siompu, {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Wali Kelas<br><br><br><br><br><strong>{{ $wali?->nama_guru ?? '-' }}</strong></td>
        </tr>
    </table>
</body>
</html>
