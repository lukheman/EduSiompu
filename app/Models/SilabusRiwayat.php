<?php

namespace App\Models;

use App\Enums\StatusSilabus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SilabusRiwayat extends Model
{
    use HasFactory;

    protected $table = 'silabus_riwayat';

    protected $primaryKey = 'id_silabus_riwayat';

    protected $fillable = [
        'id_silabus',
        'status',
        'catatan',
        'oleh_role',
        'oleh_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusSilabus::class,
        ];
    }

    public function silabus()
    {
        return $this->belongsTo(Silabus::class, 'id_silabus', 'id_silabus');
    }
}
