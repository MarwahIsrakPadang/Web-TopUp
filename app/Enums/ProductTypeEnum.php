<?php

namespace App\Enums;

enum ProductTypeEnum: string
{
    case Package = 'package';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Package => 'Paket',
            self::Manual => 'Manual',
        };
    }
}
