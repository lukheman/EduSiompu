<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id("id_materi");
            $table->foreignId("id_guru_ampu")->constrained("guru_ampu", "id_guru_ampu")->onDelete("cascade");
            $table->string("judul_materi");
            $table->string("file_path");
            $table->string("jenis_file");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
