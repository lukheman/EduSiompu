<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('silabus', function (Blueprint $table) {
            $table->id('id_silabus');
            $table->foreignId('id_guru_ampu')
                ->unique()
                ->constrained('guru_ampu', 'id_guru_ampu')
                ->onDelete('cascade');
            $table->string('status')->default('draft');
            $table->text('catatan_terakhir')->nullable();
            $table->dateTime('diajukan_at')->nullable();
            $table->dateTime('direview_at')->nullable();
            $table->foreignId('id_kepala_sekolah')
                ->nullable()
                ->constrained('kepala_sekolah', 'id_kepala_sekolah')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('silabus_item', function (Blueprint $table) {
            $table->id('id_silabus_item');
            $table->foreignId('id_silabus')
                ->constrained('silabus', 'id_silabus')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->string('materi_pokok');
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('alokasi_jam')->nullable();
            $table->timestamps();
        });

        Schema::create('silabus_riwayat', function (Blueprint $table) {
            $table->id('id_silabus_riwayat');
            $table->foreignId('id_silabus')
                ->constrained('silabus', 'id_silabus')
                ->onDelete('cascade');
            $table->string('status');
            $table->text('catatan')->nullable();
            $table->string('oleh_role');
            $table->unsignedBigInteger('oleh_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('silabus_riwayat');
        Schema::dropIfExists('silabus_item');
        Schema::dropIfExists('silabus');
    }
};
