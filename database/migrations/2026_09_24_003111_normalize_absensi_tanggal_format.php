<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Samakan format tanggal absensi ke Y-m-d (ada baris lama
     * tersimpan sebagai Y-m-d H:i:s sehingga query where() gagal cocok).
     */
    public function up(): void
    {
        DB::statement('UPDATE absensi SET tanggal = DATE(tanggal)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
