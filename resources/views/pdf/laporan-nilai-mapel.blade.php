<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai {{ $mapel->nama_mapel }} - {{ $kelas->nama_kelas }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .kop { border-bottom: 3px double #111; padding-bottom: 10px; margin-bottom: 16px; }
        .kop h2 { margin: 0; font-size: 18px; }
        .kop p { margin: 2px 0; font-size: 11px; }
        .kop-logo { width: 90px; text-align: center; vertical-align: middle; }
        .kop-logo img { width: 75px; height: 75px; }
        .kop-teks { text-align: center; vertical-align: middle; }
        .judul { text-align: center; margin: 12px 0; }
        .judul h3 { margin: 0; font-size: 14px; }
        table { border-collapse: collapse; width: 100%; }
        table.biodata td { padding: 3px 6px; vertical-align: top; }
        table.nilai th, table.nilai td { border: 1px solid #333; padding: 5px 6px; text-align: center; }
        table.nilai th { background: #e5e5e5; }
        table.nilai td.nama { text-align: left; }
        .tgl { font-size: 7px; font-weight: normal; }
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
        <h3>DAFTAR HADIR SEMESTER {{ strtoupper($tahunAjaran->semester) }}</h3>
        <h3>TAHUN PELAJARAN {{ $tahunAjaran->nama_tahun }}</h3>
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
                <th width="25" rowspan="2">No</th>
                <th rowspan="2">Nama Siswa</th>
                @if(count($pertemuans) > 0)
                    <th colspan="{{ count($pertemuans) }}">Pertemuan Ke-</th>
                @endif
                <th rowspan="2">Afektif</th>
                <th rowspan="2">Psikomotor</th>
                <th rowspan="2">Tugas</th>
                <th rowspan="2">UH</th>
                <th rowspan="2">US</th>
                <th rowspan="2">Nilai Akhir</th>
                <th rowspan="2">Predikat</th>
            </tr>
            @if(count($pertemuans) > 0)
                <tr>
                    @foreach($pertemuans as $index => $tanggal)
                        <th>{{ $index + 1 }}<br><span class="tgl">{{ $tanggal->format('d/m') }}</span></th>
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                @php
                    $nilai = $siswa->raport->first()?->nilaiRaport->first();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="nama">{{ $siswa->nama_siswa }}</td>
                    @foreach($pertemuans as $tanggal)
                        @php $st = $kehadiran[$siswa->id_siswa][$tanggal->format('Y-m-d')] ?? null; @endphp
                        <td><strong>{{ $st === 'hadir' ? '✓' : ($st === 'sakit' ? 'S' : ($st === 'izin' ? 'I' : ($st === 'alpa' ? 'A' : '-'))) }}</strong></td>
                    @endforeach
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
    @if(count($pertemuans) > 0)
        <p style="font-size: 9px;">Keterangan: ✓ = Hadir, S = Sakit, I = Izin, A = Alpa, - = Belum diisi</p>
    @else
        <p><em>Belum ada data absensi pada mata pelajaran ini.</em></p>
    @endif

    <table class="ttd">
        <tr>
            <td></td>
            <td>Siompu, {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Wali Kelas<br><br><br><br><br><strong>{{ $wali->nama_guru }}</strong></td>
        </tr>
    </table>
</body>
</html>
