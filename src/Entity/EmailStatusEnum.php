<?php

namespace App\Entity;

enum EmailStatusEnum: string
{
    case SENT = 'sent';
    case FAILED = 'failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
