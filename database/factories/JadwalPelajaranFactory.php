<?php

namespace Database\Factories;

use App\Models\GuruAmpu;
use App\Models\JadwalPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JadwalPelajaran>
 */
class JadwalPelajaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_guru_ampu' => GuruAmpu::factory(),
            'hari' => $this->faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']),
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
        ];
    }
}
