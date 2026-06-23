<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id('id_pengumpulan');
            $table->foreignId('id_tugas')->constrained('tugas', 'id_tugas')->onDelete('cascade');
            $table->foreignId('id_siswa')->constrained('siswa', 'id_siswa')->onDelete('cascade');
            $table->string('file_tugas')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('nilai')->nullable();
            $table->dateTime('waktu_pengumpulan');
            $table->timestamps();
            
            // Ensures a student only submits one assignment per tugas
            $table->unique(['id_tugas', 'id_siswa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
