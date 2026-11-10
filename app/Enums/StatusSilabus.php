<?php

namespace App\Enums;

enum StatusSilabus: string
{
    case DRAFT = 'draft';
    case DIAJUKAN = 'diajukan';
    case REVISI = 'revisi';
    case DISETUJUI = 'disetujui';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::DIAJUKAN => 'Diajukan',
            self::REVISI => 'Revisi',
            self::DISETUJUI => 'Disetujui',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::DIAJUKAN => 'warning',
            self::REVISI => 'danger',
            self::DISETUJUI => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'fas fa-pen',
            self::DIAJUKAN => 'fas fa-paper-plane',
            self::REVISI => 'fas fa-rotate-left',
            self::DISETUJUI => 'fas fa-lock',
        };
    }

    public function isLocked(): bool
    {
        return in_array($this, [self::DIAJUKAN, self::DISETUJUI], true);
    }

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
