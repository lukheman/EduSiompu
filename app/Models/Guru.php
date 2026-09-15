<?php

namespace App\Models;

use App\Models\Concerns\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable
{
    use HasAvatar, HasFactory, Notifiable;

    protected $table = 'guru';

    protected $primaryKey = 'id_guru';

    protected $fillable = ['nip', 'nama_guru', 'password'];

    protected $hidden = [
        'password',
    ];

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'id_guru', 'id_guru');
    }
}
