<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $destination = public_path('images/laporan');
        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        foreach ([public_path('storage/laporan'), storage_path('app/public/laporan')] as $source) {
            if (! is_dir($source)) {
                continue;
            }

            foreach (glob($source.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
                if (! is_file($file)) {
                    continue;
                }

                $target = $destination.DIRECTORY_SEPARATOR.basename($file);
                if (! file_exists($target)) {
                    copy($file, $target);
                }
            }
        }

        DB::table('laporans')
            ->where('foto', 'like', 'storage/laporan/%')
            ->update(['foto' => DB::raw("REPLACE(foto, 'storage/laporan/', 'images/laporan/')")]);

        DB::table('laporans')
            ->where('foto', 'like', 'laporan/%')
            ->update(['foto' => DB::raw("REPLACE(foto, 'laporan/', 'images/laporan/')")]);
    }

    public function down(): void
    {
        DB::table('laporans')
            ->where('foto', 'like', 'images/laporan/%')
            ->update(['foto' => DB::raw("REPLACE(foto, 'images/laporan/', 'storage/laporan/')")]);
    }
};
