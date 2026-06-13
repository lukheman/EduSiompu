<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MataPelajaran;

class MataPelajaranFactory extends Factory
{
    protected $model = MataPelajaran::class;

    public function definition(): array
    {
        
            return [
                'kode_mapel' => strtoupper($this->faker->bothify('MP-####')),
                'nama_mapel' => $this->faker->words(2, true),
            ];
        
    }
}
