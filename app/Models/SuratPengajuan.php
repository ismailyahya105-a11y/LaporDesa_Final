<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPengajuan extends Model
{
    protected $table = 'surat_pengajuan';

    protected $fillable = ['user_id', 'jenis_surat', 'data_pengajuan', 'dokumen', 'status', 'catatan'];

    protected function casts(): array
    {
        return ['data_pengajuan' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
