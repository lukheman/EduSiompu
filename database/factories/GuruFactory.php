<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Guru;

class GuruFactory extends Factory
{
    protected $model = Guru::class;

    public function definition(): array
    {
        
            return [
                'nip' => $this->faker->unique()->numerify('##################'),
                'nama_guru' => $this->faker->name(),
                'password' => bcrypt('password'),
            ];
        
    }
}
