<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nis')->nullable()->unique()->after('nisn');
            $table->string('nik', 16)->nullable()->unique()->after('nis');
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama')->nullable()->after('tanggal_lahir');
            $table->string('kewarganegaraan', 3)->nullable()->after('agama');
            $table->text('alamat')->nullable()->after('kewarganegaraan');
            $table->string('rt', 5)->nullable()->after('alamat');
            $table->string('rw', 5)->nullable()->after('rt');
            $table->string('dusun')->nullable()->after('rw');
            $table->string('kelurahan')->nullable()->after('dusun');
            $table->string('kecamatan')->nullable()->after('kelurahan');
            $table->string('kode_pos', 10)->nullable()->after('kecamatan');
            $table->string('jenis_tempat_tinggal')->nullable()->after('kode_pos');
            $table->string('transportasi_ke_sekolah')->nullable()->after('jenis_tempat_tinggal');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropUnique(['nis']);
            $table->dropUnique(['nik']);
            $table->dropColumn([
                'nis', 'nik', 'tempat_lahir', 'tanggal_lahir', 'agama',
                'kewarganegaraan', 'alamat', 'rt', 'rw', 'dusun',
                'kelurahan', 'kecamatan', 'kode_pos', 'jenis_tempat_tinggal',
                'transportasi_ke_sekolah',
            ]);
        });
    }
};
