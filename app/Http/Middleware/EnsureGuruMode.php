<?php

namespace App\Http\Middleware;

use App\Models\GuruAmpu;
use App\Models\Kelas;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuruMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $mode = $request->session()->get('guru_mode');
        $guruId = auth('guru')->id();

        if (! $guruId || ! in_array($mode, ['mata_pelajaran', 'wali_kelas'], true)) {
            return redirect()->route('guru.mode');
        }

        $hasAccess = $mode === 'wali_kelas'
            ? Kelas::where('id_guru', $guruId)->exists()
            : GuruAmpu::where('id_guru', $guruId)->exists();

        if (! $hasAccess) {
            $request->session()->forget('guru_mode');

            return redirect()->route('guru.mode')->withErrors([
                'mode' => 'Mode yang dipilih tidak tersedia untuk akun guru ini.',
            ]);
        }

        return $next($request);
    }
}
