<?php

namespace App\Enums;

enum RuleType: string
{
    case Violation = 'violation';
    case Achievement = 'achievement';

    public function label(): string
    {
        return match ($this) {
            self::Violation => 'Pelanggaran',
            self::Achievement => 'Prestasi',
        };
    }   
}
