# Deployment production — Lapor Desa

## Konfigurasi server

- Gunakan PHP 8.2+ beserta ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, dan `ctype`.
- Arahkan document root domain ke folder `public/`, bukan root proyek.
- Jangan unggah `.env` dari komputer pengembang. Buat `.env` di server dari `.env.example`, isi kredensial database server, lalu jalankan `php artisan key:generate --force` bila `APP_KEY` belum ada.
- Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-anda.tld`, serta kredensial MySQL yang diberikan hosting. Jangan commit atau kirimkan file `.env`.
- Pastikan `storage/` dan `bootstrap/cache/` dapat ditulis oleh pengguna web server. Jangan beri izin tulis ke seluruh proyek.

## Foto dan storage

- Foto **laporan**, UMKM, dan dokumen surat disimpan pada disk `public` di `storage/app/public`. Aplikasi menyajikan foto laporan dan UMKM melalui controller, sehingga tidak bergantung pada symbolic link untuk menampilkan foto.
- Pada Railway, tambahkan **Volume** dan mount ke `/app/storage/app/public`. Tanpa volume, seluruh upload akan hilang setiap kali service di-redeploy atau restart.
- Sertakan data upload yang sudah ada saat migrasi (`storage/app/public`). Deploy tanpa folder atau volume ini membuat foto lama tidak tersedia.

## API dan keamanan

- API menggunakan token Sanctum (`Authorization: Bearer <token>`); rute selain `POST /api/login` dan `GET /api/kategori` berada di balik `auth:sanctum`.
- `CORS_ALLOWED_ORIGINS` dikosongkan secara aman secara default. Isi hanya jika ada frontend browser lintas domain, contoh `https://app.domain-anda.tld`. Jangan gunakan `*` untuk API yang kelak memakai cookie/session.
- Untuk SPA Sanctum berbasis cookie (bila benar-benar dipakai), isi `SANCTUM_STATEFUL_DOMAINS=app.domain-anda.tld` dan ubah `supports_credentials` pada `config/cors.php` menjadi `true`; native Android yang memakai token tidak memerlukannya.

## Perintah deploy

Jalankan dari root proyek setelah `.env` final tersedia:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`npm run build` wajib dijalankan pada setiap deploy yang mengubah frontend. Perintah ini membuat file CSS/JS ber-hash di `public/build` beserta `manifest.json` yang dipakai directive `@vite`. Jika build dilakukan di mesin lokal lalu folder proyek diunggah manual, unggah juga seluruh `public/build` (termasuk `manifest.json` dan `assets/`).

Jangan menjalankan `migrate`, `migrate:fresh`, atau seeder sebagai bagian dari deploy ini karena tidak ada perubahan database yang diperlukan.

## Checklist verifikasi

- [ ] HTTPS aktif dan `APP_URL` menggunakan URL final tanpa slash di akhir.
- [ ] `APP_ENV=production` dan `APP_DEBUG=false` sudah diverifikasi di server.
- [ ] Kredensial database di `.env` benar dan file tidak dapat diakses publik.
- [ ] `storage/` serta `bootstrap/cache/` writable; folder upload laporan juga writable.
- [ ] Cache konfigurasi, rute, dan view sudah dibangun setelah seluruh nilai `.env` final.
- [ ] Login API, token Bearer, dan endpoint terlindungi diuji melalui HTTPS.
- [ ] Unggah lalu buka satu foto laporan, foto UMKM, dan dokumen surat.
- [ ] Log tidak berisi stack trace atau kredensial; debug tetap mati.
- [ ] Backup database dan dua lokasi upload telah dibuat sebelum deploy.
