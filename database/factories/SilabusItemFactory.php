<?php

namespace Database\Factories;

use App\Models\Silabus;
use App\Models\SilabusItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SilabusItem>
 */
class SilabusItemFactory extends Factory
{
    protected $model = SilabusItem::class;

    public function definition(): array
    {
        return [
            'id_silabus' => Silabus::factory(),
            'urutan' => 1,
            'materi_pokok' => $this->faker->sentence(4),
            'deskripsi' => $this->faker->sentence(10),
            'alokasi_jam' => $this->faker->numberBetween(1, 8),
        ];
    }
}
