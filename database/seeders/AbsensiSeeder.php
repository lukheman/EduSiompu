<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        Absensi::factory()->count(10)->create();
    }
}
