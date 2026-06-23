<?php

namespace App\Livewire\Admin;

use App\Models\JadwalPelajaran;
use App\Models\GuruAmpu;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Manajemen Jadwal Pelajaran')]
class JadwalPelajaranManagement extends Component
{
    #[Url]
    public ?int $filter_tahun_ajaran = null;

    // View State
    public ?int $selectedKelasId = null;

    // Form Fields
    public ?int $id_tahun_ajaran = null;
    public ?int $id_kelas = null;
    public ?int $id_mata_pelajaran = null;
    public ?int $id_guru = null;
    
    public string $hari = '';
    public string $jam_mulai = '';
    public string $jam_selesai = '';

    // Modal State
    public ?int $editingId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    // Search inside modal
    public string $searchMapelModal = '';
    public string $searchGuruModal = '';

    public function selectMapel($id, $name)
    {
        $this->id_mata_pelajaran = $id;
        $this->searchMapelModal = $name;
    }

    public function selectGuru($id, $name)
    {
        $this->id_guru = $id;
        $this->searchGuruModal = $name;
    }

    public function updatedSearchMapelModal()
    {
        if (strlen($this->searchMapelModal) < 3) {
            $this->id_mata_pelajaran = null;
        }
    }

    public function updatedSearchGuruModal()
    {
        if (strlen($this->searchGuruModal) < 3) {
            $this->id_guru = null;
        }
    }

    public function mount()
    {
        $activeTa = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTa && !$this->filter_tahun_ajaran) {
            $this->filter_tahun_ajaran = $activeTa->id_tahun_ajaran;
        }
    }

    public function selectKelas(int $id)
    {
        $this->selectedKelasId = $id;
    }

    public function backToKelas()
    {
        $this->selectedKelasId = null;
    }

    protected function rules(): array
    {
        return [
            'id_tahun_ajaran' => ['required', 'exists:tahun_ajaran,id_tahun_ajaran'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_mata_pelajaran' => ['required', 'exists:mata_pelajaran,id_mata_pelajaran'],
            'id_guru' => ['required', 'exists:guru,id_guru'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ];
    }

    public function openModal(string $hari = '')
    {
        $this->resetForm();
        
        if ($this->filter_tahun_ajaran) {
            $this->id_tahun_ajaran = $this->filter_tahun_ajaran;
        }
        if ($this->selectedKelasId) {
            $this->id_kelas = $this->selectedKelasId;
        }
        if ($hari) {
            $this->hari = $hari;
        }

        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $jadwal = JadwalPelajaran::with('guruAmpu')->findOrFail($id);
        if ($jadwal) {
            $this->editingId = $jadwal->id_jadwal_pelajaran;
            $this->id_tahun_ajaran = $jadwal->guruAmpu->id_tahun_ajaran;
            $this->id_kelas = $jadwal->guruAmpu->id_kelas;
            $this->id_mata_pelajaran = $jadwal->guruAmpu->id_mata_pelajaran;
            $this->id_guru = $jadwal->guruAmpu->id_guru;
            $this->hari = $jadwal->hari;
            $this->jam_mulai = \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i');
            $this->jam_selesai = \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i');
            
            // Set search input values
            $mapel = \App\Models\MataPelajaran::find($this->id_mata_pelajaran);
            if($mapel) $this->searchMapelModal = '[' . $mapel->kode_mapel . '] ' . $mapel->nama_mapel;
            
            $guru = \App\Models\Guru::find($this->id_guru);
            if($guru) $this->searchGuruModal = $guru->nama_guru;

            $this->showModal = true;
        }
    }

    public function save()
    {
        $this->validate();

        // Cek Bentrok Kelas
        $overlapKelas = JadwalPelajaran::whereHas('guruAmpu', function($q) {
            $q->where('id_tahun_ajaran', $this->id_tahun_ajaran)
              ->where('id_kelas', $this->id_kelas);
        })
        ->where('hari', $this->hari)
        ->where('jam_mulai', '<', $this->jam_selesai)
        ->where('jam_selesai', '>', $this->jam_mulai);

        if ($this->editingId) {
            $overlapKelas->where('id_jadwal_pelajaran', '!=', $this->editingId);
        }

        if ($overlapKelas->exists()) {
            $this->addError('jam_mulai', 'Jadwal bentrok! Kelas ini sudah ada mata pelajaran lain pada rentang waktu tersebut.');
            return;
        }

        // Cek Bentrok Guru
        $overlapGuru = JadwalPelajaran::whereHas('guruAmpu', function($q) {
            $q->where('id_tahun_ajaran', $this->id_tahun_ajaran)
              ->where('id_guru', $this->id_guru);
        })
        ->where('hari', $this->hari)
        ->where('jam_mulai', '<', $this->jam_selesai)
        ->where('jam_selesai', '>', $this->jam_mulai);

        if ($this->editingId) {
            $overlapGuru->where('id_jadwal_pelajaran', '!=', $this->editingId);
        }

        if ($overlapGuru->exists()) {
            $this->addError('id_guru', 'Jadwal bentrok! Guru ini sudah memiliki jadwal mengajar di kelas lain pada waktu tersebut.');
            return;
        }

        $guruAmpu = GuruAmpu::firstOrCreate(
            [
                'id_tahun_ajaran' => $this->id_tahun_ajaran,
                'id_kelas' => $this->id_kelas,
                'id_mata_pelajaran' => $this->id_mata_pelajaran,
            ],
            [
                'id_guru' => $this->id_guru
            ]
        );

        if ($guruAmpu->id_guru !== $this->id_guru) {
            $guruAmpu->update(['id_guru' => $this->id_guru]);
        }

        $jadwalData = [
            'id_guru_ampu' => $guruAmpu->id_guru_ampu,
            'hari' => $this->hari,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai
        ];

        if ($this->editingId) {
            JadwalPelajaran::findOrFail($this->editingId)->update($jadwalData);
            session()->flash('success', 'Jadwal pelajaran berhasil diperbarui.');
        } else {
            JadwalPelajaran::create($jadwalData);
            session()->flash('success', 'Jadwal pelajaran berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
        $this->searchMapelModal = '';
        $this->searchGuruModal = '';
    }

    protected function resetForm()
    {
        $this->id_tahun_ajaran = null;
        $this->id_kelas = null;
        $this->id_mata_pelajaran = null;
        $this->id_guru = null;
        
        $this->hari = '';
        $this->jam_mulai = '';
        $this->jam_selesai = '';
        $this->editingId = null;
    }

    public function confirmDelete(int $id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingId) {
            JadwalPelajaran::destroy($this->deletingId);
            session()->flash('success', 'Jadwal pelajaran berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun', 'desc')->get();
        
        $jadwals = collect();
        $selectedKelasName = '';
        
        if ($this->selectedKelasId) {
            $selectedKelas = Kelas::find($this->selectedKelasId);
            $selectedKelasName = $selectedKelas ? $selectedKelas->nama_kelas : '';
            
            $query = JadwalPelajaran::with(['guruAmpu.guru', 'guruAmpu.kelas', 'guruAmpu.mataPelajaran'])
                ->whereHas('guruAmpu', function($q) {
                    $q->where('id_kelas', $this->selectedKelasId);
                    if ($this->filter_tahun_ajaran) {
                        $q->where('id_tahun_ajaran', $this->filter_tahun_ajaran);
                    }
                })
                ->orderBy('jam_mulai')
                ->get();
                
            // Kelompokkan berdasarkan hari
            $jadwals = $query->groupBy('hari');
        }

        return view('livewire.admin.jadwal-pelajaran-management', [
            'kelasList' => $kelasList,
            'tahunAjarans' => $tahunAjarans,
            'jadwalsByHari' => $jadwals,
            'selectedKelasName' => $selectedKelasName,
            'mapels' => MataPelajaran::orderBy('nama_mapel')->get(),
            'gurus' => Guru::orderBy('nama_guru')->get(),
            'daftarHari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
        ]);
    }
}
