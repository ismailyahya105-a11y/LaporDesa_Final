<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_surat');
            $table->json('data_pengajuan');
            $table->string('dokumen')->nullable();
            $table->enum('status', ['diajukan', 'diproses', 'verifikasi', 'disetujui', 'siap_diambil', 'selesai', 'ditolak'])->default('diajukan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
        Schema::create('apbdes', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->string('kategori');
            $table->decimal('anggaran', 15, 2);
            $table->decimal('realisasi', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('jenis');
            $table->string('judul');
            $table->text('isi');
            $table->dateTime('tanggal')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
        Schema::create('kontak_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('telepon');
            $table->string('alamat')->nullable();
            $table->timestamps();
        });
        Schema::create('laporan_darurat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('jenis_darurat');
            $table->string('nomor_telepon');
            $table->enum('status', ['aktif', 'ditangani', 'selesai'])->default('aktif');
            $table->timestamps();
        });
        Schema::create('produk_umkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_usaha');
            $table->string('pemilik');
            $table->string('kategori');
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();
            $table->string('foto_produk')->nullable();
            $table->string('kontak');
            $table->timestamps();
        });
        Schema::create('lowongan', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('kontak');
            $table->date('tanggal');
            $table->timestamps();
        });
        Schema::create('usulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->text('isi');
            $table->unsignedInteger('jumlah_vote')->default(0);
            $table->timestamps();
        });
        Schema::create('usulan_vote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_id')->constrained('usulan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['usulan_id', 'user_id']);
            $table->timestamps();
        });
        Schema::create('polling', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('berakhir_pada')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
        Schema::create('polling_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_id')->constrained('polling')->cascadeOnDelete();
            $table->string('opsi');
            $table->timestamps();
        });
        Schema::create('polling_vote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_id')->constrained('polling')->cascadeOnDelete();
            $table->foreignId('polling_option_id')->constrained('polling_option')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['polling_id', 'user_id']);
            $table->timestamps();
        });
        Schema::create('lokasi_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['lokasi_desa', 'polling_vote', 'polling_option', 'polling', 'usulan_vote', 'usulan', 'lowongan', 'produk_umkm', 'laporan_darurat', 'kontak_desa', 'pengumuman', 'apbdes', 'surat_pengajuan'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
