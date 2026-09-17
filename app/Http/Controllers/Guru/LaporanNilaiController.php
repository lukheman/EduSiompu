<?php

namespace App\Http\Controllers\Guru;

use App\Enums\StatusKehadiran;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class LaporanNilaiController extends Controller
{
    public function cetakMapel(int $kelas, int $mapel, int $tahun)
    {
        $guru = Auth::guard('guru')->user();

        $kelas = Kelas::findOrFail($kelas);

        if ($kelas->id_guru !== $guru->id_guru) {
            abort(403, 'Anda bukan wali kelas ini.');
        }

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

        $absensiList = Absensi::whereIn('id_jadwal_pelajaran', $jadwalIds)
            ->whereIn('tanggal', $pertemuans)
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
            'wali' => $guru,
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
