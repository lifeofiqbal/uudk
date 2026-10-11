# PENAL CODE SIXNINE (Laravel)

Kalkulator pasal, denda, dan masa tahanan SIXNINE berbasis Laravel 13.

## Kebutuhan Server

- PHP 8.3+ (ekstensi: mbstring, openssl, tokenizer, xml, ctype, fileinfo)
- Composer 2
- **Tidak butuh database**: data pasal dibaca langsung dari file JSON
- **Tidak butuh Node.js**: CSS sudah terkompilasi di `public/css/app.css`

## Instalasi Production

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan optimize
```

Atur `APP_URL` di `.env`, lalu arahkan document root web server ke folder `public/`.
Pastikan folder `storage/` dan `bootstrap/cache/` dapat ditulis oleh web server:

```bash
chmod -R 775 storage bootstrap/cache
```

## Mengubah Data Pasal

Edit file `resources/data/pasal.json`, simpan, lalu refresh halaman. Selesai.
Cache otomatis diperbarui setiap kali file berubah, tanpa perintah tambahan.

## Pengaturan (.env)

| Variabel | Default | Fungsi |
|---|---|---|
| `PENAL_MAX_PASAL` | 3 | Maksimal pasal yang bisa dipilih |
| `PENAL_DPO_MULTIPLIER` | 2 | Pengali denda & masa tahanan untuk tersangka DPO |
| `PENAL_DATA_PATH` | `resources/data/pasal.json` | Lokasi file data pasal |

Setelah mengubah `.env` di production jalankan `php artisan optimize` lagi.

## Endpoint

| URL | Keterangan |
|---|---|
| `/` | Halaman Penal Code |
| `/pasal.json` | Data pasal dalam format JSON |
| `/up` | Health check |

## Mengubah Tampilan (opsional)

Sumber CSS ada di `resources/css/app.css` (Tailwind CSS v4). Untuk mengompilasi ulang:

```bash
npm install
npm run build
```

## Testing

```bash
composer install
php artisan test
```
