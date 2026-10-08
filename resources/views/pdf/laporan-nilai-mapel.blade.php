<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai {{ $mapel->nama_mapel }} - {{ $kelas->nama_kelas }}</title>
    <style>
        @page { margin: 8mm 6mm 8mm 6mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 8px; color: #111; }
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
        table.nilai { table-layout: fixed; }
        table.nilai th, table.nilai td { border: 1px solid #555; padding: 2px 1px; font-size: 7px; text-align: center; vertical-align: middle; }
        table.nilai th { background: #d4af37; color: #000000; font-weight: bold; }
        table.nilai th.angka { background: #f8e7a8; }
        table.nilai th.kiri, table.nilai th.akhir { background: #d4af37; }
        table.nilai td.pertemuan, table.nilai th.pertemuan { width: 18px; }
        table.nilai th.nama-head { width: 500px; }
        table.nilai td.nama { width: 210px; text-align: left; white-space: normal; overflow-wrap: anywhere; word-break: break-word; }
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
        </tr>
        <tr>
            <td width="120">Kelas</td><td width="10">:</td><td>{{ $kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Semester</td><td>:</td><td>{{ ucfirst($tahunAjaran->semester) }}</td>
        </tr>
    </table>

    <br>
    <table class="nilai">
        <colgroup>
            <col style="width: 24px;">
            <col style="width: 210px;">
            <col style="width: 26px;">
            @for($i = 1; $i <= 20; $i++)
                <col style="width: 13px;">
            @endfor
            @for($i = 1; $i <= 18; $i++)
                <col style="width: 17px;">
            @endfor
            @for($i = 1; $i <= 4; $i++)
                <col style="width: 28px;">
            @endfor
        </colgroup>
        <thead>
            <tr>
                    <th rowspan="3" class="kiri">NO</th>
                    <th rowspan="3" class="kiri nama-head">NAMA</th>
                    <th rowspan="3" class="kiri">JK (L/P)</th>
                    <th colspan="20">PERTEMUAN KE</th>
                    <th colspan="18">PENILAIAN</th>
                    <th rowspan="3" class="akhir">PTS</th>
                    <th rowspan="3" class="akhir">PAS</th>
                    <th rowspan="3" class="akhir">NR</th>
                    <th rowspan="3" class="akhir">KET</th>
                </tr>
                <tr>
                    @for($i = 1; $i <= 20; $i++)
                        <th rowspan="2" class="angka pertemuan">{{ $i }}</th>
                    @endfor
                <th colspan="4">AFEKTIF</th>
                <th colspan="5">PSIKOMOTOR</th>
                <th colspan="5">NILAI TUGAS</th>
                <th colspan="4">UL. HARIAN</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 3; $i++)
                    <th class="angka">{{ $i }}</th>
                @endfor
                <th>RT</th>
                @for($i = 1; $i <= 4; $i++)
                    <th class="angka">{{ $i }}</th>
                @endfor
                <th>RT</th>
                @for($i = 1; $i <= 4; $i++)
                    <th class="angka">{{ $i }}</th>
                @endfor
                <th>RT</th>
                @for($i = 1; $i <= 3; $i++)
                    <th class="angka">{{ $i }}</th>
                @endfor
                <th>RT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                @php
                    $nilai = $siswa->raport->first()?->nilaiRaport->first();
                @endphp
                <tr @if($index % 2 === 1) style="background: #e5e7eb;" @endif>
                    <td>{{ $index + 1 }}</td>
                    <td class="nama">{{ $siswa->nama_siswa }}</td>
                    <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                    @for($i = 0; $i < 20; $i++)
                        @php
                            $tanggal = $pertemuans[$i] ?? null;
                            $st = $tanggal ? ($kehadiran[$siswa->id_siswa][$tanggal->format('Y-m-d')] ?? null) : null;
                        @endphp
                        <td class="pertemuan"><strong>{{ $st === 'hadir' ? '✓' : ($st === 'sakit' ? 'S' : ($st === 'izin' ? 'I' : ($st === 'alpa' ? 'A' : '-'))) }}</strong></td>
                    @endfor
                    @for($i = 1; $i <= 3; $i++)
                        <td>{{ $nilai?->{'nilai_afektif_'.$i} ?? '-' }}</td>
                    @endfor
                    <td><strong>{{ $nilai?->rata_afektif ?? '-' }}</strong></td>
                    @for($i = 1; $i <= 4; $i++)
                        <td>{{ $nilai?->{'nilai_psikomotor_'.$i} ?? '-' }}</td>
                    @endfor
                    <td><strong>{{ $nilai?->rata_psikomotor ?? '-' }}</strong></td>
                    @for($i = 1; $i <= 4; $i++)
                        <td>{{ $nilai?->{'nilai_tugas_'.$i} ?? '-' }}</td>
                    @endfor
                    <td><strong>{{ $nilai?->rata_tugas ?? '-' }}</strong></td>
                    @for($i = 1; $i <= 3; $i++)
                        <td>{{ $nilai?->{'nilai_ulangan_harian_'.$i} ?? '-' }}</td>
                    @endfor
                    <td><strong>{{ $nilai?->rata_ulangan_harian ?? '-' }}</strong></td>
                    <td><strong>{{ $nilai?->rata_tugas ?? '-' }}</strong></td>
                    <td>{{ $nilai?->nilai_ulangan_semester ?? '-' }}</td>
                    <td><strong>{{ $nilai?->nilai_raport ?? '-' }}</strong></td>
                    <td>{{ $nilai?->predikat_raport ?? '-' }}</td>
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
            <td>Siompu, {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Wali Kelas<br><br><br><br><br><strong>{{ $wali?->nama_guru ?? '-' }}</strong></td>
        </tr>
    </table>
</body>
</html>
