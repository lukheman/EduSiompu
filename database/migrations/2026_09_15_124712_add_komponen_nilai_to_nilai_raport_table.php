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
        Schema::table('nilai_raport', function (Blueprint $table) {
            $table->integer('nilai_afektif')->nullable()->after('predikat_keterampilan');
            $table->char('predikat_afektif', 1)->nullable()->after('nilai_afektif');
            $table->integer('nilai_psikomotor')->nullable()->after('predikat_afektif');
            $table->char('predikat_psikomotor', 1)->nullable()->after('nilai_psikomotor');
            $table->integer('nilai_tugas')->nullable()->after('predikat_psikomotor');
            $table->integer('nilai_ulangan_harian')->nullable()->after('nilai_tugas');
            $table->integer('nilai_ulangan_semester')->nullable()->after('nilai_ulangan_harian');
            $table->integer('nilai_raport')->nullable()->after('nilai_ulangan_semester');
            $table->char('predikat_raport', 1)->nullable()->after('nilai_raport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_raport', function (Blueprint $table) {
            $table->dropColumn([
                'nilai_afektif', 'predikat_afektif',
                'nilai_psikomotor', 'predikat_psikomotor',
                'nilai_tugas', 'nilai_ulangan_harian', 'nilai_ulangan_semester',
                'nilai_raport', 'predikat_raport',
            ]);
        });
    }
};
