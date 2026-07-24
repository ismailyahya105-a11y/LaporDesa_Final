<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    private const DEFAULTS = [
        ['nama' => 'Infrastruktur', 'deskripsi' => 'Jalan, jembatan, drainase, dan infrastruktur desa.'],
        ['nama' => 'Fasilitas Umum', 'deskripsi' => 'Kerusakan fasilitas umum desa.'],
        ['nama' => 'Keamanan', 'deskripsi' => 'Gangguan ketertiban dan keamanan desa.'],
        ['nama' => 'Lingkungan', 'deskripsi' => 'Sampah, pencemaran, dan kondisi lingkungan.'],
    ];

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    /** Ensure a new deployment always has categories available for reporting. */
    public static function ensureDefaults(): void
    {
        if (static::query()->exists()) {
            return;
        }

        foreach (self::DEFAULTS as $kategori) {
            static::query()->firstOrCreate(['nama' => $kategori['nama']], $kategori);
        }
    }
}
