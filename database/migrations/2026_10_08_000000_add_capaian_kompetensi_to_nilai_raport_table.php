<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai_raport', function (Blueprint $table) {
            $table->text('capaian_kompetensi')->nullable()->after('predikat_raport');
        });
    }

    public function down(): void
    {
        Schema::table('nilai_raport', function (Blueprint $table) {
            $table->dropColumn('capaian_kompetensi');
        });
    }
};
