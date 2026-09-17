<?php

namespace App\Livewire\Guru;

use App\Models\GuruAmpu;
use App\Models\NilaiRaport;
use App\Models\Raport;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Input Nilai Raport')]
class InputNilai extends Component
{
    public $ampuOptions = [];

    public $selectedAmpuId = '';

    public $siswas = [];

    public $nilaiData = [];

    public function mount()
    {
        $id_guru = Auth::guard('guru')->id();
        $this->ampuOptions = GuruAmpu::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('id_guru', $id_guru)
            ->whereHas('tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->get()
            ->mapWithKeys(function ($ampu) {
                return [$ampu->id_guru_ampu => $ampu->kelas->nama_kelas.' - '.$ampu->mataPelajaran->nama_mapel.' ('.$ampu->tahunAjaran->nama_tahun.' '.ucfirst($ampu->tahunAjaran->semester).')'];
            })
            ->toArray();
    }

    public function updatedSelectedAmpuId()
    {
        $this->loadSiswas();
    }

    public function updatedNilaiData($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) !== 2 || end($parts) === 'raport') {
            return;
        }

        $this->hitungNilaiRaport($parts[0]);
    }

    private function nilaiKosong(): array
    {
        $kosong = ['ulangan_semester' => '', 'raport' => ''];

        foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
            foreach ($koloms as $kolom) {
                $kosong[substr($kolom, 6)] = '';
            }
        }

        return $kosong;
    }

    private function hitungNilaiRaport($idSiswa): void
    {
        $data = $this->nilaiData[$idSiswa] ?? [];
        $rataAspek = [];

        foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
            $scores = [];
            foreach ($koloms as $kolom) {
                $scores[] = $data[substr($kolom, 6)] ?? '';
            }
            $rata = NilaiRaport::rataAspek($scores);
            if ($rata !== null) {
                $rataAspek[] = $rata;
            }
        }

        $us = $data['ulangan_semester'] ?? '';
        if ($us !== '' && $us !== null && is_numeric($us)) {
            $rataAspek[] = (float) $us;
        }

        $this->nilaiData[$idSiswa]['raport'] = count($rataAspek) > 0 ? (int) round(array_sum($rataAspek) / count($rataAspek)) : '';
    }

    public function rataAspekForm($idSiswa, array $fields): ?int
    {
        $data = $this->nilaiData[$idSiswa] ?? [];
        $scores = [];
        foreach ($fields as $field) {
            $scores[] = $data[$field] ?? '';
        }

        return NilaiRaport::rataAspek($scores);
    }

    public function ampuValid(): bool
    {
        return $this->ampuMilikGuru() !== null;
    }

    private function ampuMilikGuru(): ?GuruAmpu
    {
        if (! $this->selectedAmpuId) {
            return null;
        }

        return GuruAmpu::where('id_guru_ampu', $this->selectedAmpuId)
            ->where('id_guru', Auth::guard('guru')->id())
            ->whereHas('tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->first();
    }

    public function loadSiswas()
    {
        if (! $this->selectedAmpuId) {
            $this->siswas = [];
            $this->nilaiData = [];

            return;
        }

        $ampu = $this->ampuMilikGuru();
        if (! $ampu) {
            $this->siswas = [];
            $this->nilaiData = [];

            return;
        }

        $this->siswas = Siswa::where('id_kelas', $ampu->id_kelas)->get();

        $this->nilaiData = [];
        foreach ($this->siswas as $siswa) {
            $raport = Raport::where('id_siswa', $siswa->id_siswa)
                ->where('id_tahun_ajaran', $ampu->id_tahun_ajaran)
                ->first();

            if ($raport) {
                $nilai = NilaiRaport::where('id_raport', $raport->id_raport)
                    ->where('id_mata_pelajaran', $ampu->id_mata_pelajaran)
                    ->first();

                $row = [];
                foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
                    foreach ($koloms as $kolom) {
                        $row[substr($kolom, 6)] = $nilai->{$kolom} ?? '';
                    }
                }
                $row['ulangan_semester'] = $nilai->nilai_ulangan_semester ?? '';
                $row['raport'] = $nilai->nilai_raport ?? '';
                $this->nilaiData[$siswa->id_siswa] = $row;
            } else {
                $this->nilaiData[$siswa->id_siswa] = $this->nilaiKosong();
            }
        }
    }

    public function getPredikat($nilai)
    {
        if ($nilai === '' || $nilai === null) {
            return null;
        }
        if ($nilai >= 90) {
            return 'A';
        }
        if ($nilai >= 80) {
            return 'B';
        }
        if ($nilai >= 70) {
            return 'C';
        }

        return 'D';
    }

    public function simpan()
    {
        if (! $this->selectedAmpuId) {
            return;
        }

        $ampu = $this->ampuMilikGuru();
        if (! $ampu) {
            return;
        }

        foreach ($this->siswas as $siswa) {
            $data = array_merge($this->nilaiKosong(), $this->nilaiData[$siswa->id_siswa] ?? []);

            foreach ($data as $field => $nilai) {
                $data[$field] = ($nilai === '') ? null : $nilai;
            }
            $this->nilaiData[$siswa->id_siswa] = $data;

            if ($data['raport'] === null) {
                $this->hitungNilaiRaport($siswa->id_siswa);
                $otomatis = $this->nilaiData[$siswa->id_siswa]['raport'] ?? '';
                $data['raport'] = ($otomatis === '') ? null : $otomatis;
            }

            if (count(array_filter($data, fn ($v) => $v !== null)) > 0) {
                $raport = Raport::firstOrCreate(
                    ['id_siswa' => $siswa->id_siswa, 'id_tahun_ajaran' => $ampu->id_tahun_ajaran],
                    ['id_kelas' => $ampu->id_kelas]
                );

                $payload = [];
                foreach (NilaiRaport::ASPEK_SCORES as $koloms) {
                    foreach ($koloms as $kolom) {
                        $payload[$kolom] = $data[substr($kolom, 6)];
                    }
                }

                $rataAfektif = NilaiRaport::rataAspek([$payload['nilai_afektif_1'], $payload['nilai_afektif_2'], $payload['nilai_afektif_3']]);
                $rataPsikomotor = NilaiRaport::rataAspek([$payload['nilai_psikomotor_1'], $payload['nilai_psikomotor_2'], $payload['nilai_psikomotor_3'], $payload['nilai_psikomotor_4']]);

                $payload['predikat_afektif'] = $this->getPredikat($rataAfektif);
                $payload['predikat_psikomotor'] = $this->getPredikat($rataPsikomotor);
                $payload['nilai_ulangan_semester'] = $data['ulangan_semester'];
                $payload['nilai_raport'] = $data['raport'];
                $payload['predikat_raport'] = $this->getPredikat($data['raport']);

                NilaiRaport::updateOrCreate(
                    ['id_raport' => $raport->id_raport, 'id_mata_pelajaran' => $ampu->id_mata_pelajaran],
                    $payload
                );
            }
        }

        session()->flash('message', 'Nilai berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.guru.input-nilai');
    }
}
