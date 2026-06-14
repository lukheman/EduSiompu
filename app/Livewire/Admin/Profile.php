<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Profile - EduSiompu')]
class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public $avatar;
    public ?string $currentAvatar = null;

    public bool $showPasswordSection = false;
    public string $identifierName = 'Email';

    public function mount(): void
    {
        $user = Auth::guard('admin')->user();
        $this->name = $user->nama;
        $this->email = $user->email;
        $this->identifierName = 'Email';
        $this->currentAvatar = $user->avatar;
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admin,email,' . Auth::guard('admin')->id() . ',id_admin'],
        ];

        if ($this->showPasswordSection && $this->password) {
            $rules['current_password'] = ['required', 'current_password:admin'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    protected $messages = [
        'current_password.current_password' => 'Password saat ini tidak sesuai.',
        'avatar.image' => 'File harus berupa gambar.',
        'avatar.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    public function togglePasswordSection(): void
    {
        $this->showPasswordSection = !$this->showPasswordSection;
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation(['current_password', 'password', 'password_confirmation']);
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => ['image', 'max:2048'], // 2MB max
        ]);
    }

    public function uploadAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::guard('admin')->user();

        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        $path = $this->avatar->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        $this->currentAvatar = $path;
        $this->avatar = null;

        session()->flash('success', 'Foto profil berhasil diperbarui.');
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if (!in_array('avatar', $user->getFillable()) && !property_exists($user, 'avatar')) {
            return;
        }

        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        $this->currentAvatar = null;

        session()->flash('success', 'Foto profil berhasil dihapus.');
    }

    public function updateProfile(): void
    {
        $validated = $this->validate();

        $user = Auth::guard('admin')->user();
        $user->nama = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        session()->flash('success', 'Profile berhasil diperbarui.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::guard('admin')->user();
        $user->password = Hash::make($this->password);
        $user->save();

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->showPasswordSection = false;

        session()->flash('success', 'Password berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}
