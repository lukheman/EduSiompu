<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pertemuan;

class PertemuanFactory extends Factory
{
    protected $model = Pertemuan::class;

    public function definition(): array
    {
        
            return [
                'id_guru_ampu' => \App\Models\GuruAmpu::factory(),
                'pertemuan_ke' => $this->faker->numberBetween(1, 16),
                'tanggal' => $this->faker->date(),
                'pokok_bahasan' => $this->faker->sentence(),
            ];
        
    }
}
