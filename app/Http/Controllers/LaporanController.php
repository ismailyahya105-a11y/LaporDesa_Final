<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use App\Notifications\LaporanBaruNotification;
use App\Notifications\StatusLaporanNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Laporan::query()->with(['kategori', 'user'])->latest();

        if (! $request->user()->isAdmin()) {
            $query->whereBelongsTo($request->user());
        }

        return view('laporan.index', [
            'laporans' => $query->paginate(10)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        abort_if($request->user()->isAdmin(), 403);

        return view('laporan.create', [
            'kategoris' => Kategori::query()->orderBy('nama')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'isi_laporan' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'menunggu';

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->uploadPhoto($request);
        }

        $laporan = Laporan::create($validated);
        $laporan->load(['user', 'kategori']);
        User::query()->where('role', 'admin')->get()
            ->each->notify(new LaporanBaruNotification($laporan));

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Laporan berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Laporan $laporan): View
    {
        $this->ensureCanView($request, $laporan);
        $laporan->load(['kategori', 'user', 'tanggapan.user']);

        return view('laporan.show', compact('laporan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Laporan $laporan): View
    {
        $this->ensureCanView($request, $laporan);

        return view('laporan.edit', [
            'laporan' => $laporan,
            'kategoris' => $request->user()->isAdmin()
                ? collect()
                : Kategori::query()->orderBy('nama')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->ensureCanView($request, $laporan);

        if (! $request->user()->isAdmin()) {
            $validated = $request->validate([
                'judul' => ['required', 'string', 'max:255'],
                'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
                'isi_laporan' => ['required', 'string'],
                'foto' => ['nullable', 'image', 'max:2048'],
                'hapus_foto' => ['nullable', 'boolean'],
            ]);

            if ($request->hasFile('foto')) {
                $this->deletePhoto($laporan->foto);
                $validated['foto'] = $this->uploadPhoto($request);
            } elseif ($request->boolean('hapus_foto') && $laporan->foto) {
                $this->deletePhoto($laporan->foto);
                $validated['foto'] = null;
            }

            unset($validated['hapus_foto']);
            $laporan->update($validated);

            return redirect()->route('laporan.show', $laporan)
                ->with('success', 'Laporan berhasil diperbarui.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'selesai'])],
        ]);

        $oldStatus = $laporan->status;
        $laporan->update($validated);

        if ($oldStatus !== $laporan->status && in_array($laporan->status, ['diproses', 'selesai'], true)) {
            $laporan->user->notify(new StatusLaporanNotification($laporan));
        }

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Status laporan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->ensureCanView($request, $laporan);

        $this->deletePhoto($laporan->foto);

        $laporan->delete();

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    private function ensureCanView(Request $request, Laporan $laporan): void
    {
        abort_unless(
            $request->user()->isAdmin() || $laporan->user_id === $request->user()->id,
            403
        );
    }

    /** Store a photo in public/images/laporan and return its public URL path. */
    private function uploadPhoto(Request $request): string
    {
        $file = $request->file('foto');
        $directory = public_path('images/laporan');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $namaFile = 'laporan_'.bin2hex(random_bytes(16)).'.'.strtolower($file->getClientOriginalExtension());
        $file->move($directory, $namaFile);

        return 'images/laporan/'.$namaFile;
    }

    /** Delete only files that belong to public/images/laporan. */
    private function deletePhoto(?string $foto): void
    {
        if (! $foto || ! str_starts_with($foto, 'images/laporan/')) {
            return;
        }

        $path = public_path($foto);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
