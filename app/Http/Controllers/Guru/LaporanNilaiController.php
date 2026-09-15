<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruAmpu;
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

        $pdf = Pdf::loadView('pdf.laporan-nilai-mapel', [
            'kelas' => $kelas,
            'mapel' => $mapel,
            'tahunAjaran' => $tahunAjaran,
            'pengampu' => $ampu->guru,
            'siswas' => $siswas,
            'wali' => $guru,
        ])->setPaper('a4', 'landscape');

        $mapelAman = str_replace(['/', '\\', ' '], '-', $mapel->nama_mapel);

        return $pdf->download("laporan-nilai-{$mapelAman}-{$kelas->nama_kelas}.pdf");
    }
}
