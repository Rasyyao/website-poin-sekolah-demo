<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Sent = 'sent';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Sent => 'Terkirim',
            self::Failed => 'Gagal',
        };
    }
}
