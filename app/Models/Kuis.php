<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuis extends Model
{
    use HasFactory;

    protected $table = 'kuis';

    protected $primaryKey = 'id_kuis';

    protected $fillable = ['id_jadwal_pelajaran', 'judul', 'deskripsi', 'tenggat_waktu', 'durasi_menit'];

    protected $casts = [
        'tenggat_waktu' => 'datetime',
    ];

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'id_jadwal_pelajaran', 'id_jadwal_pelajaran');
    }

    public function soal()
    {
        return $this->hasMany(SoalKuis::class, 'id_kuis', 'id_kuis');
    }

    public function pengerjaan()
    {
        return $this->hasMany(PengerjaanKuis::class, 'id_kuis', 'id_kuis');
    }
}
