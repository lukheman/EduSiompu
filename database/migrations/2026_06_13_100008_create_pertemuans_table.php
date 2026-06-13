<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id("id_pertemuan");
            $table->foreignId("id_guru_ampu")->constrained("guru_ampu", "id_guru_ampu")->onDelete("cascade");
            $table->integer("pertemuan_ke");
            $table->date("tanggal");
            $table->text("pokok_bahasan");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertemuan');
    }
};
