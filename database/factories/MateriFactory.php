<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Materi;

class MateriFactory extends Factory
{
    protected $model = Materi::class;

    public function definition(): array
    {
        
            return [
                'id_guru_ampu' => \App\Models\GuruAmpu::factory(),
                'judul_materi' => $this->faker->sentence(),
                'file_path' => $this->faker->filePath(),
                'jenis_file' => $this->faker->fileExtension(),
            ];
        
    }
}
