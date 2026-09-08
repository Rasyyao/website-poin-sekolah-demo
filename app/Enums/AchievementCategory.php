<?php

namespace App\Enums;

enum AchievementCategory: string
{
    case Akademik = 'akademik';
    case NonAkademik = 'non_akademik';
    case Kedisiplinan = 'kedisiplinan';
    case Organisasi = 'organisasi';
    case Sosial = 'sosial';

    public function label(): string
    {
        return match ($this) {
            self::Akademik => 'Akademik',
            self::NonAkademik => 'Non-Akademik',
            self::Kedisiplinan => 'Kedisiplinan & Karakter',
            self::Organisasi => 'Organisasi & Kepemimpinan',
            self::Sosial => 'Sosial & Lingkungan',
        };
    }

    public function requiresApproval(): bool
    {
        return false;
    }
}
