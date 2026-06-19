<?php

namespace App\Livewire\OrangTua;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Orang Tua')]
class Dashboard extends Component
{
    public function render()
    {
        $orangTua = Auth::guard('orang_tua')->user();
        $anakList = $orangTua->siswa()->with('kelas')->get();

        return view('livewire.orang-tua.dashboard', [
            'orangTua' => $orangTua,
            'anakList' => $anakList,
        ]);
    }
}
