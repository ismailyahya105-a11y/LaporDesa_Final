<?php

namespace App\Notifications;

use App\Models\Tanggapan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TanggapanBaruNotification extends Notification
{
    use Queueable;

    public function __construct(public Tanggapan $tanggapan) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['type' => 'tanggapan_baru', 'title' => 'Tanggapan baru dari admin', 'message' => 'Laporan “'.$this->tanggapan->laporan->judul.'” mendapat tanggapan baru.', 'laporan_id' => $this->tanggapan->laporan_id];
    }
}
