<?php

namespace Database\Factories;

use App\Models\Kuis;
use App\Models\SoalKuis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SoalKuis>
 */
class SoalKuisFactory extends Factory
{
    protected $model = SoalKuis::class;

    public function definition(): array
    {
        return [
            'id_kuis' => Kuis::factory(),
            'pertanyaan' => rtrim($this->faker->sentence(), '.').'?',
            'pilihan_a' => $this->faker->word(),
            'pilihan_b' => $this->faker->word(),
            'pilihan_c' => $this->faker->word(),
            'pilihan_d' => $this->faker->word(),
            'jawaban_benar' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'poin' => 1,
        ];
    }
}
