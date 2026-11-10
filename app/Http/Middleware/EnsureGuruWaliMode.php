<?php

namespace App\Http\Middleware;

use App\Models\Kelas;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuruWaliMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('guru_mode') !== 'wali_kelas') {
            abort(403, 'Fitur ini hanya tersedia untuk mode Wali Kelas.');
        }

        if (! Kelas::where('id_guru', auth('guru')->id())->exists()) {
            abort(403, 'Guru belum ditugaskan sebagai wali kelas.');
        }

        return $next($request);
    }
}
