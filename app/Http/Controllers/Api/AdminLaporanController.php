<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Notifications\StatusLaporanNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminLaporanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Laporan::with(['user', 'kategori', 'tanggapan.user'])->latest()->paginate(15),
        ]);
    }

    public function updateStatus(Request $request, Laporan $laporan): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'selesai'])],
        ]);
        $changed = $laporan->status !== $validated['status'];
        $laporan->update($validated);
        if ($changed && in_array($laporan->status, ['diproses', 'selesai'], true)) {
            $laporan->user->notify(new StatusLaporanNotification($laporan));
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.', 'data' => $laporan]);
    }
}
