<?php

namespace App\Enums;

enum VoucherTypeEnum: string
{
    case Percentage = 'percentage';
    case Nominal = 'nominal';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Persentase',
            self::Nominal => 'Nominal',
        };
    }
}
