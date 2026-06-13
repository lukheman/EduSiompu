<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruAmpu extends Model
{
    use HasFactory;

    protected $table = 'guru_ampu';
    protected $primaryKey = 'id_guru_ampu';
    protected $fillable = ['id_guru', 'id_mata_pelajaran', 'id_kelas', 'id_tahun_ajaran'];
}
