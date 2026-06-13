<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;

use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Login - EduSiompu')]
class Login extends Component
{
    public string $role = 'siswa';

    #[Rule(['required'])]
    public string $identifier = '';

    #[Rule(['required'])]
    public string $password = '';

    public bool $remember = false;

    public function setRole(string $role)
    {
        $this->role = $role;
        $this->resetErrorBag();
        $this->identifier = '';
        $this->password = '';
    }

    public function submit()
    {
        $this->validate();

        $identifier = $this->identifier;
        $password = $this->password;
        $remember = $this->remember;

        if ($this->role === 'admin') {
            if (Auth::guard('admin')->attempt(['email' => $identifier, 'password' => $password], $remember)) {
                session()->regenerate();
                return redirect()->to(route('dashboard'));
            }
        } elseif ($this->role === 'guru') {
            if (Auth::guard('guru')->attempt(['nip' => $identifier, 'password' => $password], $remember)) {
                session()->regenerate();
                return redirect()->to(route('dashboard'));
            }
        } elseif ($this->role === 'siswa') {
            if (Auth::guard('siswa')->attempt(['nisn' => $identifier, 'password' => $password], $remember)) {
                session()->regenerate();
                return redirect()->to(route('dashboard'));
            }
        }

        $this->addError('identifier', 'Identitas atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layoutData(['type' => 'auth']);
    }
}
