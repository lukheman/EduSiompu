<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {

        return [
            'id_kelas' => Kelas::factory(),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'nama_siswa' => $this->faker->name(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'password' => bcrypt('password'),
        ];

    }
}
