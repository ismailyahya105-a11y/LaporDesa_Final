<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LaporanBaruNotification extends Notification
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
            'type' => 'laporan_baru',
            'title' => 'Laporan baru masuk',
            'message' => $this->laporan->user->name.' melaporkan “'.$this->laporan->judul.'”',
            'laporan_id' => $this->laporan->id,
            'pelapor' => $this->laporan->user->name,
            'judul' => $this->laporan->judul,
            'kategori' => $this->laporan->kategori->nama,
        ];
    }
}
