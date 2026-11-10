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

        $jumlahMapel = GuruAmpu::where('id_guru', $guruId)->count();
        $jumlahWali = Kelas::where('id_guru', $guruId)->count();
        $sessionMode = $request->session()->get('guru_mode');

        $currentMode = match (true) {
            $sessionMode === 'mata_pelajaran' && $jumlahMapel > 0 => $sessionMode,
            $sessionMode === 'wali_kelas' && $jumlahWali > 0 => $sessionMode,
            default => null,
        };

        if ($currentMode) {
            $defaultMode = $currentMode;
        } elseif ($jumlahMapel > 0 && $jumlahWali === 0) {
            $defaultMode = 'mata_pelajaran';
        } elseif ($jumlahWali > 0 && $jumlahMapel === 0) {
            $defaultMode = 'wali_kelas';
        } else {
            $defaultMode = null;
        }

        return view('guru.mode', [
            'guru' => Auth::guard('guru')->user(),
            'hasMapel' => $jumlahMapel > 0,
            'hasWali' => $jumlahWali > 0,
            'jumlahMapel' => $jumlahMapel,
            'jumlahWali' => $jumlahWali,
            'currentMode' => $currentMode,
            'defaultMode' => $defaultMode,
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
