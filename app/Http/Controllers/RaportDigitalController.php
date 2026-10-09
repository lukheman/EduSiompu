<?php

namespace App\Http\Controllers;

use App\Enums\StatusKehadiran;
use App\Models\Absensi;
use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class RaportDigitalController extends Controller
{
    public function cetak(int $siswa, int $tahun)
    {
        $student = Siswa::with('kelas.waliKelas')->findOrFail($siswa);
        $this->authorizeAccess($student);

        $raport = Raport::with(['nilaiRaport.mataPelajaran', 'kelas', 'tahunAjaran', 'siswa'])
            ->where('id_siswa', $student->id_siswa)
            ->where('id_tahun_ajaran', $tahun)
            ->first();

        $tahunAjaran = TahunAjaran::findOrFail($tahun);
        $ampus = GuruAmpu::where('id_kelas', $student->id_kelas)
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

            foreach (Absensi::whereIn('id_jadwal_pelajaran', $jadwalIds)->where('id_siswa', $student->id_siswa)->get() as $absen) {
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
            'siswa' => $student,
            'raport' => $raport,
            'tahunAjaran' => $tahunAjaran,
            'baris' => $baris,
            'wali' => $student->kelas?->waliKelas,
            'logoKiri' => null,
            'logoKanan' => null,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("rapor-{$student->nisn}.pdf");
    }

    private function authorizeAccess(Siswa $siswa): void
    {
        if (Auth::guard('siswa')->check() && Auth::guard('siswa')->id() !== $siswa->id_siswa) {
            abort(403);
        }

        if (Auth::guard('orang_tua')->check() && Auth::guard('orang_tua')->id() !== $siswa->id_orang_tua) {
            abort(403);
        }

        if (Auth::guard('guru')->check() && $siswa->kelas?->id_guru !== Auth::guard('guru')->id()) {
            abort(403);
        }
    }
}
