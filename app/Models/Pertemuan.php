<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    use HasFactory;

    protected $table = 'pertemuan';
    protected $primaryKey = 'id_pertemuan';
    protected $fillable = ['id_guru_ampu', 'pertemuan_ke', 'tanggal', 'pokok_bahasan'];
}
