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
        Schema::table('kepala_sekolah', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('nama_kepala_sekolah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepala_sekolah', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
