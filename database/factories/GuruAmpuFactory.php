<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GuruAmpu;

class GuruAmpuFactory extends Factory
{
    protected $model = GuruAmpu::class;

    public function definition(): array
    {
        
            return [
                'id_guru' => \App\Models\Guru::factory(),
                'id_mata_pelajaran' => \App\Models\MataPelajaran::factory(),
                'id_kelas' => \App\Models\Kelas::factory(),
                'id_tahun_ajaran' => \App\Models\TahunAjaran::factory(),
            ];
        
    }
}
