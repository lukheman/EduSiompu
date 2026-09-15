<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait HasAvatar
{
    public function getAvatarUrlAttribute(): string
    {
        $avatar = $this->attributes['avatar'] ?? null;

        if ($avatar && Storage::exists($avatar)) {
            return Storage::url($avatar);
        }

        return asset('images/default-avatar.svg');
    }
}
