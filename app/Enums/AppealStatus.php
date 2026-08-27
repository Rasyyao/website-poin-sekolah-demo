<?php

namespace App\Enums;

enum AppealStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Review',
            self::Accepted => 'Diterima',
            self::Rejected => 'Ditolak',
        };
    }
}
