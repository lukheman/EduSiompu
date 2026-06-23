<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';
    protected $primaryKey = 'id_jadwal_pelajaran';

    protected $fillable = [
        'id_guru_ampu',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function guruAmpu()
    {
        return $this->belongsTo(GuruAmpu::class, 'id_guru_ampu', 'id_guru_ampu');
    }
}
