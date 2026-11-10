<?php

namespace Database\Factories;

use App\Enums\StatusSilabus;
use App\Models\GuruAmpu;
use App\Models\Silabus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Silabus>
 */
class SilabusFactory extends Factory
{
    protected $model = Silabus::class;

    public function definition(): array
    {
        return [
            'id_guru_ampu' => GuruAmpu::factory(),
            'status' => StatusSilabus::DRAFT->value,
            'catatan_terakhir' => null,
        ];
    }

    public function diajukan(): static
    {
        return $this->state(fn () => [
            'status' => StatusSilabus::DIAJUKAN->value,
            'diajukan_at' => now(),
        ]);
    }

    public function revisi(): static
    {
        return $this->state(fn () => [
            'status' => StatusSilabus::REVISI->value,
            'catatan_terakhir' => 'Perbaiki alokasi jam pertemuan.',
        ]);
    }

    public function disetujui(): static
    {
        return $this->state(fn () => [
            'status' => StatusSilabus::DISETUJUI->value,
            'diajukan_at' => now()->subDay(),
            'direview_at' => now(),
        ]);
    }
}
