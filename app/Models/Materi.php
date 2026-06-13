<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';
    protected $primaryKey = 'id_materi';
    protected $fillable = ['id_guru_ampu', 'judul_materi', 'file_path', 'jenis_file'];
}
