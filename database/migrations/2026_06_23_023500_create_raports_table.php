<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raport', function (Blueprint $table) {
            $table->id('id_raport');
            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_tahun_ajaran');
            $table->unsignedBigInteger('id_kelas');
            $table->integer('sakit')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('alpa')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('id_tahun_ajaran')->references('id_tahun_ajaran')->on('tahun_ajaran')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            
            $table->unique(['id_siswa', 'id_tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raport');
    }
};
