<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuis', function (Blueprint $table) {
            $table->id('id_kuis');
            $table->foreignId('id_jadwal_pelajaran')
                ->constrained('jadwal_pelajaran', 'id_jadwal_pelajaran')
                ->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('tenggat_waktu');
            $table->unsignedSmallInteger('durasi_menit')->default(30);
            $table->timestamps();
        });

        Schema::create('soal_kuis', function (Blueprint $table) {
            $table->id('id_soal');
            $table->foreignId('id_kuis')->constrained('kuis', 'id_kuis')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->string('pilihan_a');
            $table->string('pilihan_b');
            $table->string('pilihan_c');
            $table->string('pilihan_d');
            $table->char('jawaban_benar', 1);
            $table->unsignedSmallInteger('poin')->default(1);
            $table->timestamps();
        });

        Schema::create('pengerjaan_kuis', function (Blueprint $table) {
            $table->id('id_pengerjaan');
            $table->foreignId('id_kuis')->constrained('kuis', 'id_kuis')->onDelete('cascade');
            $table->foreignId('id_siswa')->constrained('siswa', 'id_siswa')->onDelete('cascade');
            $table->unsignedSmallInteger('nilai')->nullable();
            $table->unsignedSmallInteger('jumlah_benar')->default(0);
            $table->unsignedSmallInteger('jumlah_soal')->default(0);
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->timestamps();

            $table->unique(['id_kuis', 'id_siswa']);
        });

        Schema::create('jawaban_kuis', function (Blueprint $table) {
            $table->id('id_jawaban');
            $table->foreignId('id_pengerjaan')
                ->constrained('pengerjaan_kuis', 'id_pengerjaan')
                ->onDelete('cascade');
            $table->foreignId('id_soal')->constrained('soal_kuis', 'id_soal')->onDelete('cascade');
            $table->char('jawaban', 1)->nullable();
            $table->boolean('benar')->default(false);
            $table->timestamps();

            $table->unique(['id_pengerjaan', 'id_soal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_kuis');
        Schema::dropIfExists('pengerjaan_kuis');
        Schema::dropIfExists('soal_kuis');
        Schema::dropIfExists('kuis');
    }
};
