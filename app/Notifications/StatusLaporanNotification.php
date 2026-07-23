<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusLaporanNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Laporan $laporan) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'status_laporan',
            'title' => $this->laporan->status === 'selesai' ? 'Laporan Anda telah selesai' : 'Laporan Anda sedang diproses',
            'message' => 'Status “'.$this->laporan->judul.'” diperbarui menjadi '.ucfirst($this->laporan->status).'.',
            'laporan_id' => $this->laporan->id,
            'status' => $this->laporan->status,
        ];
    }
}
