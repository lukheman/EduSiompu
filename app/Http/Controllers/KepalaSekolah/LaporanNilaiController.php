<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Enums\StatusKehadiran;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanNilaiController extends Controller
{
    public function cetakSiswa(int $siswa, int $tahun)
    {
        $siswa = Siswa::with('kelas.waliKelas')->findOrFail($siswa);

        $raport = Raport::with(['nilaiRaport.mataPelajaran', 'kelas', 'tahunAjaran', 'siswa'])
            ->where('id_siswa', $siswa->id_siswa)
            ->where('id_tahun_ajaran', $tahun)
            ->first();

        $tahunAjaran = TahunAjaran::findOrFail($tahun);

        $ampus = GuruAmpu::where('id_kelas', $siswa->id_kelas)
            ->where('id_tahun_ajaran', $tahun)
            ->with('mataPelajaran')
            ->get();

        $nilaiByMapel = $raport
            ? NilaiRaport::where('id_raport', $raport->id_raport)->get()->keyBy('id_mata_pelajaran')
            : collect();

        $baris = [];
        foreach ($ampus as $ampu) {
            $jadwalIds = JadwalPelajaran::where('id_guru_ampu', $ampu->id_guru_ampu)->pluck('id_jadwal_pelajaran');
            $rekap = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];
            foreach (Absensi::whereIn('id_jadwal_pelajaran', $jadwalIds)->where('id_siswa', $siswa->id_siswa)->get() as $absen) {
                $status = $absen->status_kehadiran instanceof StatusKehadiran
                    ? $absen->status_kehadiran->value
                    : $absen->status_kehadiran;
                if (isset($rekap[$status])) {
                    $rekap[$status]++;
                }
            }
            $baris[] = [
                'mapel' => $ampu->mataPelajaran,
                'nilai' => $nilaiByMapel->get($ampu->id_mata_pelajaran),
                'rekap' => $rekap,
            ];
        }

        $pdf = Pdf::loadView('pdf.laporan-nilai-siswa', [
            'siswa' => $siswa,
            'raport' => $raport,
            'tahunAjaran' => $tahunAjaran,
            'baris' => $baris,
            'wali' => $siswa->kelas?->waliKelas,
            'logoKiri' => $this->logoBase64('logo-kiri.png'),
            'logoKanan' => $this->logoBase64('logo-kanan.png'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("laporan-nilai-{$siswa->nisn}.pdf");
    }

    public function cetakMapel(int $kelas, int $mapel, int $tahun)
    {
        $kelas = Kelas::with('waliKelas')->findOrFail($kelas);

        $ampu = GuruAmpu::where('id_kelas', $kelas->id_kelas)
            ->where('id_mata_pelajaran', $mapel)
            ->where('id_tahun_ajaran', $tahun)
            ->firstOrFail();

        $tahunAjaran = TahunAjaran::findOrFail($tahun);
        $mapel = MataPelajaran::findOrFail($mapel);

        $siswas = Siswa::where('id_kelas', $kelas->id_kelas)
            ->with(['raport' => function ($q) use ($tahun, $mapel) {
                $q->where('id_tahun_ajaran', $tahun)->with(['nilaiRaport' => function ($qq) use ($mapel) {
                    $qq->where('id_mata_pelajaran', $mapel->id_mata_pelajaran);
                }]);
            }])
            ->orderBy('nama_siswa')
            ->get();

        $jadwalIds = JadwalPelajaran::where('id_guru_ampu', $ampu->id_guru_ampu)->pluck('id_jadwal_pelajaran');

        $pertemuans = Absensi::whereIn('id_jadwal_pelajaran', $jadwalIds)
            ->select('tanggal')
            ->distinct()
            ->orderBy('tanggal')
            ->limit(20)
            ->pluck('tanggal');

        $tanggalStrings = $pertemuans->map(fn ($t) => $t->format('Y-m-d'))->all();

        $absensiList = Absensi::whereIn('id_jadwal_pelajaran', $jadwalIds)
            ->whereIn('tanggal', $tanggalStrings)
            ->get();

        $kehadiran = [];
        foreach ($absensiList as $absen) {
            $status = $absen->status_kehadiran instanceof StatusKehadiran
                ? $absen->status_kehadiran->value
                : $absen->status_kehadiran;
            $kehadiran[$absen->id_siswa][$absen->tanggal->format('Y-m-d')] = $status;
        }

        $pdf = Pdf::loadView('pdf.laporan-nilai-mapel', [
            'kelas' => $kelas,
            'mapel' => $mapel,
            'tahunAjaran' => $tahunAjaran,
            'pengampu' => $ampu->guru,
            'siswas' => $siswas,
            'wali' => $kelas->waliKelas,
            'pertemuans' => $pertemuans,
            'kehadiran' => $kehadiran,
            'logoKiri' => $this->logoBase64('logo-kiri.png'),
            'logoKanan' => $this->logoBase64('logo-kanan.png'),
        ])->setPaper('a4', 'landscape');

        $mapelAman = str_replace(['/', '\\', ' '], '-', $mapel->nama_mapel);

        return $pdf->download("laporan-nilai-{$mapelAman}-{$kelas->nama_kelas}.pdf");
    }

    private function logoBase64(string $filename): ?string
    {
        $path = public_path('images/'.$filename);

        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    }
}
