<?php

namespace App\Services;

use App\Enums\JenisPasal;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class PenalCodeService
{
    public function payload(): array
    {
        $path = $this->path();

        if (! is_file($path)) {
            throw new RuntimeException("File data pasal tidak ditemukan: {$path}");
        }

        $key = config('penal.cache_key').'.'.filemtime($path).'.'.filesize($path);

        return Cache::rememberForever($key, fn () => $this->build($path));
    }

    public function path(): string
    {
        return (string) config('penal.data_path');
    }

    private function build(string $path): array
    {
        $json = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $payload = [
            'kategoriUmumOrder' => array_values(array_map('strval', $json['kategoriUmumOrder'] ?? [])),
        ];

        foreach (JenisPasal::cases() as $jenis) {
            $payload[$jenis->payloadKey()] = array_values(array_map(
                fn (array $item) => $this->normalize($item, $jenis),
                $json[$jenis->payloadKey()] ?? [],
            ));
        }

        return $payload;
    }

    private function normalize(array $item, JenisPasal $jenis): array
    {
        $data = [
            'kode' => (string) ($item['kode'] ?? ''),
            'nama' => (string) ($item['nama'] ?? ''),
            'deskripsi' => isset($item['deskripsi']) ? (string) $item['deskripsi'] : null,
            'bulan' => (int) ($item['bulan'] ?? 0),
            'denda' => (int) ($item['denda'] ?? 0),
            'kategori' => (string) ($item['kategori'] ?? 'KETERTIBAN UMUM'),
        ];

        if ($jenis === JenisPasal::Kendaraan) {
            $data['hari'] = (int) ($item['hari'] ?? 0);
        }

        return $data;
    }
}
