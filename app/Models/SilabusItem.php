<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SilabusItem extends Model
{
    use HasFactory;

    protected $table = 'silabus_item';

    protected $primaryKey = 'id_silabus_item';

    protected $fillable = [
        'id_silabus',
        'urutan',
        'materi_pokok',
        'deskripsi',
        'alokasi_jam',
    ];

    public function silabus()
    {
        return $this->belongsTo(Silabus::class, 'id_silabus', 'id_silabus');
    }
}
