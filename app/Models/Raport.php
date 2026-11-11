<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    use HasFactory;

    protected $table = 'raport';

    protected $primaryKey = 'id_raport';

    protected $fillable = [
        'id_siswa', 'id_tahun_ajaran', 'id_kelas', 'sakit', 'izin', 'alpa', 'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function nilaiRaport()
    {
        return $this->hasMany(NilaiRaport::class, 'id_raport', 'id_raport');
    }

    /**
     * Data grafik nilai per mata pelajaran.
     *
     * @return array<int, array{label: string, nilai: int, kkm: int}>
     */
    public function grafikNilai(): array
    {
        return $this->nilaiRaport
            ->filter(fn (NilaiRaport $nilai) => $nilai->nilai_raport !== null && $nilai->nilai_raport !== '')
            ->map(fn (NilaiRaport $nilai) => [
                'label' => $nilai->mataPelajaran?->nama_mapel ?? '-',
                'nilai' => (int) $nilai->nilai_raport,
                'kkm' => (int) ($nilai->mataPelajaran?->kkm ?? 75),
            ])
            ->values()
            ->all();
    }
}
