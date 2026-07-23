<?php

namespace App\Notifications;

use App\Models\SuratPengajuan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SuratStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public SuratPengajuan $surat) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['type' => 'status_surat', 'title' => 'Status surat diperbarui', 'message' => 'Pengajuan '.strtoupper($this->surat->jenis_surat).' kini '.str_replace('_', ' ', $this->surat->status).'.', 'surat_id' => $this->surat->id, 'status' => $this->surat->status];
    }
}
