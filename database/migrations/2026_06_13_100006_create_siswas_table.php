<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id("id_siswa");
            $table->foreignId("id_kelas")->constrained("kelas", "id_kelas")->onDelete("cascade");
            $table->string("nisn")->unique();
            $table->string("nama_siswa");
            $table->string("password");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
