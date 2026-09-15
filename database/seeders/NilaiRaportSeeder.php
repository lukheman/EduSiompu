<?php

namespace Database\Seeders;

use App\Models\GuruAmpu;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiRaportSeeder extends Seeder
{
    /**
     * Seed raport + nilai untuk setiap siswa pada tahun ajaran aktif.
     * Idempoten: aman dijalankan berulang (firstOrCreate / updateOrCreate).
     */
    public function run(): void
    {
        mt_srand(20260701);

        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first()
            ?? TahunAjaran::orderBy('id_tahun_ajaran')->first();

        if (! $tahunAjaran) {
            $this->command->warn('NilaiRaportSeeder: tidak ada tahun ajaran, dilewati.');

            return;
        }

        $mapels = MataPelajaran::orderBy('id_mata_pelajaran')->get();
        $guruIds = DB::table('guru')->orderBy('id_guru')->pluck('id_guru')->all();

        if ($mapels->isEmpty() || empty($guruIds)) {
            $this->command->warn('NilaiRaportSeeder: tidak ada mapel/guru, dilewati.');

            return;
        }

        $catatans = [
            'Pertahankan prestasimu dan terus tingkatkan belajar.',
            'Hasil yang baik, tingkatkan lagi kedisiplinan.',
            'Cukup baik, perlu bimbingan lebih intensif.',
            'Tingkatkan motivasi dan kehadiran belajar.',
            null, null,
        ];

        foreach (Kelas::orderBy('id_kelas')->get() as $kelas) {
            $this->pastikanGuruAmpu($kelas->id_kelas, $tahunAjaran->id_tahun_ajaran, $mapels, $guruIds);

            $ampus = GuruAmpu::where('id_kelas', $kelas->id_kelas)
                ->where('id_tahun_ajaran', $tahunAjaran->id_tahun_ajaran)
                ->get();

            foreach (Siswa::where('id_kelas', $kelas->id_kelas)->orderBy('id_siswa')->get() as $siswa) {
                $raport = Raport::firstOrCreate(
                    ['id_siswa' => $siswa->id_siswa, 'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran],
                    [
                        'id_kelas' => $kelas->id_kelas,
                        'sakit' => $this->hitungAbsensi($siswa->id_siswa, 'sakit'),
                        'izin' => $this->hitungAbsensi($siswa->id_siswa, 'izin'),
                        'alpa' => $this->hitungAbsensi($siswa->id_siswa, 'alpa'),
                        'catatan' => $catatans[mt_rand(0, count($catatans) - 1)],
                    ]
                );

                $kemampuan = mt_rand(72, 95);

                foreach ($ampus as $ampu) {
                    $this->seedNilai($raport->id_raport, $ampu->id_mata_pelajaran, $kemampuan);
                }
            }
        }

        $this->command->info('NilaiRaportSeeder: raport + nilai siswa berhasil di-seed.');
    }

    private function pastikanGuruAmpu(int $idKelas, int $idTahunAjaran, $mapels, array $guruIds): void
    {
        foreach ($mapels as $index => $mapel) {
            GuruAmpu::firstOrCreate(
                [
                    'id_kelas' => $idKelas,
                    'id_mata_pelajaran' => $mapel->id_mata_pelajaran,
                    'id_tahun_ajaran' => $idTahunAjaran,
                ],
                ['id_guru' => $guruIds[$index % count($guruIds)]]
            );
        }
    }

    private function hitungAbsensi(int $idSiswa, string $status): int
    {
        $jumlah = DB::table('absensi')->where('id_siswa', $idSiswa)->where('status_kehadiran', $status)->count();

        return $jumlah > 0 ? $jumlah : mt_rand(0, 2);
    }

    private function skor(int $kemampuan): int
    {
        return max(60, min(100, $kemampuan + mt_rand(-6, 6)));
    }

    private function predikat(?int $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }
        if ($nilai >= 90) {
            return 'A';
        }
        if ($nilai >= 80) {
            return 'B';
        }
        if ($nilai >= 70) {
            return 'C';
        }

        return 'D';
    }

    private function seedNilai(int $idRaport, int $idMapel, int $kemampuan): void
    {
        $payload = [];
        $rataAspek = [];

        foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
            $scores = [];
            foreach ($koloms as $kolom) {
                $payload[$kolom] = $this->skor($kemampuan);
                $scores[] = $payload[$kolom];
            }
            $rataAspek[] = NilaiRaport::rataAspek($scores);
        }

        $payload['nilai_ulangan_semester'] = $this->skor($kemampuan);
        $rataAspek[] = $payload['nilai_ulangan_semester'];

        $nilaiRaport = (int) round(array_sum($rataAspek) / count($rataAspek));

        $payload['predikat_afektif'] = $this->predikat(NilaiRaport::rataAspek([$payload['nilai_afektif_1'], $payload['nilai_afektif_2'], $payload['nilai_afektif_3']]));
        $payload['predikat_psikomotor'] = $this->predikat(NilaiRaport::rataAspek([$payload['nilai_psikomotor_1'], $payload['nilai_psikomotor_2'], $payload['nilai_psikomotor_3'], $payload['nilai_psikomotor_4']]));
        $payload['nilai_raport'] = $nilaiRaport;
        $payload['predikat_raport'] = $this->predikat($nilaiRaport);

        NilaiRaport::updateOrCreate(
            ['id_raport' => $idRaport, 'id_mata_pelajaran' => $idMapel],
            $payload
        );
    }
}
