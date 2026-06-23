<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Tahun Ajaran
        $idTahunAjaranGanjil = DB::table('tahun_ajaran')->insertGetId([
            'nama_tahun' => '2026/2027',
            'semester' => 'ganjil',
            'status_aktif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $idTahunAjaranGenap = DB::table('tahun_ajaran')->insertGetId([
            'nama_tahun' => '2026/2027',
            'semester' => 'genap',
            'status_aktif' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Kelas
        $kelasData = ['X MIPA 1', 'X MIPA 2', 'X IPS 1', 'XI MIPA 1', 'XI IPS 1'];
        $kelasIds = [];
        foreach ($kelasData as $kelas) {
            $kelasIds[$kelas] = DB::table('kelas')->insertGetId([
                'nama_kelas' => $kelas,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Mata Pelajaran
        $mapelData = [
            'MAT-01' => 'Matematika Wajib',
            'BIN-01' => 'Bahasa Indonesia',
            'BIG-01' => 'Bahasa Inggris',
            'FIS-01' => 'Fisika',
            'KIM-01' => 'Kimia',
            'SEJ-01' => 'Sejarah Indonesia',
        ];
        $mapelIds = [];
        foreach ($mapelData as $kode => $nama) {
            $mapelIds[$kode] = DB::table('mata_pelajaran')->insertGetId([
                'kode_mapel' => $kode,
                'nama_mapel' => $nama,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 4. Admin
        DB::table('admin')->insert([
            'nama' => 'Administrator EduSiompu',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'avatar' => 'avatars/admin.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. Guru
        $guruData = [
            ['198001012005011001', 'Drs. Budi Santoso, M.Pd.'],
            ['198205122008012003', 'Siti Aminah, S.Pd.'],
            ['197503042000031005', 'Hendra Gunawan, M.Si.'],
            ['198808172014022001', 'Ratna Sari, S.Pd., B.Ed.'],
            ['199012252018011002', 'Andi Pratama, S.Kom.'],
        ];
        $guruIds = [];
        foreach ($guruData as $g) {
            $guruIds[] = DB::table('guru')->insertGetId([
                'nip' => $g[0],
                'nama_guru' => $g[1],
                'password' => Hash::make('password123'), // default password for testing
                'avatar' => 'avatars/guru.png',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 6. Orang Tua
        $orangTuaData = [
            ['3201010101010001', 'Bapak Budi Hartono', '081234567890'],
            ['3201010101010002', 'Ibu Siti Khadijah', '081234567891'],
            ['3201010101010003', 'Bapak Herman Santoso', '081234567892'],
            ['3201010101010004', 'Ibu Ani Suryani', '081234567893'],
            ['3201010101010005', 'Bapak Joko Susilo', '081234567894'],
        ];

        $orangTuaIds = [];
        foreach ($orangTuaData as $ot) {
            $orangTuaIds[] = DB::table('orang_tua')->insertGetId([
                'nik' => $ot[0],
                'nama_orang_tua' => $ot[1],
                'no_hp' => $ot[2],
                'password' => Hash::make('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 7. Siswa
        $siswaNames = [
            'Agus Saputra', 'Bima Aryo', 'Citra Dewi', 'Dina Lestari', 'Eko Purnomo',
            'Fikri Haikal', 'Gita Gutawa', 'Hadi Prasetio', 'Indah Permatasari', 'Joko Susanto',
            'Kiki Amalia', 'Lukman Hakim', 'Mia Rahmawati', 'Nanda Rizky', 'Oscar Saputra',
            'Putri Ayu', 'Qori Akbar', 'Rina Wati', 'Sandi Maulana', 'Tuti Alawiyah',
            'Umar Mutaqin', 'Vina Panduwinata', 'Wawan Gunawan', 'Xaverius', 'Yana Yulianti'
        ];

        $siswaIds = [];
        $nisnBase = 10010001;
        $idx = 0;
        foreach ($kelasIds as $namaKelas => $idKelas) {
            // Put 5 students in each class
            for ($i = 0; $i < 5; $i++) {
                if ($idx >= count($siswaNames)) break;

                $siswaIds[] = DB::table('siswa')->insertGetId([
                    'id_kelas' => $idKelas,
                    'id_orang_tua' => ($idx % 2 == 0) ? $orangTuaIds[$idx % count($orangTuaIds)] : null,
                    'nisn' => (string)($nisnBase++),
                    'nama_siswa' => $siswaNames[$idx++],
                    'password' => Hash::make('password123'),
                    'avatar' => 'avatars/siswa.png',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 8. Guru Ampu (Penugasan)
        // Budi Santoso (0) ajar Matematika (MAT-01) di X MIPA 1 & X MIPA 2
        $idGuruAmpu1 = DB::table('guru_ampu')->insertGetId([
            'id_guru' => $guruIds[0],
            'id_mata_pelajaran' => $mapelIds['MAT-01'],
            'id_kelas' => $kelasIds['X MIPA 1'],
            'id_tahun_ajaran' => $idTahunAjaranGanjil,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $idGuruAmpu2 = DB::table('guru_ampu')->insertGetId([
            'id_guru' => $guruIds[0],
            'id_mata_pelajaran' => $mapelIds['MAT-01'],
            'id_kelas' => $kelasIds['X MIPA 2'],
            'id_tahun_ajaran' => $idTahunAjaranGanjil,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Siti Aminah (1) ajar Bahasa Indonesia (BIN-01) di X MIPA 1 & X IPS 1
        $idGuruAmpu3 = DB::table('guru_ampu')->insertGetId([
            'id_guru' => $guruIds[1],
            'id_mata_pelajaran' => $mapelIds['BIN-01'],
            'id_kelas' => $kelasIds['X MIPA 1'],
            'id_tahun_ajaran' => $idTahunAjaranGanjil,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Hendra Gunawan (2) ajar Fisika (FIS-01) di X MIPA 1
        $idGuruAmpu4 = DB::table('guru_ampu')->insertGetId([
            'id_guru' => $guruIds[2],
            'id_mata_pelajaran' => $mapelIds['FIS-01'],
            'id_kelas' => $kelasIds['X MIPA 1'],
            'id_tahun_ajaran' => $idTahunAjaranGanjil,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 9. Jadwal Pelajaran & Absensi (X MIPA 1 - Matematika - Budi Santoso)
        $idJadwal1 = DB::table('jadwal_pelajaran')->insertGetId([
            'id_guru_ampu' => $idGuruAmpu1,
            'hari' => 'Senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $idJadwal2 = DB::table('jadwal_pelajaran')->insertGetId([
            'id_guru_ampu' => $idGuruAmpu1,
            'hari' => 'Kamis',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:30',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Get Siswa from X MIPA 1
        $siswaXmipa1 = DB::table('siswa')->where('id_kelas', $kelasIds['X MIPA 1'])->get();

        $statuses = ['hadir', 'hadir', 'hadir', 'sakit', 'izin', 'alpa'];

        foreach ($siswaXmipa1 as $index => $s) {
            // Absen Jadwal 1 (Minggu lalu)
            DB::table('absensi')->insert([
                'id_jadwal_pelajaran' => $idJadwal1,
                'id_siswa' => $s->id_siswa,
                'tanggal' => (clone $now)->startOfWeek()->subWeeks(1)->format('Y-m-d'), // Senin minggu lalu
                'status_kehadiran' => 'hadir', // Semua hadir
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Absen Jadwal 2 (Minggu lalu)
            DB::table('absensi')->insert([
                'id_jadwal_pelajaran' => $idJadwal2,
                'id_siswa' => $s->id_siswa,
                'tanggal' => (clone $now)->startOfWeek()->subWeeks(1)->addDays(3)->format('Y-m-d'), // Kamis minggu lalu
                'status_kehadiran' => $index == 2 ? 'sakit' : ($index == 4 ? 'izin' : 'hadir'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 10. Materi
        DB::table('materi')->insert([
            [
                'id_guru_ampu' => $idGuruAmpu1,
                'judul_materi' => 'Modul 1: SPLTV dan Penerapannya',
                'file_path' => 'dummy/spltv_modul1.pdf',
                'jenis_file' => 'pdf',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_guru_ampu' => $idGuruAmpu3,
                'judul_materi' => 'PPT Teks Anekdot',
                'file_path' => 'dummy/teks_anekdot.pptx',
                'jenis_file' => 'pptx',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Output info
        $this->command->info('Data EduSiompu berhasil di-seed (tanpa factory) secara alami!');
    }
}
