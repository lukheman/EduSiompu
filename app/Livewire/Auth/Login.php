<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Login - EduSiompu')]
class Login extends Component
{
    public string $role = '';

    public string $identifier = '';

    public string $password = '';

    public bool $remember = false;

    public function getRoleOptionsProperty(): array
    {
        return [
            'siswa' => 'Siswa',
            'guru' => 'Guru',
            'admin' => 'Admin',
            'orang_tua' => 'Orang Tua',
        ];
    }

    public function submit()
    {
        $this->validate([
            'role' => ['required', 'in:siswa,guru,admin,orang_tua'],
            'identifier' => ['required'],
            'password' => ['required'],
        ], [
            'role.required' => 'Silakan pilih role terlebih dahulu.',
            'identifier.required' => 'Identitas wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if ($this->role === 'admin') {
            if (Auth::guard('admin')->attempt(['email' => $this->identifier, 'password' => $this->password], $this->remember)) {
                session()->regenerate();
                return redirect()->to(route('admin.dashboard'));
            }
        } elseif ($this->role === 'guru') {
            if (Auth::guard('guru')->attempt(['nip' => $this->identifier, 'password' => $this->password], $this->remember)) {
                session()->regenerate();
                return redirect()->to(route('guru.dashboard'));
            }
        } elseif ($this->role === 'siswa') {
            if (Auth::guard('siswa')->attempt(['nisn' => $this->identifier, 'password' => $this->password], $this->remember)) {
                session()->regenerate();
                return redirect()->to(route('siswa.dashboard'));
            }
        } elseif ($this->role === 'orang_tua') {
            if (Auth::guard('orang_tua')->attempt(['nik' => $this->identifier, 'password' => $this->password], $this->remember)) {
                session()->regenerate();
                return redirect()->intended(route('orang-tua.dashboard'));
            }
        }

        $this->addError('identifier', 'Identitas atau kata sandi salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layoutData(['type' => 'auth']);
    }
}
