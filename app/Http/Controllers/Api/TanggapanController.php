<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LaporanResource;
use App\Models\Laporan;
use App\Notifications\TanggapanBaruNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TanggapanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'laporan_id' => ['required', 'integer', 'exists:laporans,id'],
            'isi' => ['required', 'string'],
        ]);

        $laporan = Laporan::findOrFail($validated['laporan_id']);
        $tanggapan = $laporan->tanggapan()->create([
            'user_id' => $request->user()->id,
            'isi' => $validated['isi'],
        ]);
        $tanggapan->load('laporan');
        $laporan->user->notify(new TanggapanBaruNotification($tanggapan));

        return response()->json([
            'success' => true,
            'message' => 'Tanggapan berhasil dikirim.',
            'data' => LaporanResource::make($laporan->load(['user', 'kategori', 'tanggapan.user']))->resolve(),
        ], 201);
    }
}
