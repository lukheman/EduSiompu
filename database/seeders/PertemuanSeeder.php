<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pertemuan;

class PertemuanSeeder extends Seeder
{
    public function run(): void
    {
        Pertemuan::factory()->count(10)->create();
    }
}
