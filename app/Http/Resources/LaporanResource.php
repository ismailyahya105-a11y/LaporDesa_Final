<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'judul' => $this->judul,
            'kategori' => $this->whenLoaded('kategori', fn () => [
                'id' => $this->kategori->id,
                'nama' => $this->kategori->nama,
                'deskripsi' => $this->kategori->deskripsi,
            ]),
            'isi_laporan' => $this->isi_laporan,
            'foto' => $this->foto,
            'foto_url' => $this->foto ? url($this->foto) : null,
            'status' => $this->status,
            'tanggal' => $this->created_at?->toISOString(),
            'pelapor' => UserResource::make($this->whenLoaded('user')),
            'tanggapan' => $this->whenLoaded('tanggapan', fn () => $this->tanggapan->map(fn ($item) => [
                'id' => $item->id,
                'isi' => $item->isi,
                'tanggal' => $item->created_at?->toISOString(),
                'admin' => UserResource::make($item->whenLoaded('user'))->resolve(),
            ])->values()),
        ];
    }
}
