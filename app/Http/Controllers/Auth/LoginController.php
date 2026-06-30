<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login')->with('type', 'auth');
    }

    public function login(Request $request)
    {
        $request->validate([
            'role' => ['required', 'in:siswa,guru,admin,orang_tua'],
            'identifier' => ['required'],
            'password' => ['required'],
        ], [
            'role.required' => 'Silakan pilih role terlebih dahulu.',
            'identifier.required' => 'Identitas wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $role = $request->role;
        $identifier = $request->identifier;
        $password = $request->password;

        if ($role === 'admin') {
            if (Auth::guard('admin')->attempt(['email' => $identifier, 'password' => $password])) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        } elseif ($role === 'guru') {
            if (Auth::guard('guru')->attempt(['nip' => $identifier, 'password' => $password])) {
                $request->session()->regenerate();
                return redirect()->intended(route('guru.dashboard'));
            }
        } elseif ($role === 'siswa') {
            if (Auth::guard('siswa')->attempt(['nisn' => $identifier, 'password' => $password])) {
                $request->session()->regenerate();
                return redirect()->intended(route('siswa.dashboard'));
            }
        } elseif ($role === 'orang_tua') {
            if (Auth::guard('orang_tua')->attempt(['nik' => $identifier, 'password' => $password])) {
                $request->session()->regenerate();
                return redirect()->intended(route('orang-tua.dashboard'));
            }
        }

        return back()->withErrors([
            'identifier' => 'Identitas atau kata sandi salah.',
        ])->withInput($request->only('identifier', 'role'));
    }
}
