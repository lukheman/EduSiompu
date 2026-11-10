<?php

namespace App\Models;

use App\Enums\StatusSilabus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Silabus extends Model
{
    use HasFactory;

    protected $table = 'silabus';

    protected $primaryKey = 'id_silabus';

    protected $fillable = [
        'id_guru_ampu',
        'status',
        'catatan_terakhir',
        'diajukan_at',
        'direview_at',
        'id_kepala_sekolah',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusSilabus::class,
            'diajukan_at' => 'datetime',
            'direview_at' => 'datetime',
        ];
    }

    public function guruAmpu()
    {
        return $this->belongsTo(GuruAmpu::class, 'id_guru_ampu', 'id_guru_ampu');
    }

    public function items()
    {
        return $this->hasMany(SilabusItem::class, 'id_silabus', 'id_silabus')->orderBy('urutan');
    }

    public function riwayat()
    {
        return $this->hasMany(SilabusRiwayat::class, 'id_silabus', 'id_silabus')->latest();
    }

    public function reviewer()
    {
        return $this->belongsTo(KepalaSekolah::class, 'id_kepala_sekolah', 'id_kepala_sekolah');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', StatusSilabus::DISETUJUI->value);
    }

    public function bisaDiedit(): bool
    {
        return ! $this->status->isLocked();
    }

    public function catatRiwayat(StatusSilabus $status, ?string $catatan, string $role, ?int $olehId): SilabusRiwayat
    {
        return $this->riwayat()->create([
            'status' => $status->value,
            'catatan' => $catatan,
            'oleh_role' => $role,
            'oleh_id' => $olehId,
        ]);
    }
}
