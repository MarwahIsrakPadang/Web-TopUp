<?php

namespace App\Enums;

enum FeeTypeEnum: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Tetap',
            self::Percentage => 'Persentase',
        };
    }
}
