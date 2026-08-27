<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Trial = 'trial';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Trial => 'Trial',
            self::Expired => 'Kadaluarsa',
        };
    }

    public function isAccessible(): bool
    {
        return in_array($this, [self::Active, self::Trial]);
    }
}
