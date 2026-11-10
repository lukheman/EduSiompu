<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengerjaanKuis extends Model
{
    use HasFactory;

    protected $table = 'pengerjaan_kuis';

    protected $primaryKey = 'id_pengerjaan';

    protected $fillable = [
        'id_kuis', 'id_siswa', 'nilai', 'jumlah_benar',
        'jumlah_soal', 'waktu_mulai', 'waktu_selesai',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id_kuis');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanKuis::class, 'id_pengerjaan', 'id_pengerjaan');
    }

    public function selesai(): bool
    {
        return $this->waktu_selesai !== null;
    }
}
