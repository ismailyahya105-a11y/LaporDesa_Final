<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuan;
use App\Notifications\SuratStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuratPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $items = SuratPengajuan::with('user')->when(! $request->user()->isAdmin(), fn ($q) => $q->whereBelongsTo($request->user()))->latest()->paginate(10);

        return view('smart.surat', compact('items'));
    }

    public function store(Request $request)
    {
        abort_if($request->user()->isAdmin(), 403);
        $data = $request->validate(['jenis_surat' => ['required', Rule::in(['sku', 'sktm', 'pengantar_rt_rw', 'pembaruan_kk'])], 'keperluan' => 'required|string|max:2000', 'nomor_telepon' => 'required|string|max:30', 'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096']);
        $path = $request->file('dokumen')?->store('surat', 'public');
        $request->user()->suratPengajuan()->create(['jenis_surat' => $data['jenis_surat'], 'data_pengajuan' => ['keperluan' => $data['keperluan'], 'nomor_telepon' => $data['nomor_telepon']], 'dokumen' => $path]);

        return back()->with('success', 'Pengajuan surat berhasil dikirim.');
    }

    public function update(Request $request, SuratPengajuan $surat)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['status' => ['required', Rule::in(['diajukan', 'diproses', 'verifikasi', 'disetujui', 'siap_diambil', 'selesai', 'ditolak'])], 'catatan' => 'nullable|string|max:2000']);
        $changed = $surat->status !== $data['status'];
        $surat->update($data);
        if ($changed) {
            $surat->user->notify(new SuratStatusNotification($surat));
        }

return back()->with('success', 'Status surat diperbarui.');
    }
}
