<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai {{ $mapel->nama_mapel }} - {{ $kelas->nama_kelas }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .kop { text-align: center; border-bottom: 3px double #111; padding-bottom: 10px; margin-bottom: 16px; }
        .kop h2 { margin: 0; font-size: 18px; }
        .kop p { margin: 2px 0; font-size: 11px; }
        .judul { text-align: center; margin: 12px 0; }
        .judul h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        table { border-collapse: collapse; width: 100%; }
        table.biodata td { padding: 3px 6px; vertical-align: top; }
        table.nilai th, table.nilai td { border: 1px solid #333; padding: 5px 6px; text-align: center; }
        table.nilai th { background: #e5e5e5; }
        table.nilai td.nama { text-align: left; }
        .ttd { margin-top: 24px; width: 100%; }
        .ttd td { width: 50%; text-align: center; vertical-align: top; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>SMAN 1 SIOMPU</h2>
        <p>EduSiompu &mdash; Sistem Informasi Akademik</p>
    </div>

    <div class="judul">
        <h3>LAPORAN NILAI MATA PELAJARAN</h3>
        <p>Tahun Ajaran {{ $tahunAjaran->nama_tahun }} Semester {{ ucfirst($tahunAjaran->semester) }}</p>
    </div>

    <table class="biodata">
        <tr>
            <td width="150">Mata Pelajaran</td><td width="10">:</td><td><strong>{{ $mapel->nama_mapel }}</strong></td>
            <td width="120">Kelas</td><td width="10">:</td><td>{{ $kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Guru Pengampu</td><td>:</td><td>{{ $pengampu->nama_guru ?? '-' }}</td>
            <td>Wali Kelas</td><td>:</td><td>{{ $wali->nama_guru }}</td>
        </tr>
    </table>

    <br>
    <table class="nilai">
        <thead>
            <tr>
                <th width="25">No</th>
                <th>Nama Siswa</th>
                <th>NISN</th>
                <th>Afektif</th>
                <th>Psikomotor</th>
                <th>Tugas</th>
                <th>UH</th>
                <th>US</th>
                <th>Nilai Akhir</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                @php $nilai = $siswa->raport->first()?->nilaiRaport->first(); @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="nama">{{ $siswa->nama_siswa }}</td>
                    <td>{{ $siswa->nisn }}</td>
                    <td>{{ $nilai?->rata_afektif ?? '-' }}</td>
                    <td>{{ $nilai?->rata_psikomotor ?? '-' }}</td>
                    <td>{{ $nilai?->rata_tugas ?? '-' }}</td>
                    <td>{{ $nilai?->rata_ulangan_harian ?? '-' }}</td>
                    <td>{{ $nilai?->nilai_ulangan_semester ?? '-' }}</td>
                    <td><strong>{{ $nilai?->nilai_raport ?? '-' }}</strong></td>
                    <td><strong>{{ $nilai?->predikat_raport ?? '-' }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td></td>
            <td>Siompu, {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Wali Kelas<br><br><br><br><br><strong>{{ $wali->nama_guru }}</strong></td>
        </tr>
    </table>
</body>
</html>
