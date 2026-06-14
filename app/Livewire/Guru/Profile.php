<?php

namespace App\Livewire\Guru;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profil Guru')]
class Profile extends Component
{
    public string $nama_guru = '';
    public string $nip = '';
    
    public bool $showPasswordSection = false;
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $guru = Auth::guard('guru')->user();
        if ($guru) {
            $this->nama_guru = $guru->nama_guru;
            $this->nip = $guru->nip;
        }
    }

    protected function rules(): array
    {
        $guruId = Auth::guard('guru')->id();
        
        $rules = [
            'nama_guru' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:255', 'unique:guru,nip,' . $guruId . ',id_guru'],
        ];

        if ($this->showPasswordSection && $this->password) {
            $rules['current_password'] = ['required', 'current_password:guru'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    protected $messages = [
        'current_password.current_password' => 'Password saat ini tidak sesuai.',
    ];

    public function togglePasswordSection(): void
    {
        $this->showPasswordSection = !$this->showPasswordSection;
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation(['current_password', 'password', 'password_confirmation']);
    }

    public function updateProfile(): void
    {
        $validated = $this->validate([
            'nama_guru' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:255', 'unique:guru,nip,' . Auth::guard('guru')->id() . ',id_guru'],
        ]);

        $guru = Auth::guard('guru')->user();
        $guru->nama_guru = $validated['nama_guru'];
        $guru->nip = $validated['nip'];
        $guru->save();

        session()->flash('success_profile', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password:guru'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $guru = Auth::guard('guru')->user();
        $guru->password = Hash::make($this->password);
        $guru->save();

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->showPasswordSection = false;

        session()->flash('success_password', 'Kata sandi berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.guru.profile');
    }
}
