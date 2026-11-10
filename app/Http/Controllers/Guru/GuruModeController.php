<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruAmpu;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruModeController extends Controller
{
    public function show(Request $request)
    {
        $guruId = Auth::guard('guru')->id();

        return view('guru.mode', [
            'guru' => Auth::guard('guru')->user(),
            'hasMapel' => GuruAmpu::where('id_guru', $guruId)->exists(),
            'hasWali' => Kelas::where('id_guru', $guruId)->exists(),
        ]);
    }

    public function select(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:mata_pelajaran,wali_kelas'],
        ]);

        $guruId = Auth::guard('guru')->id();
        $allowed = $validated['mode'] === 'wali_kelas'
            ? Kelas::where('id_guru', $guruId)->exists()
            : GuruAmpu::where('id_guru', $guruId)->exists();

        if (! $allowed) {
            return back()->withErrors([
                'mode' => 'Mode tersebut belum tersedia untuk akun guru ini.',
            ])->withInput();
        }

        $request->session()->put('guru_mode', $validated['mode']);

        return redirect()->intended(route('guru.dashboard'));
    }
}
