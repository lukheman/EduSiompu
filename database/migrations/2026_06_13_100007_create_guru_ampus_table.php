<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_ampu', function (Blueprint $table) {
            $table->id("id_guru_ampu");
            $table->foreignId("id_guru")->constrained("guru", "id_guru")->onDelete("cascade");
            $table->foreignId("id_mata_pelajaran")->constrained("mata_pelajaran", "id_mata_pelajaran")->onDelete("cascade");
            $table->foreignId("id_kelas")->constrained("kelas", "id_kelas")->onDelete("cascade");
            $table->foreignId("id_tahun_ajaran")->constrained("tahun_ajaran", "id_tahun_ajaran")->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_ampu');
    }
};
