<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Notifications\TanggapanBaruNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TanggapanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'laporan_id' => ['required', 'integer', 'exists:laporans,id'],
            'isi_tanggapan' => ['required', 'string'],
        ]);

        $laporan = Laporan::findOrFail($validated['laporan_id']);
        $tanggapan = $laporan->tanggapan()->create([
            'user_id' => $request->user()->id,
            'isi' => $validated['isi_tanggapan'],
        ]);
        $tanggapan->load('laporan');
        $laporan->user->notify(new TanggapanBaruNotification($tanggapan));

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Tanggapan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
