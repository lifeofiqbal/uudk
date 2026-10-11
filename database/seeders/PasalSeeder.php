<?php

namespace Database\Seeders;

use App\Enums\JenisPasal;
use App\Models\KategoriPasal;
use App\Models\Pasal;
use App\Services\PenalCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class PasalSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/pasal.json');

        if (! File::exists($path)) {
            throw new RuntimeException("File data tidak ditemukan: {$path}");
        }

        $data = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        $now = now();

        DB::transaction(function () use ($data, $now) {
            $kategori = collect($data['kategoriUmumOrder'] ?? [])
                ->values()
                ->map(fn (string $nama, int $index) => [
                    'nama' => $nama,
                    'urutan' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all();

            KategoriPasal::query()->whereNotIn('nama', array_column($kategori, 'nama'))->delete();
            KategoriPasal::query()->upsert($kategori, ['nama'], ['urutan', 'updated_at']);

            $rows = [];
            $urutan = 0;

            foreach (JenisPasal::cases() as $jenis) {
                foreach ($data[$jenis->payloadKey()] ?? [] as $item) {
                    $rows[] = [
                        'jenis' => $jenis->value,
                        'kode' => (string) $item['kode'],
                        'nama' => (string) $item['nama'],
                        'deskripsi' => $item['deskripsi'] ?? null,
                        'kategori' => (string) ($item['kategori'] ?? 'KETERTIBAN UMUM'),
                        'bulan' => (int) ($item['bulan'] ?? 0),
                        'hari' => (int) ($item['hari'] ?? 0),
                        'denda' => (int) ($item['denda'] ?? 0),
                        'urutan' => ++$urutan,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            Pasal::query()->whereNotIn('kode', array_column($rows, 'kode'))->delete();

            foreach (array_chunk($rows, 100) as $chunk) {
                Pasal::query()->upsert(
                    $chunk,
                    ['kode'],
                    ['jenis', 'nama', 'deskripsi', 'kategori', 'bulan', 'hari', 'denda', 'urutan', 'updated_at'],
                );
            }
        });

        PenalCodeService::flush();
    }
}
