<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Siswa;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        
            return [
                'id_kelas' => \App\Models\Kelas::factory(),
                'nisn' => $this->faker->unique()->numerify('##########'),
                'nama_siswa' => $this->faker->name(),
                'password' => bcrypt('password'),
            ];
        
    }
}
