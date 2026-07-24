<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LaporanResource;
use App\Models\Laporan;
use App\Models\User;
use App\Notifications\LaporanBaruNotification;
use App\Notifications\StatusLaporanNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Laporan::query()->with(['user', 'kategori', 'tanggapan.user'])->latest();

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json([
            'success' => true,
            'data' => LaporanResource::collection($query->paginate(10))->response()->getData(true),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_if($request->user()->isAdmin(), 403, 'Admin tidak dapat membuat laporan.');

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'isi_laporan' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->uploadPhoto($request);
        }

        $laporan = $request->user()->laporan()->create([
            ...$validated,
            'status' => 'menunggu',
        ])->load(['user', 'kategori', 'tanggapan.user']);

        User::query()->where('role', 'admin')->get()
            ->each->notify(new LaporanBaruNotification($laporan));

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim.',
            'data' => LaporanResource::make($laporan)->resolve(),
        ], 201);
    }

    public function show(Request $request, Laporan $laporan): JsonResponse
    {
        abort_unless(
            $request->user()->isAdmin() || $laporan->user_id === $request->user()->id,
            403
        );

        return response()->json([
            'success' => true,
            'data' => LaporanResource::make($laporan->load(['user', 'kategori', 'tanggapan.user']))->resolve(),
        ]);
    }

    public function updateStatus(Request $request, Laporan $laporan): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'selesai'])],
        ]);

        $changed = $laporan->status !== $validated['status'];
        $laporan->update($validated);

        if ($changed && in_array($laporan->status, ['diproses', 'selesai'], true)) {
            $laporan->user->notify(new StatusLaporanNotification($laporan));
        }

        return response()->json([
            'success' => true,
            'message' => 'Status laporan berhasil diperbarui.',
            'data' => LaporanResource::make($laporan->load(['user', 'kategori', 'tanggapan.user']))->resolve(),
        ]);
    }

    private function uploadPhoto(Request $request): string
    {
        return $request->file('foto')->store('laporan', 'public');
    }
}
