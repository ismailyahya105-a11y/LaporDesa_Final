<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()->notifications()->findOrFail($notification);
        $item->markAsRead();

        return match ($item->data['type'] ?? null) {
            'laporan_baru', 'status_laporan', 'tanggapan_baru' => redirect()->route('laporan.show', $item->data['laporan_id']),
            'status_surat' => redirect()->route('surat.index'),
            'darurat_baru' => redirect()->route('admin.smart'),
            default => redirect()->route($request->user()->isAdmin() ? 'admin.dashboard' : 'dashboard'),
        };
    }
}
