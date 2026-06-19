<?php

namespace App\Livewire\Admin;

use App\Models\OrangTua;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule as ValidationRule;

#[Layout('layouts.app')]
#[Title('Manajemen Orang Tua')]
class OrangTuaManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditing = false;
    public $deleteId = null;
    public $editingId = null;

    #[Rule('required|string|max:255')]
    public $nama_orang_tua = '';

    public $nik = '';
    public $password = '';
    public $no_hp = '';

    public function rules()
    {
        return [
            'nik' => ['required', 'string', 'min:16', 'max:16', ValidationRule::unique('orang_tua', 'nik')->ignore($this->editingId, 'id_orang_tua')],
            'password' => $this->isEditing ? 'nullable|min:6' : 'required|min:6',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['nama_orang_tua', 'nik', 'password', 'no_hp', 'isEditing', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function edit($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $this->editingId = $orangTua->id_orang_tua;
        $this->nama_orang_tua = $orangTua->nama_orang_tua;
        $this->nik = $orangTua->nik;
        $this->no_hp = $orangTua->no_hp;
        $this->isEditing = true;
        
        $this->resetValidation();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'nama_orang_tua' => $this->nama_orang_tua,
            'nik' => $this->nik,
            'no_hp' => $this->no_hp,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            OrangTua::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Data orang tua berhasil diperbarui.');
        } else {
            OrangTua::create($data);
            session()->flash('success', 'Data orang tua berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            OrangTua::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Data orang tua berhasil dihapus.');
            $this->showDeleteModal = false;
        }
    }

    public function render()
    {
        $orangTuas = OrangTua::withCount('siswa')
            ->where('nama_orang_tua', 'like', '%' . $this->search . '%')
            ->orWhere('nik', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.admin.orang-tua-management', [
            'orangTuas' => $orangTuas
        ]);
    }
}
