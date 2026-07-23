<?php

namespace Database\Seeders;

use App\Models\Apbdes;
use App\Models\KontakDesa;
use App\Models\LokasiDesa;
use App\Models\Lowongan;
use App\Models\Pengumuman;
use App\Models\Polling;
use App\Models\ProdukUmkm;
use Illuminate\Database\Seeder;

class SmartVillageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['Pembangunan', 500000000, 275000000], ['Pemberdayaan', 250000000, 150000000], ['Pelayanan Publik', 180000000, 90000000]] as [$kategori,$anggaran,$realisasi]) {
            Apbdes::updateOrCreate(['tahun' => now()->year, 'kategori' => $kategori], compact('anggaran', 'realisasi'));
        }
        foreach ([['Bhabinkamtibmas', 'Aipda Budi', '110'], ['Babinsa', 'Serka Rahmat', '081234567801'], ['Bidan Desa', 'Siti Aminah', '081234567802'], ['Ambulans Desa', 'Petugas Siaga', '081234567803']] as [$jabatan,$nama,$telepon]) {
            KontakDesa::updateOrCreate(['jabatan' => $jabatan], compact('nama', 'telepon'));
        }
        Pengumuman::updateOrCreate(['judul' => 'Jadwal Posyandu Bulanan'], ['jenis' => 'Posyandu', 'isi' => 'Posyandu dilaksanakan hari Sabtu pukul 08.00 di Balai Desa.', 'tanggal' => now(), 'aktif' => true]);
        ProdukUmkm::updateOrCreate(['nama_produk' => 'Keripik Singkong Desa'], ['nama_usaha' => 'UMKM Makmur', 'pemilik' => 'Ibu Sari', 'kategori' => 'Makanan', 'kontak' => '081234567804']);
        Lowongan::updateOrCreate(['judul' => 'Tenaga Administrasi BUMDes'], ['deskripsi' => 'Dibutuhkan warga desa yang menguasai komputer dasar.', 'kontak' => '081234567805', 'tanggal' => today()->addMonth()]);
        LokasiDesa::updateOrCreate(['nama' => 'Kantor Desa'], ['kategori' => 'Fasilitas Umum', 'latitude' => -6.2000000, 'longitude' => 106.8166660, 'deskripsi' => 'Pusat pelayanan administrasi desa.']);
        $poll = Polling::firstOrCreate(['judul' => 'Jadwal Gotong Royong'], ['deskripsi' => 'Pilih jadwal yang paling sesuai.', 'aktif' => true]);
        foreach (['Sabtu pagi', 'Minggu pagi'] as $opsi) {
            $poll->options()->firstOrCreate(compact('opsi'));
        }
    }
}
