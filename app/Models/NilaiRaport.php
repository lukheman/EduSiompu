<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiRaport extends Model
{
    use HasFactory;

    protected $table = 'nilai_raport';

    protected $primaryKey = 'id_nilai_raport';

    protected $fillable = [
        'id_raport', 'id_mata_pelajaran',
        'nilai_afektif_1', 'nilai_afektif_2', 'nilai_afektif_3', 'predikat_afektif',
        'nilai_psikomotor_1', 'nilai_psikomotor_2', 'nilai_psikomotor_3', 'nilai_psikomotor_4', 'predikat_psikomotor',
        'nilai_tugas_1', 'nilai_tugas_2', 'nilai_tugas_3', 'nilai_tugas_4',
        'nilai_ulangan_harian_1', 'nilai_ulangan_harian_2', 'nilai_ulangan_harian_3',
        'nilai_ulangan_semester', 'nilai_raport', 'predikat_raport',
    ];

    public const ASPEK_SCORES = [
        'afektif' => ['nilai_afektif_1', 'nilai_afektif_2', 'nilai_afektif_3'],
        'psikomotor' => ['nilai_psikomotor_1', 'nilai_psikomotor_2', 'nilai_psikomotor_3', 'nilai_psikomotor_4'],
        'tugas' => ['nilai_tugas_1', 'nilai_tugas_2', 'nilai_tugas_3', 'nilai_tugas_4'],
        'ulangan_harian' => ['nilai_ulangan_harian_1', 'nilai_ulangan_harian_2', 'nilai_ulangan_harian_3'],
    ];

    public static function rataAspek(array $scores): ?int
    {
        $terisi = array_filter($scores, fn ($v) => $v !== '' && $v !== null && is_numeric($v));

        if (count($terisi) === 0) {
            return null;
        }

        return (int) round(array_sum($terisi) / count($terisi));
    }

    public function getRataAfektifAttribute(): ?int
    {
        return self::rataAspek([$this->nilai_afektif_1, $this->nilai_afektif_2, $this->nilai_afektif_3]);
    }

    public function getRataPsikomotorAttribute(): ?int
    {
        return self::rataAspek([$this->nilai_psikomotor_1, $this->nilai_psikomotor_2, $this->nilai_psikomotor_3, $this->nilai_psikomotor_4]);
    }

    public function getRataTugasAttribute(): ?int
    {
        return self::rataAspek([$this->nilai_tugas_1, $this->nilai_tugas_2, $this->nilai_tugas_3, $this->nilai_tugas_4]);
    }

    public function getRataUlanganHarianAttribute(): ?int
    {
        return self::rataAspek([$this->nilai_ulangan_harian_1, $this->nilai_ulangan_harian_2, $this->nilai_ulangan_harian_3]);
    }

    public function raport()
    {
        return $this->belongsTo(Raport::class, 'id_raport', 'id_raport');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mata_pelajaran', 'id_mata_pelajaran');
    }
}
