<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuruFactory extends Factory
{
    protected $model = Guru::class;

    public function definition(): array
    {

        return [
            'nip' => $this->faker->unique()->numerify('##################'),
            'nama_guru' => $this->faker->name(),
            'password' => bcrypt('password123'),
        ];

    }
}
