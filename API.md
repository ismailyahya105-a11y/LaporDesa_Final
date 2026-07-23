# REST API Lapor Desa

Base URL lokal: `http://127.0.0.1:8000/api` (jalankan `php artisan serve`). Seluruh endpoint selain login memakai header berikut:

```http
Accept: application/json
Authorization: Bearer {token}
```

## Postman

1. Buat environment dengan variabel `base_url` bernilai `http://127.0.0.1:8000/api` dan variabel `token` kosong.
2. Jalankan `php artisan migrate` lalu `php artisan db:seed --class=AdminSeeder` untuk membuat admin bawaan (`admin@lapordesa.test` / `password`, kecuali diubah lewat `.env`). Buat akun warga dari halaman registrasi web bila belum ada.
3. Kirim request login, lalu salin nilai `token` respons ke environment Postman.
4. Untuk endpoint terproteksi, pilih Authorization type **Bearer Token** dan isi `{{token}}`.
5. Khusus upload laporan, pilih Body **form-data**, lalu ubah tipe field `foto` menjadi **File**. Request lain dapat memakai Body **raw** dengan tipe JSON.

## Endpoints

### Login

`POST {{base_url}}/login`

```json
{
  "email": "warga@example.com",
  "password": "password"
}
```

```json
{
  "success": true,
  "token": "1|token-sanctum...",
  "user": {
    "id": 2,
    "name": "Budi",
    "email": "warga@example.com",
    "role": "masyarakat",
    "created_at": "2026-07-23T10:00:00.000000Z"
  }
}
```

### Logout dan profile

`POST {{base_url}}/logout`

```json
{
  "success": true,
  "message": "Logout berhasil."
}
```

`GET {{base_url}}/profile` mengembalikan `{ "success": true, "user": { ... } }` untuk user yang sedang login.

### Laporan

`GET {{base_url}}/laporan`

Warga menerima laporan miliknya; admin menerima semua laporan. Respons dipaginasi dalam `data.data`, disertai `data.links` dan `data.meta`.

`GET {{base_url}}/laporan/1`

Menampilkan judul, kategori, isi laporan, path dan URL foto, status, tanggal, pelapor, serta tanggapan.

`POST {{base_url}}/laporan` (khusus warga, **form-data**)

| Key | Nilai |
| --- | --- |
| `judul` | Jalan rusak RT 02 |
| `kategori_id` | 1 |
| `isi_laporan` | Ada lubang besar di jalan. |
| `foto` | file gambar, opsional, maksimal 2 MB |

Contoh respons laporan:

```json
{
  "success": true,
  "message": "Laporan berhasil dikirim.",
  "data": {
    "id": 1,
    "judul": "Jalan rusak RT 02",
    "kategori": { "id": 1, "nama": "Infrastruktur", "deskripsi": null },
    "isi_laporan": "Ada lubang besar di jalan.",
    "foto": "images/laporan/laporan_a1b2.jpg",
    "foto_url": "http://127.0.0.1:8000/images/laporan/laporan_a1b2.jpg",
    "status": "menunggu",
    "tanggal": "2026-07-23T10:00:00.000000Z",
    "pelapor": { "id": 2, "name": "Budi", "email": "warga@example.com", "role": "masyarakat", "created_at": "2026-07-23T09:00:00.000000Z" },
    "tanggapan": []
  }
}
```

File foto disimpan langsung di `public/images/laporan`; nilai yang tersimpan di database selalu berbentuk `images/laporan/nama_file.jpg`.

### Status laporan (khusus admin)

`PUT {{base_url}}/laporan/1/status`

```json
{ "status": "diproses" }
```

Nilai status yang valid: `menunggu`, `diproses`, dan `selesai`.

### Tanggapan (khusus admin)

`POST {{base_url}}/tanggapan`

```json
{
  "laporan_id": 1,
  "isi": "Petugas akan meninjau lokasi hari ini."
}
```

Respons `201` memuat detail laporan terbaru beserta tanggapannya.

## Status HTTP

- `200`: berhasil
- `201`: data berhasil dibuat
- `401`: token tidak ada atau tidak valid
- `403`: role atau kepemilikan tidak sesuai
- `422`: validasi gagal; detail tersedia di `errors`
