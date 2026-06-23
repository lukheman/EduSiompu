<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    use HasFactory;

    protected $table = 'raport';
    protected $primaryKey = 'id_raport';
    protected $fillable = [
        'id_siswa', 'id_tahun_ajaran', 'id_kelas', 'sakit', 'izin', 'alpa', 'catatan'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function nilaiRaport()
    {
        return $this->hasMany(NilaiRaport::class, 'id_raport', 'id_raport');
    }
}
