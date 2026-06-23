<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_raport', function (Blueprint $table) {
            $table->id('id_nilai_raport');
            $table->unsignedBigInteger('id_raport');
            $table->unsignedBigInteger('id_mata_pelajaran');
            $table->integer('nilai_pengetahuan')->nullable();
            $table->char('predikat_pengetahuan', 1)->nullable();
            $table->integer('nilai_keterampilan')->nullable();
            $table->char('predikat_keterampilan', 1)->nullable();
            $table->timestamps();

            $table->foreign('id_raport')->references('id_raport')->on('raport')->onDelete('cascade');
            $table->foreign('id_mata_pelajaran')->references('id_mata_pelajaran')->on('mata_pelajaran')->onDelete('cascade');
            
            $table->unique(['id_raport', 'id_mata_pelajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_raport');
    }
};
