<?php

namespace App\Enums;

enum JenisPasal: string
{
    case Umum = 'umum';
    case Lalin = 'lalin';
    case Kendaraan = 'kendaraan';

    public function payloadKey(): string
    {
        return match ($this) {
            self::Umum => 'pasalUmum',
            self::Lalin => 'pasalLalin',
            self::Kendaraan => 'pasalKendaraan',
        };
    }
}
