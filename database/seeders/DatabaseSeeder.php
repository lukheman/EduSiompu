<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\TahunAjaranSeeder::class,
            \Database\Seeders\KelasSeeder::class,
            \Database\Seeders\MataPelajaranSeeder::class,
            \Database\Seeders\GuruSeeder::class,
            \Database\Seeders\SiswaSeeder::class,
            \Database\Seeders\GuruAmpuSeeder::class,
            \Database\Seeders\PertemuanSeeder::class,
            \Database\Seeders\AbsensiSeeder::class,
            \Database\Seeders\MateriSeeder::class,
        ]);
    }
}
