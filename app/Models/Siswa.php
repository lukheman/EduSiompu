<?php

namespace App\Models;

use App\Models\Concerns\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use HasAvatar, HasFactory, Notifiable;

    protected $table = 'siswa';

    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'id_kelas', 'nis', 'nisn', 'nama_siswa', 'nik', 'tempat_lahir',
        'tanggal_lahir', 'jenis_kelamin', 'agama', 'kewarganegaraan',
        'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos',
        'jenis_tempat_tinggal', 'transportasi_ke_sekolah', 'password', 'id_orang_tua',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    protected $hidden = [
        'password',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'id_orang_tua', 'id_orang_tua');
    }

    public function raport()
    {
        return $this->hasMany(Raport::class, 'id_siswa', 'id_siswa');
    }
}
