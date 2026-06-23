<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiRaport extends Model
{
    use HasFactory;

    protected $table = 'nilai_raport';
    protected $primaryKey = 'id_nilai_raport';
    protected $fillable = [
        'id_raport', 'id_mata_pelajaran', 'nilai_pengetahuan', 'predikat_pengetahuan',
        'nilai_keterampilan', 'predikat_keterampilan'
    ];

    public function raport()
    {
        return $this->belongsTo(Raport::class, 'id_raport', 'id_raport');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mata_pelajaran', 'id_mata_pelajaran');
    }
}
