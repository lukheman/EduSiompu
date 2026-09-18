<?php

namespace Database\Factories;

use App\Models\KepalaSekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KepalaSekolah>
 */
class KepalaSekolahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nip' => $this->faker->unique()->numerify('##################'),
            'nama_kepala_sekolah' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];
    }
}
