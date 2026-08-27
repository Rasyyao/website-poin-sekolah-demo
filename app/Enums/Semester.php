<?php

namespace App\Enums;

enum Semester: int
{
    case Ganjil = 1;
    case Genap = 2;

    public function label(): string
    {
        return match ($this) {
            self::Ganjil => 'Ganjil',
            self::Genap => 'Genap',
        };
    }
}
