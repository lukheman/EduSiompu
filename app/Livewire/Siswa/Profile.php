<?php

namespace App\Livewire\Siswa;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Profil Saya')]
class Profile extends Component
{
    public function render()
    {
        $siswa = Auth::guard('siswa')->user()->load('kelas');
        return view('livewire.siswa.profile', compact('siswa'));
    }
}
