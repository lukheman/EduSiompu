<?php

namespace App\Models;

use App\Models\Concerns\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class KepalaSekolah extends Authenticatable
{
    use HasAvatar, HasFactory, Notifiable;

    protected $table = 'kepala_sekolah';

    protected $primaryKey = 'id_kepala_sekolah';

    protected $fillable = ['nip', 'nama_kepala_sekolah', 'email', 'password', 'avatar'];

    protected $hidden = [
        'password',
    ];
}
