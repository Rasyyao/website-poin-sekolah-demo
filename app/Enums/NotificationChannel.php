<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case Whatsapp = 'whatsapp';
    case Email = 'email';
    case Push = 'push';

    public function label(): string
    {
        return match ($this) {
            self::Whatsapp => 'WhatsApp',
            self::Email => 'Email',
            self::Push => 'Push Notification',
        };
    }
}
