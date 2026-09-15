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
            $table->dropColumn([
                'nilai_afektif', 'nilai_psikomotor', 'nilai_tugas', 'nilai_ulangan_harian',
            ]);

            $table->integer('nilai_afektif_1')->nullable();
            $table->integer('nilai_afektif_2')->nullable();
            $table->integer('nilai_afektif_3')->nullable();

            $table->integer('nilai_psikomotor_1')->nullable();
            $table->integer('nilai_psikomotor_2')->nullable();
            $table->integer('nilai_psikomotor_3')->nullable();
            $table->integer('nilai_psikomotor_4')->nullable();

            $table->integer('nilai_tugas_1')->nullable();
            $table->integer('nilai_tugas_2')->nullable();
            $table->integer('nilai_tugas_3')->nullable();
            $table->integer('nilai_tugas_4')->nullable();

            $table->integer('nilai_ulangan_harian_1')->nullable();
            $table->integer('nilai_ulangan_harian_2')->nullable();
            $table->integer('nilai_ulangan_harian_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_raport', function (Blueprint $table) {
            $table->dropColumn([
                'nilai_afektif_1', 'nilai_afektif_2', 'nilai_afektif_3',
                'nilai_psikomotor_1', 'nilai_psikomotor_2', 'nilai_psikomotor_3', 'nilai_psikomotor_4',
                'nilai_tugas_1', 'nilai_tugas_2', 'nilai_tugas_3', 'nilai_tugas_4',
                'nilai_ulangan_harian_1', 'nilai_ulangan_harian_2', 'nilai_ulangan_harian_3',
            ]);

            $table->integer('nilai_afektif')->nullable();
            $table->integer('nilai_psikomotor')->nullable();
            $table->integer('nilai_tugas')->nullable();
            $table->integer('nilai_ulangan_harian')->nullable();
        });
    }
};
