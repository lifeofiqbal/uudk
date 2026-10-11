<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_halaman_utama_menampilkan_penal_code(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('PENAL CODE')
            ->assertSee('penal-data', false)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_endpoint_json_mengembalikan_struktur_data(): void
    {
        $this->getJson('/pasal.json')
            ->assertOk()
            ->assertJsonStructure([
                'kategoriUmumOrder',
                'pasalUmum' => [['kode', 'nama', 'deskripsi', 'bulan', 'denda', 'kategori']],
                'pasalLalin',
                'pasalKendaraan' => [['kode', 'nama', 'hari', 'denda']],
            ])
            ->assertJsonCount(102, 'pasalUmum')
            ->assertJsonCount(16, 'pasalLalin')
            ->assertJsonCount(15, 'pasalKendaraan')
            ->assertJsonPath('pasalUmum.0.kode', '1.1');
    }
}
