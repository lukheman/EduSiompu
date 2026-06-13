<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TahunAjaran;

class TahunAjaranFactory extends Factory
{
    protected $model = TahunAjaran::class;

    public function definition(): array
    {
        
            return [
                'nama_tahun' => '202' . $this->faker->numberBetween(0, 5) . '/202' . $this->faker->numberBetween(1, 6),
                'semester' => $this->faker->randomElement(['ganjil', 'genap']),
                'status_aktif' => $this->faker->boolean(),
            ];
        
    }
}
