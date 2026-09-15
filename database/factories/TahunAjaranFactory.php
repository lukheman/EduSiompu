<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class TahunAjaranFactory extends Factory
{
    protected $model = TahunAjaran::class;

    public function definition(): array
    {

        return [
            'nama_tahun' => '202'.$this->faker->numberBetween(0, 5).'/202'.$this->faker->numberBetween(1, 6),
            'semester' => $this->faker->randomElement(['ganjil', 'genap']),
            'tanggal_mulai' => $this->faker->date(),
            'tanggal_akhir' => $this->faker->date(),
            'status_aktif' => $this->faker->boolean(),
        ];

    }
}
