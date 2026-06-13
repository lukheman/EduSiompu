<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    protected $fillable = ['nip', 'nama_guru', 'password'];

    protected $hidden = [
        'password',
    ];
}
