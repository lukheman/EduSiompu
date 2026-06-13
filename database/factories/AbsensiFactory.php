<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Absensi;

class AbsensiFactory extends Factory
{
    protected $model = Absensi::class;

    public function definition(): array
    {
        
            return [
                'id_pertemuan' => \App\Models\Pertemuan::factory(),
                'id_siswa' => \App\Models\Siswa::factory(),
                'status_kehadiran' => $this->faker->randomElement(['hadir', 'sakit', 'izin', 'alpa']),
            ];
        
    }
}
