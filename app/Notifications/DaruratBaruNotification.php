<?php

namespace App\Notifications;

use App\Models\LaporanDarurat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DaruratBaruNotification extends Notification
{
    use Queueable;

    public function __construct(public LaporanDarurat $darurat) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['type' => 'darurat_baru', 'title' => 'SOS darurat masuk', 'message' => $this->darurat->user->name.' membutuhkan bantuan: '.$this->darurat->jenis_darurat.'.', 'darurat_id' => $this->darurat->id];
    }
}
