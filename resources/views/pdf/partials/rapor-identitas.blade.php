<table class="identitas">
    <tr>
        <td class="label">Nama Murid</td><td>: {{ $siswa->nama_siswa }}</td>
        <td class="label-right">Kelas</td><td class="value-right">: {{ $siswa->kelas?->nama_kelas ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">NIS/NISN</td><td>: {{ $nis }} / {{ $nisn }}</td>
        <td class="label-right">Fase</td><td>: {{ $fase }}</td>
    </tr>
    <tr>
        <td class="label">Sekolah</td><td>: SMAN 1 SIOMPU</td>
        <td class="label-right">Semester</td><td>: {{ ucfirst($tahunAjaran->semester ?? '-') }}</td>
    </tr>
    <tr>
        <td class="label">Alamat</td><td>: Jln. Poros Siompu, Desa Batuawu, Kecamatan Siompu</td>
        <td class="label-right">Tahun Ajaran</td><td>: {{ $tahunAjaran->nama_tahun ?? '-' }}</td>
    </tr>
</table>
<div class="garis"></div>
