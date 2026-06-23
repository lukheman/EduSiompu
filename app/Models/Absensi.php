<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = ['id_jadwal_pelajaran', 'id_siswa', 'tanggal', 'status_kehadiran'];

    protected $casts = [
        'status_kehadiran' => \App\Enums\StatusKehadiran::class,
        'tanggal' => 'date',
    ];

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'id_jadwal_pelajaran', 'id_jadwal_pelajaran');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}
