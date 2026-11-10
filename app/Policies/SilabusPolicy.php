<?php

namespace App\Policies;

use App\Enums\StatusSilabus;
use App\Models\Guru;
use App\Models\KepalaSekolah;
use App\Models\Silabus;
use App\Models\Siswa;

class SilabusPolicy
{
    public function view($user, Silabus $silabus): bool
    {
        if ($user instanceof Guru) {
            return $silabus->guruAmpu?->id_guru === $user->id_guru;
        }

        if ($user instanceof KepalaSekolah) {
            return true;
        }

        if ($user instanceof Siswa) {
            return $silabus->status === StatusSilabus::DISETUJUI
                && $silabus->guruAmpu?->id_kelas === $user->id_kelas;
        }

        return false;
    }

    public function update($user, Silabus $silabus): bool
    {
        return $user instanceof Guru
            && $silabus->guruAmpu?->id_guru === $user->id_guru
            && $silabus->bisaDiedit();
    }

    public function submit($user, Silabus $silabus): bool
    {
        return $this->update($user, $silabus);
    }

    public function review($user, Silabus $silabus): bool
    {
        return $user instanceof KepalaSekolah
            && $silabus->status === StatusSilabus::DIAJUKAN;
    }

    public function unlock($user, Silabus $silabus): bool
    {
        return $user instanceof KepalaSekolah
            && $silabus->status === StatusSilabus::DISETUJUI;
    }
}
