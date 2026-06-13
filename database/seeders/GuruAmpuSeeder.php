<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuruAmpu;

class GuruAmpuSeeder extends Seeder
{
    public function run(): void
    {
        GuruAmpu::factory()->count(10)->create();
    }
}
