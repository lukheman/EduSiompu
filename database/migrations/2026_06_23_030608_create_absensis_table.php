<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id("id_absensi");
            $table->foreignId("id_jadwal_pelajaran")->constrained("jadwal_pelajaran", "id_jadwal_pelajaran")->onDelete("cascade");
            $table->foreignId("id_siswa")->constrained("siswa", "id_siswa")->onDelete("cascade");
            $table->date("tanggal");
            $table->enum("status_kehadiran", ["hadir", "sakit", "izin", "alpa"]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
