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
                'nilai_pengetahuan', 'predikat_pengetahuan',
                'nilai_keterampilan', 'predikat_keterampilan',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_raport', function (Blueprint $table) {
            $table->integer('nilai_pengetahuan')->nullable();
            $table->char('predikat_pengetahuan', 1)->nullable();
            $table->integer('nilai_keterampilan')->nullable();
            $table->char('predikat_keterampilan', 1)->nullable();
        });
    }
};
