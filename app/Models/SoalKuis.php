<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalKuis extends Model
{
    use HasFactory;

    protected $table = 'soal_kuis';

    protected $primaryKey = 'id_soal';

    protected $fillable = [
        'id_kuis', 'pertanyaan', 'pilihan_a', 'pilihan_b',
        'pilihan_c', 'pilihan_d', 'jawaban_benar', 'poin',
    ];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id_kuis');
    }

    public function pilihan(): array
    {
        return [
            'A' => $this->pilihan_a,
            'B' => $this->pilihan_b,
            'C' => $this->pilihan_c,
            'D' => $this->pilihan_d,
        ];
    }
}
