<?php

namespace App\Enums;

enum StatusKehadiran: string
{
    case HADIR = 'hadir';
    case SAKIT = 'sakit';
    case IZIN = 'izin';
    case ALPA = 'alpa';

    public function getLabel(): string
    {
        return match ($this) {
            self::HADIR => 'Hadir',
            self::SAKIT => 'Sakit',
            self::IZIN => 'Izin',
            self::ALPA => 'Alpa',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::HADIR => 'success',
            self::SAKIT => 'warning',
            self::IZIN => 'info',
            self::ALPA => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::HADIR => 'fas fa-check-circle',
            self::SAKIT => 'fas fa-briefcase-medical',
            self::IZIN => 'fas fa-envelope-open-text',
            self::ALPA => 'fas fa-times-circle',
        };
    }

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
