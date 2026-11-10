<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanKuis extends Model
{
    use HasFactory;

    protected $table = 'jawaban_kuis';

    protected $primaryKey = 'id_jawaban';

    protected $fillable = ['id_pengerjaan', 'id_soal', 'jawaban', 'benar'];

    protected $casts = [
        'benar' => 'boolean',
    ];

    public function pengerjaan()
    {
        return $this->belongsTo(PengerjaanKuis::class, 'id_pengerjaan', 'id_pengerjaan');
    }

    public function soal()
    {
        return $this->belongsTo(SoalKuis::class, 'id_soal', 'id_soal');
    }
}
