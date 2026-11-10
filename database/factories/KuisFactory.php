<?php

namespace Database\Factories;

use App\Models\JadwalPelajaran;
use App\Models\Kuis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kuis>
 */
class KuisFactory extends Factory
{
    protected $model = Kuis::class;

    public function definition(): array
    {
        return [
            'id_jadwal_pelajaran' => JadwalPelajaran::factory(),
            'judul' => $this->faker->sentence(4),
            'deskripsi' => $this->faker->sentence(),
            'tenggat_waktu' => now()->addDays(7),
            'durasi_menit' => 30,
        ];
    }
}
