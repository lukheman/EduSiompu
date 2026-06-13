<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kelas;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        
            return [
                'nama_kelas' => $this->faker->word() . ' ' . $this->faker->numberBetween(1, 10),
            ];
        
    }
}
