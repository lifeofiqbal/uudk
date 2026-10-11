<?php

namespace App\Models;

use App\Services\PenalCodeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KategoriPasal extends Model
{
    protected $table = 'kategori_pasal';

    protected $fillable = [
        'nama',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => PenalCodeService::flush());
        static::deleted(fn () => PenalCodeService::flush());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('id');
    }
}
