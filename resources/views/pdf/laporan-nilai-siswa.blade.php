<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor {{ $siswa->nama_siswa }}</title>
    <style>
        @page { margin: 14mm 16mm 16mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: Arial, Helvetica, sans-serif; font-size: 10px; }
        .page { position: relative; min-height: 267mm; page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        .identitas { width: 100%; border-collapse: collapse; margin: 2mm 0 4mm; font-size: 10px; }
        .identitas td { padding: 1.2mm 0; vertical-align: top; }
        .identitas .label { width: 22%; }
        .identitas .value { width: 31%; }
        .identitas .label-right { width: 18%; padding-left: 8mm; }
        .identitas .value-right { width: 29%; }
        .garis { border-bottom: 1px solid #222; margin-bottom: 8mm; }
        h1 { margin: 0 0 7mm; text-align: center; font-size: 18px; font-weight: 700; }
        table { border-collapse: collapse; width: 100%; }
        .nilai th, .nilai td { border: 1px solid #333; padding: 2.4mm 2.2mm; vertical-align: middle; }
        .nilai th { background: #f4f4fa; text-align: center; font-size: 10px; }
        .nilai .no { width: 7%; text-align: center; }
        .nilai .mapel { width: 24%; }
        .nilai .angka { width: 12%; text-align: center; }
        .nilai .capaian { width: 57%; text-align: justify; line-height: 1.25; }
        .nilai td.no, .nilai td.angka { text-align: center; }
        .kelompok td { padding: 2.4mm; font-weight: 700; background: #fff; }
        .section-title { border: 1px solid #333; background: #f4f4fa; padding: 2.5mm; text-align: center; font-weight: 700; font-size: 11px; }
        .section-body { border: 1px solid #333; border-top: 0; padding: 4mm; line-height: 1.35; text-align: justify; }
        .spacer { height: 5mm; }
        .ekstra th, .ekstra td { border: 1px solid #333; padding: 2.5mm; }
        .ekstra th { background: #f4f4fa; text-align: center; }
        .ekstra .no { width: 8%; text-align: center; }
        .ekstra .nama { width: 25%; }
        .footer { position: absolute; bottom: 0; left: 0; right: 0; border-top: 1px solid #333; padding-top: 3mm; font-family: "Courier New", monospace; font-size: 9px; font-weight: 700; font-style: italic; }
        .footer .halaman { float: right; }
        .box-row { display: table; width: 100%; table-layout: fixed; }
        .box-col { display: table-cell; vertical-align: top; }
        .box-col:first-child { width: 29%; padding-right: 5mm; }
        .box-col:last-child { width: 71%; }
        .absensi th, .absensi td, .catatan td { border: 1px solid #333; padding: 2.5mm; }
        .absensi th, .catatan .title { background: #f4f4fa; text-align: center; font-weight: 700; }
        .absensi td:last-child { text-align: center; }
        .catatan { width: 100%; height: 39mm; }
        .catatan .isi { height: 29mm; vertical-align: top; }
        .kenaikan { border: 1px solid #333; margin-top: 5mm; padding: 3mm; text-align: center; font-weight: 700; }
        .tanggapan { margin-top: 5mm; height: 43mm; }
        .tanggapan .isi { height: 34mm; vertical-align: top; }
        .signature { width: 100%; margin-top: 6mm; text-align: center; }
        .signature td { width: 33.33%; vertical-align: top; height: 41mm; }
        .signature .line { padding-top: 27mm; text-decoration: underline; }
        .signature .wali { text-align: center; }
        .signature .kepala { text-align: center; }
        .small { font-size: 9px; }
    </style>
</head>
<body>
@php
    $baris = collect($baris)->values();
    $wajib = $baris->take(8);
    $pilihan = $baris->slice(8)->values();
    $fase = str_starts_with(strtoupper($siswa->kelas?->nama_kelas ?? ''), 'X') ? 'F' : '-';
    $tahun = $tahunAjaran->nama_tahun ?? '-';
    $semester = ucfirst($tahunAjaran->semester ?? '-');
    $nis = $siswa->nis ?? '-';
    $nisn = $siswa->nisn ?? '-';
    $namaWali = $wali?->nama_guru ?? '';
    $nipWali = $wali?->nip ?? '';
    $namaMapel = fn ($row) => $row['mapel']->nama_mapel ?? '-';
    $capaian = function ($row) {
        if (filled($row['nilai']?->capaian_kompetensi)) {
            return $row['nilai']->capaian_kompetensi;
        }

        $nilai = $row['nilai']?->nilai_raport;
        if ($nilai === null || $nilai === '') return 'Capaian kompetensi belum diisi.';
        if ($nilai >= 75) return 'Mencapai kompetensi dengan sangat baik dalam memahami dan menerapkan materi pembelajaran.';
        if ($nilai >= 60) return 'Mencapai kompetensi dengan baik dalam memahami materi pembelajaran dan perlu penguatan pada beberapa bagian.';
        return 'Perlu peningkatan dalam memahami dan menerapkan kompetensi pada mata pelajaran ini.';
    };
    $mapelRow = function ($row, $index) use ($namaMapel, $capaian) {
        $nilai = $row['nilai']?->nilai_raport;
        echo '<tr><td class="no">'.e($index).'</td><td class="mapel">'.e($namaMapel($row)).'</td><td class="angka">'.e($nilai ?? '-').'</td><td class="capaian">'.e($capaian($row)).'</td></tr>';
    };
@endphp

<div class="page">
    @include('pdf.partials.rapor-identitas', ['siswa' => $siswa, 'tahunAjaran' => $tahunAjaran, 'fase' => $fase, 'nis' => $nis, 'nisn' => $nisn])
    <h1>LAPORAN HASIL BELAJAR</h1>
    <table class="nilai">
        <thead><tr><th class="no">No</th><th class="mapel">Mata Pelajaran</th><th class="angka">Nilai Akhir</th><th class="capaian">Capaian Kompetensi</th></tr></thead>
        <tbody>
            <tr class="kelompok"><td colspan="4">Mata Pelajaran Wajib</td></tr>
            @foreach($wajib as $index => $row)
                @php $mapelRow($row, $index + 1); @endphp
            @endforeach
            @if($pilihan->isNotEmpty())
                <tr class="kelompok"><td colspan="4">Mata Pelajaran Pilihan</td></tr>
                @php $mapelRow($pilihan->first(), 1); @endphp
            @endif
        </tbody>
    </table>
    <div class="footer"><span>{{ $siswa->kelas?->nama_kelas ?? '-' }} &nbsp;|&nbsp; {{ strtoupper($siswa->nama_siswa) }} &nbsp;|&nbsp; {{ $nis }}</span><span class="halaman">Halaman &nbsp;: 1</span></div>
</div>

<div class="page">
    @include('pdf.partials.rapor-identitas', ['siswa' => $siswa, 'tahunAjaran' => $tahunAjaran, 'fase' => $fase, 'nis' => $nis, 'nisn' => $nisn])
    <table class="nilai">
        <thead><tr><th class="no">No</th><th class="mapel">Mata Pelajaran</th><th class="angka">Nilai Akhir</th><th class="capaian">Capaian Kompetensi</th></tr></thead>
        <tbody>
            <tr class="kelompok"><td colspan="4">Mata Pelajaran Pilihan</td></tr>
            @forelse($pilihan->slice(1) as $index => $row)
                @php $mapelRow($row, $index + 2); @endphp
            @empty
                <tr><td colspan="4" class="small">Belum ada mata pelajaran pilihan lainnya.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="spacer"></div>
    <div class="section-title">Kokurikuler</div>
    <div class="section-body">
        Pada semester ini, ananda menunjukkan perkembangan dalam penguatan profil lulusan melalui kegiatan kokurikuler.<br>
        Pada dimensi penalaran kritis, ananda mampu mengambil keputusan berdasarkan informasi yang relevan.<br>
        Pada dimensi kreativitas, ananda mampu mengembangkan gagasan dan menghasilkan karya.<br>
        Pada dimensi kemandirian, ananda menunjukkan tanggung jawab dalam menyelesaikan tugas.<br>
        Pada dimensi komunikasi, ananda mampu menyampaikan gagasan dengan baik.
    </div>
    <div class="spacer"></div>
    <table class="ekstra">
        <thead><tr><th class="no">No</th><th class="nama">Ekstrakurikuler</th><th>Keterangan</th></tr></thead>
        <tbody><tr><td class="no">1</td><td>PRAMUKA</td><td>Belum ada data ekstrakurikuler.</td></tr></tbody>
    </table>
    <div class="footer"><span>{{ $siswa->kelas?->nama_kelas ?? '-' }} &nbsp;|&nbsp; {{ strtoupper($siswa->nama_siswa) }} &nbsp;|&nbsp; {{ $nis }}</span><span class="halaman">Halaman &nbsp;: 2</span></div>
</div>

<div class="page">
    @include('pdf.partials.rapor-identitas', ['siswa' => $siswa, 'tahunAjaran' => $tahunAjaran, 'fase' => $fase, 'nis' => $nis, 'nisn' => $nisn])
    <div class="box-row">
        <div class="box-col">
            <table class="absensi">
                <tr><th colspan="2">Ketidakhadiran</th></tr>
                <tr><td>Sakit</td><td>{{ $raport?->sakit ?? 0 }} hari</td></tr>
                <tr><td>Izin</td><td>{{ $raport?->izin ?? 0 }} hari</td></tr>
                <tr><td>Tanpa Keterangan</td><td>{{ $raport?->alpa ?? 0 }} hari</td></tr>
            </table>
        </div>
        <div class="box-col">
            <table class="catatan"><tr><td class="title">Catatan Wali Kelas</td></tr><tr><td class="isi">{{ $raport?->catatan ?? '' }}</td></tr></table>
        </div>
    </div>
    <div class="kenaikan">Keterangan Kenaikan Kelas &nbsp;: &nbsp; Naik ke kelas XII</div>
    <table class="catatan tanggapan"><tr><td class="title">Tanggapan Orang Tua/Wali Murid</td></tr><tr><td class="isi"></td></tr></table>
    <table class="signature">
        <tr>
            <td>Orang Tua Murid<div class="line">................................</div></td>
            <td>Wali Kelas<div class="line">{{ $namaWali ?: '................................' }}</div>@if($nipWali)<div>NIP. {{ $nipWali }}</div>@endif</td>
            <td>Kepala Sekolah<div class="line">................................</div></td>
        </tr>
    </table>
    <div class="footer"><span>{{ $siswa->kelas?->nama_kelas ?? '-' }} &nbsp;|&nbsp; {{ strtoupper($siswa->nama_siswa) }} &nbsp;|&nbsp; {{ $nis }}</span><span class="halaman">Halaman &nbsp;: 3</span></div>
</div>
</body>
</html>
