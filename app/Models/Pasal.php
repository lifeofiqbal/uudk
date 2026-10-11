<?php

namespace App\Models;

use App\Enums\JenisPasal;
use App\Services\PenalCodeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Pasal extends Model
{
    protected $table = 'pasal';

    protected $fillable = [
        'jenis',
        'kode',
        'nama',
        'deskripsi',
        'kategori',
        'bulan',
        'hari',
        'denda',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisPasal::class,
            'bulan' => 'integer',
            'hari' => 'integer',
            'denda' => 'integer',
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

    public function toPayload(): array
    {
        $data = [
            'kode' => $this->kode,
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'bulan' => $this->bulan,
            'denda' => $this->denda,
            'kategori' => $this->kategori,
        ];

        if ($this->jenis === JenisPasal::Kendaraan) {
            $data['hari'] = $this->hari;
        }

        return $data;
    }
}
