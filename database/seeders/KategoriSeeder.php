<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Infrastruktur',
                'deskripsi' => 'Jalan, jembatan, drainase, dan infrastruktur desa.',
            ],
            [
                'nama' => 'Fasilitas Umum',
                'deskripsi' => 'Kerusakan fasilitas umum desa.',
            ],
            [
                'nama' => 'Keamanan',
                'deskripsi' => 'Gangguan ketertiban dan keamanan desa.',
            ],
            [
                'nama' => 'Lingkungan',
                'deskripsi' => 'Sampah, pencemaran, dan kondisi lingkungan.',
            ],
        ];

        foreach ($kategoris as $data) {
            Kategori::query()->updateOrCreate(
                ['nama' => $data['nama']],
                ['deskripsi' => $data['deskripsi']],
            );
        }
    }
}
