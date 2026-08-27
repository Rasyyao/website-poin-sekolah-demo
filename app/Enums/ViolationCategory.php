<?php

namespace App\Enums;

enum ViolationCategory: string
{
    case Ringan = 'ringan';
    case Sedang = 'sedang';
    case Berat = 'berat';

    public function label(): string
    {
        return match ($this) {
            self::Ringan => 'Ringan',
            self::Sedang => 'Sedang',
            self::Berat => 'Berat',
        };
    }

    /**
     * Whether this category requires admin approval.
     */
    public function requiresApproval(): bool
    {
        return $this === self::Berat;
    }
}
