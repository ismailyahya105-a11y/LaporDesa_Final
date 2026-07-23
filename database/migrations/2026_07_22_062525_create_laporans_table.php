<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('kategori_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('judul');

            $table->text('isi_laporan');

            $table->string('foto')
                ->nullable();

            $table->enum('status', [
                'menunggu',
                'diproses',
                'selesai',
            ])
                ->default('menunggu');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
